<?php
// Configuración de la base de datos
// Rutas posibles para el archivo secreto
$secret_path_1 = __DIR__ . '/db_secrets.php';
$secret_path_2 = $_SERVER['DOCUMENT_ROOT'] . '/../db_secrets.php';

if (file_exists($secret_path_2)) {
    // Buscar un nivel arriba de public_html (Muy seguro y no se borra con Git)
    require_once $secret_path_2;
} elseif (file_exists($secret_path_1)) {
    // Buscar en la misma carpeta
    require_once $secret_path_1;
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
