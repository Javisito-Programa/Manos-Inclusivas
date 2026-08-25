<?php
require_once 'config.php';

header('Content-Type: application/json');

// Recibir JSON payload
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['token_id']) || !isset($data['device_session_id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos o faltantes.']);
    exit;
}

$token_id = $data['token_id'];
$device_session_id = $data['device_session_id'];
$amount = floatval($data['amount']);
$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$name = htmlspecialchars(trim($data['name']));
$is_recurring = isset($data['is_recurring']) ? filter_var($data['is_recurring'], FILTER_VALIDATE_BOOLEAN) : false;
$require_cfdi = isset($data['require_cfdi']) ? filter_var($data['require_cfdi'], FILTER_VALIDATE_BOOLEAN) : false;
$billing_data = $data['billing_data'] ?? null;

if ($amount < 50) {
    echo json_encode(['success' => false, 'message' => 'El monto mínimo de donación es de $50 MXN.']);
    exit;
}
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Correo electrónico inválido.']);
    exit;
}

// Inicializar cURL
function openpay_request($endpoint, $method = 'POST', $payload = null) {
    $url = getOpenpayBaseUrl() . OPENPAY_MERCHANT_ID . $endpoint;
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, OPENPAY_PRIVATE_KEY . ":"); // Basic auth
    
    $headers = ['Content-Type: application/json'];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($payload) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['status' => $httpCode, 'data' => json_decode($response, true)];
}

$response_data = null;
$redirect_url = null;
$transaccion_id_openpay = null;
$numero_autorizacion = null;
$estado = 'Pendiente';

// Obtener host para la url de redirección
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$base_uri = $protocol . $host . dirname(dirname($_SERVER['REQUEST_URI']));
$redirect_3d_url = $base_uri . '/donaciones/retorno.php';

if (!$is_recurring) {
    // ---- DONACIÓN ÚNICA ----
    $payload = [
        'method' => 'card',
        'source_id' => $token_id,
        'amount' => $amount,
        'currency' => 'MXN',
        'description' => 'Donativo a Manos Inclusivas A.C.',
        'device_session_id' => $device_session_id,
        'use_3d_secure' => true,
        'redirect_url' => $redirect_3d_url,
        'customer' => [
            'name' => $name,
            'email' => $email
        ]
    ];
    
    $res = openpay_request('/charges', 'POST', $payload);
    
    if ($res['status'] == 200 && isset($res['data']['id'])) {
        $transaccion_id_openpay = $res['data']['id'];
        if (isset($res['data']['payment_method']['url'])) {
            $redirect_url = $res['data']['payment_method']['url'];
        } else {
            $estado = ($res['data']['status'] == 'completed') ? 'Completado' : 'Pendiente';
            $numero_autorizacion = $res['data']['authorization'] ?? null;
        }
        $response_data = ['success' => true, 'redirect_url' => $redirect_url];
    } else {
        echo json_encode(['success' => false, 'message' => $res['data']['description'] ?? 'Error procesando el pago.']);
        exit;
    }
} else {
    // ---- DONACIÓN RECURRENTE (SUSCRIPCIÓN) ----
    
    // 1. Crear Customer
    $customer_payload = [
        'name' => $name,
        'email' => $email,
        'requires_account' => false
    ];
    $cust_res = openpay_request('/customers', 'POST', $customer_payload);
    if ($cust_res['status'] != 200 && $cust_res['status'] != 201) {
        echo json_encode(['success' => false, 'message' => 'Error creando cliente para suscripción.']);
        exit;
    }
    $customer_id = $cust_res['data']['id'];
    
    // 2. Asociar Tarjeta al Customer
    $card_payload = [
        'token_id' => $token_id,
        'device_session_id' => $device_session_id
    ];
    $card_res = openpay_request("/customers/{$customer_id}/cards", 'POST', $card_payload);
    if ($card_res['status'] != 200 && $card_res['status'] != 201) {
        echo json_encode(['success' => false, 'message' => 'Error asociando la tarjeta. ' . ($card_res['data']['description'] ?? '')]);
        exit;
    }
    
    // 3. Crear Plan Dinámico
    $plan_id = 'plan_mensual_' . (int)$amount;
    $plan_payload = [
        'amount' => $amount,
        'status_after_retry' => 'cancelled',
        'retry_times' => 3,
        'name' => 'Donación Mensual ' . $amount . ' MXN',
        'repeat_unit' => 'month',
        'trial_days' => 0,
        'repeat_every' => 1,
        'currency' => 'MXN'
    ];
    // Intentamos crear el plan, si ya existe dará error 409, lo cual está bien, lo reutilizamos
    openpay_request('/plans', 'POST', $plan_payload);
    
    // 4. Suscribir Customer al Plan
    $sub_payload = [
        'plan_id' => $plan_id,
        'card_id' => $card_res['data']['id']
    ];
    $sub_res = openpay_request("/customers/{$customer_id}/subscriptions", 'POST', $sub_payload);
    if ($sub_res['status'] == 200 || $sub_res['status'] == 201) {
        $transaccion_id_openpay = $sub_res['data']['id']; // ID de suscripción
        $estado = 'Suscripcion_Activa';
        $response_data = ['success' => true];
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creando la suscripción mensual.']);
        exit;
    }
}

// ---- REGISTRO CONTABLE EN BASE DE DATOS ----
// Crear ID UUID
$id_donacion = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

$comision_pasarela = $amount * 0.029 + 2.50; // Comisión base de Openpay aprox
$monto_neto = $amount - $comision_pasarela;
$tipo_donacion = $is_recurring ? 'Recurrente' : 'Unica';
$datos_fiscales_json = $require_cfdi && $billing_data ? json_encode($billing_data) : null;
$estatus_cfdi = $require_cfdi ? 'Pendiente' : null;
$datos_donante = json_encode(['name' => $name, 'email' => $email]);

try {
    $stmt = $pdo->prepare("INSERT INTO transacciones (
        id_donacion, transaccion_id_openpay, numero_autorizacion, monto_bruto, comision_pasarela, monto_neto, moneda, metodo_pago_marca, ultimos_4_digitos_tarjeta, tipo_donacion, estatus_pago, datos_donante, requiere_factura, datos_fiscales_json, estatus_cfdi
    ) VALUES (
        ?, ?, ?, ?, ?, ?, 'MXN', 'Openpay', '****', ?, ?, ?, ?, ?, ?
    )");
    
    $stmt->execute([
        $id_donacion,
        $transaccion_id_openpay,
        $numero_autorizacion,
        $amount,
        $comision_pasarela,
        $monto_neto,
        $tipo_donacion,
        $estado,
        $datos_donante,
        $require_cfdi ? 1 : 0,
        $datos_fiscales_json,
        $estatus_cfdi
    ]);
} catch (PDOException $e) {
    // Si falla la DB no mostramos error al donante si ya pagó, pero se registra
    error_log("DB Insert error: " . $e->getMessage());
}

echo json_encode($response_data);
