<?php
// Configuración de la base de datos
if (file_exists(__DIR__ . '/db_secrets.php')) {
    // En Hostinger, leeremos las credenciales de este archivo secreto
    require_once __DIR__ . '/db_secrets.php';
} else {
    // Credenciales para entorno local (XAMPP/WAMP)
    $host = 'localhost';
    $dbname = 'fundacion_db';
    $username = 'root';
    $password = '';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Habilitar excepciones para errores de PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch objects por defecto
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Si la base de datos no existe (por ejemplo, en desarrollo local antes de crearla), 
    // no mostramos el error feo, sino que permitimos que la app maneje la advertencia.
    // die("ERROR: No se pudo conectar a la base de datos. " . $e->getMessage());
}
?>
