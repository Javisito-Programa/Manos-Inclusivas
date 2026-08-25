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
        $estado_html = "¡Donativo Exitoso!";
        $mensaje_html = "Gracias por tu apoyo. Tu donativo ha sido procesado correctamente.";
        
        // Actualizar en base de datos
        if (isset($pdo)) {
            $stmt = $pdo->prepare("UPDATE transacciones SET estatus_pago = 'Completado', numero_autorizacion = ? WHERE transaccion_id_openpay = ?");
            $stmt->execute([$transaction['authorization'] ?? '', $transaction_id]);
        }
    } else {
        $estado_html = "Pago Declinado / Pendiente";
        $mensaje_html = "Tu banco ha declinado la transacción o está pendiente de revisión. " . ($transaction['error_message'] ?? '');
    }
} else {
    $estado_html = "Error";
    $mensaje_html = "No se pudo verificar el estado de la transacción.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Donación - Manos Inclusivas</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background: #f9f9f9; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
        h1 { color: #8e75d8; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #8e75d8; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="card">
        <h1><?= $estado_html ?></h1>
        <p><?= $mensaje_html ?></p>
        <a href="../../donar.html" class="btn">Volver al sitio</a>
    </div>
</body>
</html>
