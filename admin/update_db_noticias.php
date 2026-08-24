<?php
require_once 'config/database.php';

try {
    // 1. Agregar columna fecha_publicacion
    $stmt = $pdo->query("SHOW COLUMNS FROM noticias LIKE 'fecha_publicacion'");
    if ($stmt->rowCount() == 0) {
        $pdo->query("ALTER TABLE noticias ADD COLUMN fecha_publicacion DATE");
    }
    
    // 2. Agregar columna is_pinned
    $stmt = $pdo->query("SHOW COLUMNS FROM noticias LIKE 'is_pinned'");
    if ($stmt->rowCount() == 0) {
        $pdo->query("ALTER TABLE noticias ADD COLUMN is_pinned TINYINT(1) DEFAULT 0");
    }
    
    // 3. Poblar fecha_publicacion con las fechas existentes (solo si están nulas)
    $pdo->query("UPDATE noticias SET fecha_publicacion = DATE(created_at) WHERE fecha_publicacion IS NULL");
    
    echo "<h1>¡Base de Datos Actualizada!</h1>";
    echo "<p>Se agregaron las columnas para fijar noticias y fechas personalizadas.</p>";
    echo "<a href='index.php'>Volver al panel</a>";
    echo "<p style='color:red;'>NOTA: Por seguridad, borra este archivo (update_db_noticias.php) de Hostinger cuando termines.</p>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
