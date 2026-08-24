<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
$permisos = $_SESSION['admin_permisos'] ?? [];
if (!isset($permisos['noticias']) || $permisos['noticias'] !== true) {
    header("Location: perfil.php");
    exit();
}
require_once 'config/database.php';
date_default_timezone_set('America/Mexico_City');

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: noticias.php");
    exit();
}
$id_noticia = (int)$_GET['id'];

// Obtener datos de la noticia
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id_noticia]);
$noticia = $stmt->fetch();

if (!$noticia) {
    header("Location: noticias.php");
    exit();
}

$mensaje = '';
$upload_dir = '../uploads/noticias/';

// Manejar actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo'])) {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $fecha_publicacion = $_POST['fecha_publicacion'] ?: date('Y-m-d');
    $enlace_facebook = $_POST['enlace_facebook'] ?? '';
    $enlace_instagram = $_POST['enlace_instagram'] ?? '';
    $enlace_twitter = $_POST['enlace_twitter'] ?? '';
    $enlace_tiktok = $_POST['enlace_tiktok'] ?? '';
    $enlace_youtube = $_POST['enlace_youtube'] ?? '';
    
    // Verificar duplicados (mismo título pero diferente ID)
    $stmt_check = $pdo->prepare("SELECT id FROM noticias WHERE titulo = ? AND id != ?");
    $stmt_check->execute([$titulo, $id_noticia]);
    
    if ($stmt_check->rowCount() > 0) {
        $mensaje = "Ya existe OTRA noticia con ese mismo título. Por favor, cambia el título.";
    } else {
        $imagen_path = $noticia['imagen_path']; // Mantener la anterior por defecto
        $imagenes_extra = $noticia['imagenes_extra'];
        
        // Procesar nueva imagen si se subió
        if (isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name']) && $_FILES['imagenes']['error'][0] == 0) {
            
            // Borrar las viejas
            if (!empty($noticia['imagen_path']) && file_exists('../' . $noticia['imagen_path'])) {
                unlink('../' . $noticia['imagen_path']);
            }
            if (!empty($noticia['imagenes_extra'])) {
                $viejas_extra = json_decode($noticia['imagenes_extra'], true);
                if (is_array($viejas_extra)) {
                    foreach ($viejas_extra as $vieja) {
                        if (file_exists('../' . $vieja)) unlink('../' . $vieja);
                    }
                }
            }
            
            $uploaded_images = [];
            $file_count = count($_FILES['imagenes']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['imagenes']['error'][$i] == 0) {
                    $file_info = pathinfo($_FILES['imagenes']['name'][$i]);
                    $ext = strtolower($file_info['extension']);
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($ext, $allowed_ext)) {
                        $new_filename = uniqid('noticia_') . '.' . $ext;
                        $destination = $upload_dir . $new_filename;
                        if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $destination)) {
                            $uploaded_images[] = 'uploads/noticias/' . $new_filename;
                        }
                    }
                }
            }
            
            if (count($uploaded_images) > 0) {
                $imagen_path = array_shift($uploaded_images);
                $imagenes_extra = count($uploaded_images) > 0 ? json_encode($uploaded_images) : null;
            }
        }
        
        if (empty($mensaje)) {
            $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, contenido = ?, imagen_path = ?, imagenes_extra = ?, fecha_publicacion = ?, enlace_facebook = ?, enlace_instagram = ?, enlace_twitter = ?, enlace_tiktok = ?, enlace_youtube = ? WHERE id = ?");
            if ($stmt->execute([$titulo, $contenido, $imagen_path, $imagenes_extra, $fecha_publicacion, $enlace_facebook, $enlace_instagram, $enlace_twitter, $enlace_tiktok, $enlace_youtube, $id_noticia])) {
                header("Location: noticias.php");
                exit();
            } else {
                $mensaje = "Error al actualizar en la base de datos.";
            }
        }
    }
}

// Preparar variables para el formulario
$titulo_val = htmlspecialchars($noticia['titulo']);
$contenido_val = htmlspecialchars($noticia['contenido']);
$fecha_val = !empty($noticia['fecha_publicacion']) ? $noticia['fecha_publicacion'] : date('Y-m-d', strtotime($noticia['created_at']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Noticia - Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <!-- Animación de ondas de fondo -->
    <div style="position: fixed; bottom: 0; left: 0; width: 100%; overflow: hidden; z-index: -1; opacity: 0.2;">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" style="width: 200%; height: 250px; transform: translateX(0); animation: waveAnimate 20s linear infinite;">
            <path d="M0,60 C300,120 300,0 600,60 C900,120 900,0 1200,60 C1500,120 1500,0 1800,60 C2100,120 2100,0 2400,60 L2400,120 L0,120 Z" fill="#f59e0b"></path>
        </svg>
    </div>
    <style>@keyframes waveAnimate { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }</style>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="https://miic-neurodesarrollo.org/img/Logo circular.png" alt="Logo" style="width: 60px; height: 60px; border-radius: 50%; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); background: white; padding: 2px;">
            <h2>Panel Admin</h2>
        </div>
        <ul class="nav-links">
            <?php if(isset($permisos['noticias']) && $permisos['noticias']): ?>
            <li><a href="noticias.php" class="active"><span class="nav-icon">📰</span> Noticias</a></li>
            <?php endif; ?>
            
            <?php if(isset($permisos['finanzas']) && $permisos['finanzas']): ?>
            <li><a href="finanzas.php"><span class="nav-icon">💰</span> Finanzas</a></li>
            <?php endif; ?>
            
            <?php if(isset($permisos['usuarios']) && $permisos['usuarios']): ?>
            <li><a href="usuarios.php"><span class="nav-icon">👥</span> Usuarios</a></li>
            <?php endif; ?>
            
            <li><a href="perfil.php"><span class="nav-icon">⚙️</span> Mi Perfil</a></li>
            <li style="margin-top: auto;"><a href="logout.php"><span class="nav-icon">🚪</span> Salir</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <h1>Editar Noticia #<?php echo $id_noticia; ?></h1>
            <div class="user-profile">
                <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Administrador'); ?></span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>

        <div class="form-container" style="max-width: 800px; margin: 0 auto; border-left: 6px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #f59e0b; font-size: 1.2rem; font-weight: 700; margin: 0;">Actualizar Información</h3>
                <a href="noticias.php" class="btn" style="background: var(--bg-tertiary); color: var(--text-main);">← Volver</a>
            </div>
            
            <?php if(!empty($mensaje)): ?>
                <div class="alert" style="margin-bottom:20px; padding:15px; background:rgba(229, 62, 62, 0.2); color:#c53030; border: 1px solid rgba(229, 62, 62, 0.4); border-radius:12px; font-weight: 600;">
                    ❌ <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Título de la Noticia</label>
                        <input type="text" name="titulo" class="form-control" required value="<?php echo $titulo_val; ?>">
                    </div>
                    <div class="form-group">
                        <label>Fecha de Publicación</label>
                        <input type="date" name="fecha_publicacion" class="form-control" required value="<?php echo $fecha_val; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Reemplazar Imágenes (Opcional - Selecciona una o varias para el carrusel)</label>
                    <?php if(!empty($noticia['imagen_path'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="https://miic-neurodesarrollo.org/<?php echo htmlspecialchars($noticia['imagen_path']); ?>" style="height: 60px; border-radius: 5px; vertical-align: middle; margin-right: 10px;">
                            <span style="font-size: 0.85rem; color: gray;">Imagen Portada actual</span>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($noticia['imagenes_extra'])): ?>
                        <div style="margin-bottom: 10px;">
                            <?php 
                            $viejas_extra = json_decode($noticia['imagenes_extra'], true);
                            if(is_array($viejas_extra)):
                                foreach($viejas_extra as $vieja):
                            ?>
                                <img src="https://miic-neurodesarrollo.org/<?php echo htmlspecialchars($vieja); ?>" style="height: 40px; border-radius: 5px; vertical-align: middle; margin-right: 5px; opacity: 0.7;">
                            <?php 
                                endforeach;
                            endif;
                            ?>
                            <span style="font-size: 0.85rem; color: gray;">(+ Extras en carrusel)</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple style="padding: 10px 15px;">
                    <small style="color: #e53e3e;">Nota: Si subes imágenes nuevas aquí, se borrarán y reemplazarán TODAS las actuales (portada y extras).</small>
                </div>
                <div class="form-group">
                    <label>Contenido</label>
                    <textarea name="contenido" class="form-control" rows="8" required><?php echo $contenido_val; ?></textarea>
                </div>
                
                <h4 style="margin-top: 15px; margin-bottom: 10px; color: var(--accent-purple); font-size: 1rem;">Redes Sociales Vinculadas (Opcional)</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label>Facebook</label>
                        <input type="url" name="enlace_facebook" class="form-control" placeholder="https://facebook.com/..." value="<?php echo htmlspecialchars($noticia['enlace_facebook'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Instagram</label>
                        <input type="url" name="enlace_instagram" class="form-control" placeholder="https://instagram.com/..." value="<?php echo htmlspecialchars($noticia['enlace_instagram'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Twitter (X)</label>
                        <input type="url" name="enlace_twitter" class="form-control" placeholder="https://twitter.com/..." value="<?php echo htmlspecialchars($noticia['enlace_twitter'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>TikTok</label>
                        <input type="url" name="enlace_tiktok" class="form-control" placeholder="https://tiktok.com/..." value="<?php echo htmlspecialchars($noticia['enlace_tiktok'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>YouTube</label>
                        <input type="url" name="enlace_youtube" class="form-control" placeholder="https://youtube.com/..." value="<?php echo htmlspecialchars($noticia['enlace_youtube'] ?? ''); ?>">
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="background-color: #f59e0b; width: 100%; font-size: 1.1rem; padding: 12px;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
