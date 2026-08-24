<?php
require_once 'config/database.php';

try {
    // Verificar si la columna existe (MariaDB anterior a 10.6.0 no soporta ADD COLUMN IF NOT EXISTS)
    $stmt = $pdo->query("SHOW COLUMNS FROM usuarios LIKE 'permisos'");
    if ($stmt->rowCount() == 0) {
        $pdo->query("ALTER TABLE usuarios ADD COLUMN permisos TEXT");
    }
    
    // 2. Darle todos los permisos por defecto al usuario 'admin' (SuperAdmin)
    $superadmin_permisos = json_encode([
        'noticias' => true,
        'noticias_borrar' => true,
        'finanzas' => true,
        'usuarios' => true
    ]);
    
    $stmt = $pdo->prepare("UPDATE usuarios SET permisos = ? WHERE username = 'admin' OR id = 1");
    $stmt->execute([$superadmin_permisos]);
    
    echo "<h1>¡Base de Datos Actualizada!</h1>";
    echo "<p>Se añadió exitosamente el sistema de permisos a tu servidor Hostinger.</p>";
    echo "<p>Tus cuentas de administración principales ahora tienen todos los accesos.</p>";
    echo "<a href='index.php'>Volver al panel</a>";
    echo "<p style='color:red;'>NOTA: Por seguridad, borra este archivo (update_db.php) de Hostinger cuando termines.</p>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
