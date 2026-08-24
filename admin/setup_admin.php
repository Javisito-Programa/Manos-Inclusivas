<?php
require_once 'config/database.php';

try {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    
    // Si ya existe el admin, lo actualizamos. Si no, lo creamos.
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = 'admin'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $pdo->query("UPDATE usuarios SET password_hash = '$hash' WHERE username = 'admin'");
        echo "<h1>¡Éxito!</h1><p>El usuario 'admin' ya existía, así que su contraseña fue reseteada en la base de datos de Hostinger.</p>";
    } else {
        $pdo->query("INSERT INTO usuarios (username, password_hash, role) VALUES ('admin', '$hash', 'admin')");
        echo "<h1>¡Éxito!</h1><p>El usuario 'admin' fue creado desde cero en tu base de datos de Hostinger.</p>";
    }
    
    echo "<h3>Ahora puedes iniciar sesión con:</h3>";
    echo "<ul><li><b>Usuario:</b> admin</li><li><b>Contraseña:</b> admin123</li></ul>";
    echo "<p><a href='index.php'>Clic aquí para ir al login</a></p>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
