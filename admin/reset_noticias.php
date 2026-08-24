<?php
require_once 'config/database.php';

try {
    // ESTO BORRARÁ TODAS LAS NOTICIAS Y REINICIARÁ EL ID A 1
    $pdo->query("TRUNCATE TABLE noticias");
    
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h1 style='color: #E53E3E;'>¡Base de Datos Reiniciada!</h1>";
    echo "<p>Se han borrado TODAS las noticias y el contador de ID ha vuelto a 1.</p>";
    echo "<a href='noticias.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #6c5ce7; color: white; text-decoration: none; border-radius: 5px;'>Volver al Panel</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
