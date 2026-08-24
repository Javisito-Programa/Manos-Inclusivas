<?php
require_once 'config/database.php';

try {
    // 1. Agregar nuevas columnas a la tabla noticias
    $queries = [
        "ALTER TABLE noticias ADD COLUMN imagenes_extra TEXT DEFAULT NULL",
        "ALTER TABLE noticias ADD COLUMN enlace_facebook VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE noticias ADD COLUMN enlace_instagram VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE noticias ADD COLUMN enlace_twitter VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE noticias ADD COLUMN enlace_tiktok VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE noticias ADD COLUMN enlace_youtube VARCHAR(255) DEFAULT NULL"
    ];

    foreach ($queries as $query) {
        try {
            $pdo->query($query);
        } catch (PDOException $e) {
            // Ignoramos el error si la columna ya existe
            if (strpos($e->getMessage(), 'Duplicate column name') === false && strpos($e->getMessage(), '1060') === false) {
                throw $e;
            }
        }
    }

    // 2. Reiniciar el AUTO_INCREMENT (esto solo afectará si la tabla está vacía, o lo pondrá al siguiente ID disponible)
    $pdo->query("ALTER TABLE noticias AUTO_INCREMENT = 1");

    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h1 style='color: #4CAF50;'>¡Base de Datos Actualizada Exitosamente!</h1>";
    echo "<p>Se agregaron las columnas para redes sociales y carrusel de imágenes.</p>";
    echo "<p>El contador de ID ha sido reiniciado a 1 (o al siguiente disponible).</p>";
    echo "<a href='noticias.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #6c5ce7; color: white; text-decoration: none; border-radius: 5px;'>Volver al Panel</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
