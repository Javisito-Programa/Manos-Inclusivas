<?php
/**
 * admin/index.php
 * Panel de inicio de sesión de administradores.
 * Implementa protección contra fuerza bruta y registro de auditoría (BBVA / PCI DSS compliant).
 */
require_once 'config/session.php';
require_once 'config/database.php';

// Si ya está logueado, redirigir a noticias
if(isset($_SESSION['admin_id'])) {
    echo "<script>sessionStorage.setItem('admin_session_active', '1'); window.location.href='noticias.php';</script>";
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitización de entradas
    $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (!empty($username) && !empty($password)) {
        if (isset($pdo)) {
            date_default_timezone_set('America/Mexico_City');
            
            // 1. Protección contra Fuerza Bruta
            // Verificar intentos fallidos previos desde la misma IP
            $stmt_check = $pdo->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
            $stmt_check->execute([$ip_address]);
            $attempt_data = $stmt_check->fetch();
            
            $is_blocked = false;
            if ($attempt_data) {
                $last_time = strtotime($attempt_data['last_attempt']);
                $current_time = time();
                $diff_minutes = round(abs($current_time - $last_time) / 60, 2);
                
                // Bloqueo de 15 minutos tras 5 intentos fallidos
                if ($attempt_data['attempts'] >= 5 && $diff_minutes < 15) {
                    $is_blocked = true;
                    $minutos_restantes = ceil(15 - $diff_minutes);
                    $error = "Demasiados intentos fallidos. Por seguridad, intenta de nuevo en $minutos_restantes minutos.";
                } elseif ($diff_minutes >= 15) {
                    // Resetear intentos si ya pasó el tiempo de castigo
                    $pdo->prepare("UPDATE login_attempts SET attempts = 0 WHERE ip_address = ?")->execute([$ip_address]);
                }
            }

            if (!$is_blocked) {
                // 2. Validación de Credenciales (Uso de Sentencias Preparadas contra SQLi)
                $stmt = $pdo->prepare("SELECT id, username, password_hash, role, permisos FROM usuarios WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                // 3. Verificación Segura (BCRYPT / ARGON2I) 
                if ($user && password_verify($password, $user['password_hash'])) {
                    // Inicio de sesión exitoso: Limpiar intentos fallidos
                    $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?")->execute([$ip_address]);
                    
                    // 4. Registro de Auditoría (Requisito PCI DSS)
                    // Registra accesos exitosos para monitoreo (en este caso lo volcamos al error_log del servidor temporalmente)
                    error_log("AUDIT_LOG: Acceso exitoso al panel - Usuario: {$user['username']} - IP: {$ip_address} - Fecha: " . date('Y-m-d H:i:s'));
                    
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_role'] = $user['role'];
                    
                    // Decodificar permisos y guardarlos en sesión
                    if (!empty($user['permisos'])) {
                        $_SESSION['admin_permisos'] = json_decode($user['permisos'], true);
                    } else {
                        $_SESSION['admin_permisos'] = [];
                    }
                    
                    echo "<script>sessionStorage.setItem('admin_session_active', '1'); window.location.href='noticias.php';</script>";
                    exit();
                } else {
                    $error = "Usuario o contraseña incorrectos.";
                    
                    // Registrar intento fallido
                    if ($attempt_data) {
                        $pdo->prepare("UPDATE login_attempts SET attempts = attempts + 1, last_attempt = NOW() WHERE ip_address = ?")->execute([$ip_address]);
                    } else {
                        $pdo->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt) VALUES (?, 1, NOW())")->execute([$ip_address]);
                    }
                }
            }
        } else {
            $error = "Error de conexión a la base de datos.";
        }
    } else {
        $error = "Por favor ingrese usuario y contraseña.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo - Manos Inclusivas</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-style.css?v=11">
    <!-- PWA Config -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#8b5cf6">
    <link rel="apple-touch-icon" href="https://miic-neurodesarrollo.org/img/Logo%20circular.webp">
    <meta name="mobile-web-app-capable" content="yes">
    <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js');
      });
    }
    </script>
</head>
<body class="login-body">

    <!-- Fondo de Partículas interactivo -->
    <div id="particles-js" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2;"></div>
    
    <div class="login-box" style="position: relative; z-index: 3;">
        <img src="https://miic-neurodesarrollo.org/img/Logo%20circular.webp" alt="Logo Manos Inclusivas" style="width: 100px; height: 100px; border-radius: 50%; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); background: white; padding: 5px;">
        <h2>Manos Inclusivas</h2>
        <p>Panel de Administración</p>

        <?php if($error): ?>
            <div class="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group" style="text-align: left;">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Ingresa tu usuario" required autocomplete="username">
            </div>
            <div class="form-group" style="text-align: left;">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Acceder al Panel</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 100, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#8b5cf6" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.8, "random": false },
                "size": { "value": 4, "random": true },
                "line_linked": {
                    "enable": true,
                    "distance": 120,
                    "color": "#8b5cf6",
                    "opacity": 0.6,
                    "width": 2
                },
                "move": {
                    "enable": true,
                    "speed": 2,
                    "direction": "none",
                    "random": false,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": { "enable": true, "mode": "grab" },
                    "onclick": { "enable": true, "mode": "push" },
                    "resize": true
                },
                "modes": {
                    "grab": { "distance": 140, "line_linked": { "opacity": 0.5 } },
                    "push": { "particles_nb": 4 }
                }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>
