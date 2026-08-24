<?php
// Configuración de la base de datos
// Rutas posibles para el archivo secreto
$secret_path_1 = __DIR__ . '/db_secrets.php';
$secret_path_2 = $_SERVER['DOCUMENT_ROOT'] . '/../db_secrets.php';

try {
    if (file_exists($secret_path_2)) {
        require_once $secret_path_2;
    } elseif (file_exists($secret_path_1)) {
        require_once $secret_path_1;
    } else {
        // Credenciales para entorno local (XAMPP/WAMP)
        $host = 'localhost';
        $dbname = 'fundacion_db';
        $username = 'root';
        $password = '';
    }
} catch (Throwable $t) {
    die("<div style='background:red;color:white;padding:20px;font-family:sans-serif;'>
        <h2>🚨 Error Crítico en tu archivo db_secrets.php</h2>
        <p>Hostinger dice: <b>" . $t->getMessage() . "</b></p>
        <p>Revisa que tenga &lt;?php al inicio y que no te falte ninguna comilla o punto y coma.</p>
        </div>");
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
