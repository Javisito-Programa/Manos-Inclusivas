<?php
require_once 'config.php';

$transaction_id = $_GET['id'] ?? null;

if (!$transaction_id) {
    die("ID de transacción no proporcionado.");
}

// Consultar estado en OpenPay
function check_openpay_transaction($id) {
    $url = getOpenpayBaseUrl() . OPENPAY_MERCHANT_ID . '/charges/' . $id;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, OPENPAY_PRIVATE_KEY . ":");
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$transaction = check_openpay_transaction($transaction_id);

$estado_html = "Procesando...";
$mensaje_html = "";

if (isset($transaction['status'])) {
    if ($transaction['status'] == 'completed') {
        // Actualizar en base de datos
        if (isset($pdo)) {
            $stmt = $pdo->prepare("UPDATE transacciones SET estatus_pago = 'Completado', numero_autorizacion = ? WHERE transaccion_id_openpay = ?");
            $stmt->execute([$transaction['authorization'] ?? '', $transaction_id]);
        }
        
        // Preparar parámetros para recibo
        $amount = $transaction['amount'] ?? 0;
        $auth = $transaction['authorization'] ?? 'Procesado';
        $name = $transaction['customer']['name'] ?? '';
        $email = $transaction['customer']['email'] ?? '';
        
        $queryParams = http_build_query([
            'amount' => $amount,
            'auth' => $auth,
            'name' => $name,
            'email' => $email
        ]);
        
        header("Location: ../../donacion-exitosa.html?" . $queryParams);
        exit;
    } else {
        // Pago declinado o fallido
        header("Location: ../../donar.html?error=declined");
        exit;
    }
} else {
    // Error de verificación
    header("Location: ../../donar.html?error=verification_failed");
    exit;
}
