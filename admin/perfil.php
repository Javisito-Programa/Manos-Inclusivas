<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'config/database.php';

$mensaje = '';
$tipo_alerta = 'success'; // 'success' o 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($new_password) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
        $tipo_alerta = "error";
    } elseif ($new_password !== $confirm_password) {
        $mensaje = "Las contraseñas no coinciden.";
        $tipo_alerta = "error";
    } else {
        // Encriptar la nueva contraseña de forma segura
        $hash = password_hash($new_password, PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        if ($stmt->execute([$hash, $_SESSION['admin_id']])) {
            $mensaje = "¡Tu contraseña ha sido actualizada exitosamente!";
            $tipo_alerta = "success";
        } else {
            $mensaje = "Error al actualizar la base de datos.";
            $tipo_alerta = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <!-- Animación de ondas de fondo -->
    <div style="position: fixed; bottom: 0; left: 0; width: 100%; overflow: hidden; z-index: -1; opacity: 0.2;">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" style="width: 200%; height: 250px; transform: translateX(0); animation: waveAnimate 20s linear infinite;">
            <path d="M0,60 C300,120 300,0 600,60 C900,120 900,0 1200,60 C1500,120 1500,0 1800,60 C2100,120 2100,0 2400,60 L2400,120 L0,120 Z" fill="#6B46C1"></path>
        </svg>
    </div>
    <style>@keyframes waveAnimate { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }</style>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="../img/Logo circular.png" alt="Logo" style="width: 60px; height: 60px; border-radius: 50%; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); background: white; padding: 2px;">
            <h2>Panel Admin</h2>
        </div>
        <ul class="nav-links">
            <li><a href="noticias.php"><span class="nav-icon">📰</span> Noticias</a></li>
            <li><a href="finanzas.php"><span class="nav-icon">💰</span> Finanzas</a></li>
            <li><a href="perfil.php" class="active"><span class="nav-icon">⚙️</span> Mi Perfil</a></li>
            <li style="margin-top: auto;"><a href="logout.php"><span class="nav-icon">🚪</span> Salir</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <h1>Configuración de Seguridad</h1>
            <div class="user-profile">
                <span>Administrador</span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>

        <div class="form-container" style="max-width: 600px; margin: 0 auto; border-left: 6px solid var(--accent-purple);">
            <h3 style="margin-bottom: 25px; color: var(--accent-purple); font-size: 1.2rem; font-weight: 700;">Actualizar Contraseña</h3>
            
            <?php if(!empty($mensaje)): ?>
                <?php if($tipo_alerta == 'success'): ?>
                    <div class="alert" style="margin-bottom:20px; padding:15px; background:rgba(72, 187, 120, 0.2); color:#276749; border: 1px solid rgba(72, 187, 120, 0.4); border-radius:12px; font-weight: 600;">
                        ✔️ <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php else: ?>
                    <div class="alert" style="margin-bottom:20px; padding:15px;">
                        ❌ <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nueva Contraseña</label>
                    <input type="password" name="new_password" class="form-control" required placeholder="Ingresa al menos 6 caracteres">
                </div>
                <div class="form-group">
                    <label>Confirmar Nueva Contraseña</label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Vuelve a escribir la contraseña">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Guardar Cambios</button>
            </form>
        </div>
    </main>

</body>
</html>
