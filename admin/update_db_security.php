<?php
require_once 'config/database.php';

try {
    // Crear tabla para registrar intentos de inicio de sesión
    $pdo->query("CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL UNIQUE,
        attempts INT DEFAULT 1,
        last_attempt DATETIME
    )");
    
    echo "<h1>¡Seguridad Actualizada!</h1>";
    echo "<p>Se ha implementado el bloqueo contra ataques de fuerza bruta.</p>";
    echo "<a href='index.php'>Volver al inicio de sesión</a>";
    echo "<p style='color:red;'>NOTA: Borra este archivo (update_db_security.php) después de usarlo.</p>";
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
