<?php
require_once 'config/session.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// 1. Verificación de seguridad: Solo usuarios con permiso 'usuarios' pueden estar aquí
$permisos = $_SESSION['admin_permisos'] ?? [];
if (!isset($permisos['usuarios']) || $permisos['usuarios'] !== true) {
    // Si no tiene permiso, lo echamos al dashboard (noticias o index)
    header("Location: noticias.php");
    exit();
}

require_once 'config/database.php';

$mensaje = '';
$tipo_alerta = 'success';

// Manejar creación de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
    $new_username = trim($_POST['username']);
    $new_password = $_POST['password'];
    
    // Recolectar checkboxes de permisos
    $nuevos_permisos = [
        'noticias' => isset($_POST['perm_noticias']),
        'noticias_borrar' => isset($_POST['perm_noticias_borrar']),
        'finanzas' => isset($_POST['perm_finanzas']),
        'usuarios' => isset($_POST['perm_usuarios'])
    ];
    $permisos_json = json_encode($nuevos_permisos);
    
    if (strlen($new_username) < 3 || strlen($new_password) < 6) {
        $mensaje = "Usuario mínimo 3 caracteres y contraseña mínimo 6.";
        $tipo_alerta = "error";
    } else {
        // Validar que no exista
        $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
        $stmt_check->execute([$new_username]);
        if ($stmt_check->rowCount() > 0) {
            $mensaje = "Ese nombre de usuario ya está en uso.";
            $tipo_alerta = "error";
        } else {
            $hash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, password_hash, role, permisos) VALUES (?, ?, 'editor', ?)");
            if ($stmt->execute([$new_username, $hash, $permisos_json])) {
                $mensaje = "Usuario '$new_username' creado con éxito.";
                $tipo_alerta = "success";
            } else {
                $mensaje = "Error al crear el usuario en la BD.";
                $tipo_alerta = "error";
            }
        }
    }
}

// Manejar eliminación de usuario
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_to_delete = (int)$_GET['delete'];
    
    // Evitar que el admin principal (ID 1) o a sí mismo se elimine accidentalmente
    if ($id_to_delete === 1) {
        $mensaje = "No puedes eliminar al SuperAdministrador principal.";
        $tipo_alerta = "error";
    } elseif ($id_to_delete === $_SESSION['admin_id']) {
        $mensaje = "No puedes eliminarte a ti mismo.";
        $tipo_alerta = "error";
    } else {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        if ($stmt->execute([$id_to_delete])) {
            $mensaje = "Usuario eliminado correctamente.";
            $tipo_alerta = "success";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-style.css?v=9">
    <!-- PWA Config -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#8b5cf6">
    <link rel="apple-touch-icon" href="../img/Logo%20circular.png">
    <meta name="mobile-web-app-capable" content="yes">
    <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js');
      });
    }
    </script>
    <style>
        .perm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        .perm-item {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(0,0,0,0.1);
            padding: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        .perm-item input {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            accent-color: var(--accent-purple);
        }
        .perm-badge {
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 12px;
            background: #e2e8f0;
            color: #4a5568;
            margin-right: 5px;
            display: inline-block;
            margin-bottom: 3px;
        }
        .perm-badge.active {
            background: #d6bcfa;
            color: #553c9a;
        }
    </style>
</head>
<body>
    <!-- Animación de ondas de fondo -->
    <div style="position: fixed; bottom: 0; left: 0; width: 100%; overflow: hidden; z-index: -1; opacity: 0.2;">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" style="width: 200%; height: 250px; transform: translateX(0); animation: waveAnimate 20s linear infinite;">
            <path d="M0,60 C300,120 300,0 600,60 C900,120 900,0 1200,60 C1500,120 1500,0 1800,60 C2100,120 2100,0 2400,60 L2400,120 L0,120 Z" fill="#E53E3E"></path>
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
            <?php if(isset($permisos['noticias']) && $permisos['noticias']): ?>
            <li><a href="noticias.php"><span class="nav-icon">📰</span> Noticias</a></li>
            <?php endif; ?>
            
            <?php if(isset($permisos['finanzas']) && $permisos['finanzas']): ?>
            <li><a href="finanzas.php"><span class="nav-icon">💰</span> Finanzas</a></li>
            <?php endif; ?>
            
            <?php if(isset($permisos['usuarios']) && $permisos['usuarios']): ?>
            <li><a href="usuarios.php" class="active"><span class="nav-icon">👥</span> Usuarios</a></li>
            <?php endif; ?>
            
            <li><a href="perfil.php"><span class="nav-icon">⚙️</span> Mi Perfil</a></li>
            <li style="margin-top: auto;"><a href="logout.php"><span class="nav-icon">🚪</span> Salir</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <h1>Gestión de Usuarios (SuperAdmin)</h1>
            <div class="user-profile">
                <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Administrador'); ?></span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>

        <?php if(!empty($mensaje)): ?>
            <?php if($tipo_alerta == 'success'): ?>
                <div class="alert" style="margin-bottom:20px; padding:15px; background:rgba(72, 187, 120, 0.2); color:#276749; border: 1px solid rgba(72, 187, 120, 0.4); border-radius:12px; font-weight: 600;">
                    ✔️ <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php else: ?>
                <div class="alert" style="margin-bottom:20px; padding:15px; background:rgba(229, 62, 62, 0.2); color:#c53030; border: 1px solid rgba(229, 62, 62, 0.4); border-radius:12px; font-weight: 600;">
                    ❌ <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Formulario Crear Usuario -->
        <div class="form-container" style="border-left: 6px solid #E53E3E;">
            <h3 style="margin-bottom: 15px; color: #E53E3E; font-size: 1.2rem; font-weight: 700;">Crear Nueva Cuenta</h3>
            <form action="" method="POST">
                <input type="hidden" name="action" value="create">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Nombre de Usuario (Ej: juan_perez)</label>
                        <input type="text" name="username" class="form-control" required placeholder="Sin espacios">
                    </div>
                    <div class="form-group">
                        <label>Contraseña Temporal</label>
                        <input type="text" name="password" class="form-control" required placeholder="Mínimo 6 caracteres">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label style="font-size: 1.1rem; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 8px;">Asignar Permisos a esta cuenta:</label>
                    <div class="perm-grid">
                        <div class="perm-item">
                            <input type="checkbox" id="p1" name="perm_noticias" value="1">
                            <label for="p1" style="margin:0; cursor:pointer;">📰 Ver y Crear Noticias</label>
                        </div>
                        <div class="perm-item">
                            <input type="checkbox" id="p2" name="perm_noticias_borrar" value="1">
                            <label for="p2" style="margin:0; cursor:pointer;">🗑️ Eliminar Noticias</label>
                        </div>
                        <div class="perm-item">
                            <input type="checkbox" id="p3" name="perm_finanzas" value="1">
                            <label for="p3" style="margin:0; cursor:pointer;">💰 Acceder a Finanzas</label>
                        </div>
                        <div class="perm-item">
                            <input type="checkbox" id="p4" name="perm_usuarios" value="1">
                            <label for="p4" style="margin:0; cursor:pointer; color: #E53E3E; font-weight:bold;">👥 Crear Usuarios (Admin)</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="background-color: #E53E3E; margin-top: 10px;">Crear Usuario</button>
            </form>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="data-table-wrapper" style="margin-top: 30px;">
            <div style="padding: 20px 25px; border-bottom: 1px solid rgba(226, 232, 240, 0.5);">
                <h3 style="color: var(--text-primary); font-size: 1.2rem; font-weight: 700;">Cuentas Activas</h3>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Permisos Asignados</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($pdo)): ?>
                        <?php 
                        $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id ASC");
                        while($row = $stmt->fetch()): 
                            $p = json_decode($row['permisos'], true) ?: [];
                        ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--text-secondary);">#<?php echo $row['id']; ?></td>
                            <td style="font-weight: 700; color: var(--accent-purple);">
                                <?php echo htmlspecialchars($row['username']); ?>
                                <?php if($row['id'] == 1) echo '<span title="Súper Admin" style="font-size:12px;">👑</span>'; ?>
                                <?php if($row['id'] == $_SESSION['admin_id']) echo '<span style="font-size:10px; background:#48bb78; color:white; padding:2px 5px; border-radius:4px; margin-left:5px;">(Tú)</span>'; ?>
                            </td>
                            <td>
                                <?php if(isset($p['noticias']) && $p['noticias']): ?><span class="perm-badge active">Noticias</span><?php endif; ?>
                                <?php if(isset($p['noticias_borrar']) && $p['noticias_borrar']): ?><span class="perm-badge active" style="background:#fed7d7; color:#c53030;">Borrar Noti.</span><?php endif; ?>
                                <?php if(isset($p['finanzas']) && $p['finanzas']): ?><span class="perm-badge active" style="background:#c6f6d5; color:#276749;">Finanzas</span><?php endif; ?>
                                <?php if(isset($p['usuarios']) && $p['usuarios']): ?><span class="perm-badge active" style="background:#feebc8; color:#c05621;">SuperAdmin</span><?php endif; ?>
                                <?php if(empty($p)): ?><span class="perm-badge">Sin permisos</span><?php endif; ?>
                            </td>
                            <td><span class="badge badge-transfer"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></span></td>
                            <td>
                                <?php if($row['id'] !== 1 && $row['id'] !== $_SESSION['admin_id']): ?>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Seguro que deseas ELIMINAR a este usuario para siempre?');">Revocar Acceso</a>
                                <?php else: ?>
                                    <span style="color: gray; font-size: 0.8rem;">Protegido</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
