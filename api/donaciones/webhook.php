<?php
require_once 'config.php';

// Validar que la petición venga de OpenPay
// Para mayor seguridad en prod, se valida con Basic Auth de Openpay, 
// verificando que el user sea tu merchant_id o private key.
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== OPENPAY_PRIVATE_KEY) {
    header('HTTP/1.0 401 Unauthorized');
    echo 'Unauthorized';
    exit;
}

$input = file_get_contents('php://input');
$event = json_decode($input, true);

if (!$event || !isset($event['type']) || !isset($event['transaction'])) {
    http_response_code(400);
    echo 'Bad Request';
    exit;
}

$type = $event['type'];
$transaction = $event['transaction'];
$openpay_id = $transaction['id'];

// Estado a mapear
$nuevo_estado = null;
$numero_autorizacion = $transaction['authorization'] ?? null;

switch ($type) {
    case 'charge.succeeded':
        $nuevo_estado = 'Completado';
        break;
    case 'charge.failed':
    case 'charge.cancelled':
        $nuevo_estado = 'Fallido';
        break;
    case 'charge.refunded':
        $nuevo_estado = 'Reembolsado';
        break;
    case 'subscription.charge.succeeded':
        // Es un cargo de suscripción. Podría ser el primero o los subsecuentes.
        // Si no existe, deberíamos crearlo. Para simplificar, asumimos que si el ID existe, lo actualizamos.
        $nuevo_estado = 'Completado';
        // Aquí podríamos insertar un nuevo registro si es un mes nuevo de una suscripción activa
        break;
}

if ($nuevo_estado && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("UPDATE transacciones SET estatus_pago = ?, numero_autorizacion = IFNULL(?, numero_autorizacion) WHERE transaccion_id_openpay = ?");
        $stmt->execute([$nuevo_estado, $numero_autorizacion, $openpay_id]);
    } catch (PDOException $e) {
        error_log("Webhook DB error: " . $e->getMessage());
    }
}

http_response_code(200);
echo 'OK';
