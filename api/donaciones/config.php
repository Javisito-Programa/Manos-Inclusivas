<?php
// Evitar acceso directo
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

function loadEnv($path) {
    if(!file_exists($path)) {
        return false;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    return true;
}

// Intentar cargar .env en el directorio actual, luego uno más arriba, etc.
$env_paths = [
    __DIR__ . '/.env',
    dirname(__DIR__) . '/.env',
    dirname(dirname(__DIR__)) . '/.env'
];

foreach ($env_paths as $path) {
    if (loadEnv($path)) {
        break;
    }
}

// Configuración OpenPay
define('OPENPAY_MERCHANT_ID', getenv('OPENPAY_MERCHANT_ID') ?: 'tu_merchant_id_aqui');
define('OPENPAY_PUBLIC_KEY', getenv('OPENPAY_PUBLIC_KEY') ?: 'tu_public_key_aqui');
define('OPENPAY_PRIVATE_KEY', getenv('OPENPAY_PRIVATE_KEY') ?: 'tu_private_key_aqui');
define('OPENPAY_PRODUCTION_MODE', filter_var(getenv('OPENPAY_PRODUCTION_MODE'), FILTER_VALIDATE_BOOLEAN));

function getOpenpayBaseUrl() {
    return OPENPAY_PRODUCTION_MODE ? 'https://api.openpay.mx/v1/' : 'https://sandbox-api.openpay.mx/v1/';
}

// Conexión a Base de Datos (usamos la principal si existe)
$db_path = dirname(dirname(__DIR__)) . '/admin/config/database.php';
if (file_exists($db_path)) {
    require_once $db_path;
} else {
    // Fallback manual si no se encuentra
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'fundacion_db';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') ?: '';
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die(json_encode(['error' => 'Database connection failed']));
    }
}
?>
