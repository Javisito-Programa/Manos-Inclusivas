<?php
// Configuración estricta de seguridad para la sesión
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

// Parámetros de la cookie de sesión
$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => 0, // 0 significa que la sesión se borra al cerrar el navegador
    'path' => $cookieParams["path"],
    'domain' => $cookieParams["domain"],
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Solo por HTTPS
    'httponly' => true, // Inaccesible para JavaScript (Previene XSS)
    'samesite' => 'Strict' // Previene CSRF
]);

session_start();

// Tiempo de inactividad (timeout) en segundos: 30 minutos (1800 segundos)
$timeout_duration = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Si el usuario estuvo inactivo, cerrar sesión automáticamente
    session_unset();
    session_destroy();
    
    // Si no estamos en el login, redirigir
    if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
        header("Location: index.php?error=" . urlencode("Tu sesión expiró por inactividad."));
        exit();
    }
}

// Actualizar el "timer" de actividad en cada clic o recarga
$_SESSION['LAST_ACTIVITY'] = time();

// Prevenir el secuestro de sesión (Session Hijacking) regenerando el ID
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

// Javascript protector: Forza el cierre de sesión si se cerró la pestaña
if (basename($_SERVER['PHP_SELF']) !== 'index.php' && basename($_SERVER['PHP_SELF']) !== 'logout.php') {
    echo "<script>
    if (typeof sessionStorage !== 'undefined') {
        if (!sessionStorage.getItem('admin_session_active')) {
            window.location.href = 'logout.php?error=' + encodeURIComponent('Sesión cerrada por seguridad al cerrar pestaña.');
        }
    }
    </script>";
}
?>
