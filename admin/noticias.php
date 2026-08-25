<?php
/**
 * admin/noticias.php
 * Panel de administración de noticias. Permite crear, fijar, ordenar, editar y eliminar noticias.
 * Implementa protección de sesión y sentencias preparadas (PDO) para evitar SQL Injection.
 */

// 1. Verificación de sesión y seguridad
require_once 'config/session.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// 2. Verificación de roles/permisos (Control de Acceso)
$permisos = $_SESSION['admin_permisos'] ?? [];
if (!isset($permisos['noticias']) || $permisos['noticias'] !== true) {
    header("Location: perfil.php");
    exit();
}

// 3. Conexión segura a la BD
require_once 'config/database.php';
date_default_timezone_set('America/Mexico_City');

// 4. Mantenimiento Automático: Auto-inicializar pinned_order si es 0 (para noticias ya fijadas)
$stmt_check = $pdo->query("SELECT id FROM noticias WHERE is_pinned = 1 AND pinned_order = 0 ORDER BY fecha_publicacion DESC");
$unordered = $stmt_check->fetchAll();
if (count($unordered) > 0) {
    // Obtenemos el valor máximo actual para agregar los nuevos al final
    $stmtMax = $pdo->query("SELECT MAX(pinned_order) FROM noticias WHERE is_pinned = 1");
    $maxOrder = (int)$stmtMax->fetchColumn();
    foreach ($unordered as $row) {
        $maxOrder++;
        $pdo->prepare("UPDATE noticias SET pinned_order = ? WHERE id = ?")->execute([$maxOrder, $row['id']]);
    }
}

// 5. Manejar orden manual de noticias fijadas (Arriba / Abajo)
if (isset($_GET['move_up'])) {
    $id = filter_var($_GET['move_up'], FILTER_SANITIZE_NUMBER_INT); // Sanitización de ID
    $stmt = $pdo->prepare("SELECT pinned_order FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();
    
    if ($current && $current['pinned_order'] > 0) {
        $current_order = $current['pinned_order'];
        // Buscar la noticia que está justo encima visualmente (menor pinned_order)
        $stmt_above = $pdo->prepare("SELECT id, pinned_order FROM noticias WHERE is_pinned = 1 AND pinned_order < ? ORDER BY pinned_order DESC LIMIT 1");
        $stmt_above->execute([$current_order]);
        $above = $stmt_above->fetch();
        
        if ($above) {
            // Intercambiar el orden de ambas noticias de forma segura
            $pdo->prepare("UPDATE noticias SET pinned_order = ? WHERE id = ?")->execute([$above['pinned_order'], $id]);
            $pdo->prepare("UPDATE noticias SET pinned_order = ? WHERE id = ?")->execute([$current_order, $above['id']]);
        }
    }
    header("Location: noticias.php");
    exit();
}

if (isset($_GET['move_down'])) {
    $id = filter_var($_GET['move_down'], FILTER_SANITIZE_NUMBER_INT); // Sanitización de ID
    $stmt = $pdo->prepare("SELECT pinned_order FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();
    
    if ($current && $current['pinned_order'] > 0) {
        $current_order = $current['pinned_order'];
        // Buscar la noticia que está justo debajo visualmente (mayor pinned_order)
        $stmt_below = $pdo->prepare("SELECT id, pinned_order FROM noticias WHERE is_pinned = 1 AND pinned_order > ? ORDER BY pinned_order ASC LIMIT 1");
        $stmt_below->execute([$current_order]);
        $below = $stmt_below->fetch();
        
        if ($below) {
            // Intercambiar el orden de ambas noticias de forma segura
            $pdo->prepare("UPDATE noticias SET pinned_order = ? WHERE id = ?")->execute([$below['pinned_order'], $id]);
            $pdo->prepare("UPDATE noticias SET pinned_order = ? WHERE id = ?")->execute([$current_order, $below['id']]);
        }
    }
    header("Location: noticias.php");
    exit();
}

// 6. Configuración de directorio para subir imágenes
$upload_dir = 'uploads/noticias/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true); // Crear con permisos seguros si no existe
}

// 7. Manejar fijar/desfijar noticia del carrusel principal
if (isset($_GET['toggle_pin'])) {
    $id_pin = filter_var($_GET['toggle_pin'], FILTER_SANITIZE_NUMBER_INT);
    
    // Consultar estado actual de la noticia
    $stmt = $pdo->prepare("SELECT is_pinned FROM noticias WHERE id = ?");
    $stmt->execute([$id_pin]);
    $noticia_pin = $stmt->fetch();
    
    if ($noticia_pin) {
        $is_currently_pinned = (bool)$noticia_pin['is_pinned'];
        if ($is_currently_pinned) {
            // Acción: Desfijar y resetear orden a 0
            $stmt = $pdo->prepare("UPDATE noticias SET is_pinned = 0, pinned_order = 0 WHERE id = ?");
            $stmt->execute([$id_pin]);
        } else {
            // Acción: Fijar y mandar al final de la lista (mayor pinned_order + 1)
            $stmtMax = $pdo->query("SELECT MAX(pinned_order) as max_order FROM noticias WHERE is_pinned = 1");
            $maxOrder = (int)$stmtMax->fetchColumn();
            $stmt = $pdo->prepare("UPDATE noticias SET is_pinned = 1, pinned_order = ? WHERE id = ?");
            $stmt->execute([$maxOrder + 1, $id_pin]);
        }
    }
    header("Location: noticias.php");
    exit();
}

// 8. Manejar eliminación de noticia (Soft y Hard Delete de imágenes)
if (isset($_GET['delete'])) {
    if (!isset($permisos['noticias_borrar']) || $permisos['noticias_borrar'] !== true) {
        $mensaje = "No tienes permiso para eliminar noticias.";
    } else {
        $id_to_delete = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);
        
        // Obtener la imagen asociada para borrarla del servidor y no dejar basura
        $stmt = $pdo->prepare("SELECT imagen_path FROM noticias WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        $noticia = $stmt->fetch();
        
        if ($noticia && $noticia['imagen_path']) {
            $file_path = $noticia['imagen_path'];
            if (file_exists($file_path)) {
                unlink($file_path); // Borrado físico del archivo WebP/JPG
            }
        }
        
        // Borrar definitivamente de la base de datos (Hard delete)
        $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        
        header("Location: noticias.php");
        exit();
    }
}

// 9. Manejar subida de nueva noticia
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo'])) {
    // Sanitización y prevención XSS de campos de texto
    $titulo = htmlspecialchars(trim($_POST['titulo']), ENT_QUOTES, 'UTF-8');
    $contenido = htmlspecialchars(trim($_POST['contenido']), ENT_QUOTES, 'UTF-8');
    $fecha_publicacion = htmlspecialchars($_POST['fecha_publicacion'], ENT_QUOTES, 'UTF-8') ?: date('Y-m-d');
    $imagen_path = '';
    
    // Sanitización de enlaces
    $enlace_facebook = filter_input(INPUT_POST, 'enlace_facebook', FILTER_SANITIZE_URL) ?? '';
    $enlace_instagram = filter_input(INPUT_POST, 'enlace_instagram', FILTER_SANITIZE_URL) ?? '';
    $enlace_twitter = filter_input(INPUT_POST, 'enlace_twitter', FILTER_SANITIZE_URL) ?? '';
    $enlace_tiktok = filter_input(INPUT_POST, 'enlace_tiktok', FILTER_SANITIZE_URL) ?? '';
    $enlace_youtube = filter_input(INPUT_POST, 'enlace_youtube', FILTER_SANITIZE_URL) ?? '';
    
    // 10. Prevención de Duplicados
    // 10. Prevención de Duplicados
    // Verificar si ya existe una noticia con el mismo título para evitar spam o errores
    $stmt_check = $pdo->prepare("SELECT id FROM noticias WHERE titulo = ?");
    $stmt_check->execute([$titulo]);
    if ($stmt_check->rowCount() > 0) {
        $mensaje = "Ya existe una noticia con ese título. Por favor, edita la existente o cambia el título.";
    } else {
        $uploaded_images = [];
        
        // 11. Procesamiento seguro de imágenes múltiples
        if (isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name'])) {
            $file_count = count($_FILES['imagenes']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['imagenes']['error'][$i] == 0) {
                    $file_info = pathinfo($_FILES['imagenes']['name'][$i]);
                    $ext = strtolower($file_info['extension']);
                    // Validar estrictamente la extensión del archivo para prevenir subida de scripts maliciosos (.php, .js)
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($ext, $allowed_ext)) {
                        // Generar un nombre único aleatorio para evitar colisiones y ocultar el nombre original
                        $new_filename = uniqid('noticia_') . '.' . $ext;
                        $destination = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $destination)) {
                            $uploaded_images[] = 'uploads/noticias/' . $new_filename;
                        }
                    }
                }
            }
        }
        
        // La primera imagen procesada se asigna como portada
        if (count($uploaded_images) > 0) {
            $imagen_path = array_shift($uploaded_images); // Extraer el primer elemento
        }
        
        // Las imágenes restantes se guardan como un arreglo codificado en JSON
        $imagenes_extra = count($uploaded_images) > 0 ? json_encode($uploaded_images) : null;
        
        // 12. Inserción final en la base de datos (Uso de Sentencias Preparadas contra Inyecciones SQL)
        if (empty($mensaje)) {
            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, contenido, imagen_path, imagenes_extra, fecha_publicacion, enlace_facebook, enlace_instagram, enlace_twitter, enlace_tiktok, enlace_youtube) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$titulo, $contenido, $imagen_path, $imagenes_extra, $fecha_publicacion, $enlace_facebook, $enlace_instagram, $enlace_twitter, $enlace_tiktok, $enlace_youtube])) {
                $mensaje = "¡Noticia publicada con éxito!";
            } else {
                $mensaje = "Error al guardar en la base de datos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - Administración</title>
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
<body>
    <!-- Animación de ondas de fondo -->
    <div class="animated-bg"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header" style="text-align: center; margin-bottom: 20px;">
            <img src="https://miic-neurodesarrollo.org/img/Logo%20circular.webp" alt="Logo" style="width: 60px; height: 60px; border-radius: 50%; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); background: white; padding: 2px;">
            <h2 style="font-size: 1.2rem; font-weight: 700; margin: 0; color: #1f2937;">Panel Admin</h2>
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
            <h1>Gestión de Noticias</h1>
            <div class="user-profile">
                <span>Administrador</span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>

        <div class="form-container">
            <h3 style="margin-bottom: 20px; color: var(--accent-purple); font-size: 1.2rem; font-weight: 700;">Publicar Nueva Noticia</h3>
            <?php if(!empty($mensaje)): ?>
                <div class="alert" style="margin-bottom:20px; padding:15px; background:rgba(72, 187, 120, 0.2); color:#276749; border: 1px solid rgba(72, 187, 120, 0.4); border-radius:12px; font-weight: 600;">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="responsive-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Título de la Noticia</label>
                        <input type="text" name="titulo" class="form-control" required placeholder="Ej: Nueva alianza con escuelas locales">
                    </div>
                    <div class="form-group">
                        <label>Fecha de Publicación</label>
                        <input type="date" name="fecha_publicacion" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Imágenes (Opcional - Selecciona una o varias para el carrusel)</label>
                    <div class="custom-file-upload-container">
                        <label for="image-input" class="btn" style="display: inline-flex; align-items: center; background: rgba(109, 40, 217, 0.1); color: var(--accent-purple); border: 1px solid rgba(109, 40, 217, 0.3); font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                            <span style="font-size: 1.2rem; margin-right: 8px;">📷</span> Seleccionar Imágenes
                        </label>
                        <input type="file" id="image-input" name="imagenes[]" accept="image/*" multiple style="display: none;">
                        <small style="display: block; margin-top: 8px; color: var(--text-secondary);">Selecciona una por una o varias a la vez. La que diga "Portada" será la principal.</small>
                        <div id="image-preview-container" style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px;"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Contenido</label>
                    <textarea name="contenido" class="form-control" rows="5" required placeholder="Escribe el desarrollo de la noticia aquí..."></textarea>
                </div>
                
                <h4 style="margin-top: 15px; margin-bottom: 10px; color: var(--accent-purple); font-size: 1rem;">Redes Sociales Vinculadas (Opcional)</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label>Facebook</label>
                        <input type="url" name="enlace_facebook" class="form-control" placeholder="https://facebook.com/...">
                    </div>
                    <div class="form-group">
                        <label>Instagram</label>
                        <input type="url" name="enlace_instagram" class="form-control" placeholder="https://instagram.com/...">
                    </div>
                    <div class="form-group">
                        <label>Twitter (X)</label>
                        <input type="url" name="enlace_twitter" class="form-control" placeholder="https://twitter.com/...">
                    </div>
                    <div class="form-group">
                        <label>TikTok</label>
                        <input type="url" name="enlace_tiktok" class="form-control" placeholder="https://tiktok.com/...">
                    </div>
                    <div class="form-group">
                        <label>YouTube</label>
                        <input type="url" name="enlace_youtube" class="form-control" placeholder="https://youtube.com/...">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Publicar Noticia</button>
            </form>
        </div>

        <div class="data-table-wrapper">
            <div style="padding: 20px 25px; border-bottom: 1px solid rgba(226, 232, 240, 0.5);">
                <h3 style="color: var(--accent-purple); font-size: 1.2rem; font-weight: 700;">Noticias Publicadas</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Orden</th>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Fecha de Publicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($pdo)): ?>
                        <?php 
                        $stmt = $pdo->query("SELECT * FROM noticias ORDER BY is_pinned DESC, pinned_order ASC, fecha_publicacion DESC, id DESC");
                        while($row = $stmt->fetch()): 
                            $raw_date = !empty($row['fecha_publicacion']) ? $row['fecha_publicacion'] : $row['created_at'];
                            $fecha_display = date('d/m/Y', strtotime($raw_date));
                        ?>
                        <tr data-id="<?php echo $row['id']; ?>" class="<?php echo $row['is_pinned'] ? 'sortable-row' : ''; ?>" style="<?php echo $row['is_pinned'] ? 'background-color: rgba(236, 72, 153, 0.05);' : ''; ?>">
                            <td style="font-weight: 600; color: var(--text-secondary);">
                                #<?php echo $row['id']; ?>
                                <?php if($row['is_pinned']) echo '<br><span style="font-size: 0.8rem; color: var(--accent-purple);">📌 Fijada</span>'; ?>
                            </td>
                            <td style="text-align: center;" data-label="Orden">
                                <?php if($row['is_pinned']): ?>
                                    <div style="display: inline-flex; flex-direction: row; gap: 5px;">
                                        <a href="?move_up=<?php echo $row['id']; ?>" class="order-btn" title="Mover arriba" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: rgba(109, 40, 217, 0.1); color: var(--accent-purple); border-radius: 8px; font-size: 1.2rem; transition: background 0.3s;">⬆️</a>
                                        <a href="?move_down=<?php echo $row['id']; ?>" class="order-btn" title="Mover abajo" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: rgba(109, 40, 217, 0.1); color: var(--accent-purple); border-radius: 8px; font-size: 1.2rem; transition: background 0.3s;">⬇️</a>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #cbd5e1;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;" data-label="Imagen">
                                <?php if($row['imagen_path']): ?>
                                    <img src="<?php echo htmlspecialchars($row['imagen_path']); ?>" width="60" style="border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                <?php else: ?>
                                    <span class="badge badge-card">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 500;"><?php echo htmlspecialchars($row['titulo']); ?></td>
                            <td><span class="badge badge-transfer"><?php echo $fecha_display; ?></span></td>
                            <td style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center; justify-content: flex-start;">
                                <a href="?toggle_pin=<?php echo $row['id']; ?>" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; text-align: center; background-color: #cbd5e1; color: #334155; min-width: 80px;">
                                    <?php echo $row['is_pinned'] ? 'Desfijar' : '📌 Fijar'; ?>
                                </a>
                                <a href="editar_noticia.php?id=<?php echo $row['id']; ?>" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; text-align: center; background: linear-gradient(135deg, var(--accent-purple) 0%, #9F7AEA 100%); color: white; min-width: 80px;">Editar</a>
                                <?php if(isset($permisos['noticias_borrar']) && $permisos['noticias_borrar']): ?>
                                <a href="?delete=<?php echo $row['id']; ?>" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; text-align: center; background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); color: white; min-width: 80px;" onclick="return confirm('¿Seguro que deseas eliminar esta noticia?');">Eliminar</a>
                                <?php else: ?>
                                <span style="color: gray; font-size: 0.8rem;">Sin Permiso</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 40px;">
                                <em style="font-size: 1.1rem;">Base de datos no conectada. Revisa tu db_secrets.php</em>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Manejo de sidebar en móvil (Global Scope)
        window.toggleSidebar = function() {
            document.querySelector('.sidebar').classList.toggle('active');
        };

        const fileInput = document.getElementById('image-input');
        const previewContainer = document.getElementById('image-preview-container');
        if (fileInput && previewContainer) {
            let dt = new DataTransfer();

            fileInput.addEventListener('change', function(e) {
                for(let i = 0; i < this.files.length; i++){
                    dt.items.add(this.files[i]);
                }
                updatePreviews();
            });

            function updatePreviews() {
                previewContainer.innerHTML = '';
                fileInput.files = dt.files;
                
                for(let i = 0; i < dt.files.length; i++) {
                    const file = dt.files[i];
                    const reader = new FileReader();
                    
                    const div = document.createElement('div');
                    div.style.position = 'relative';
                    div.style.width = '100px';
                    div.style.height = '100px';
                    div.style.borderRadius = '12px';
                    div.style.overflow = 'hidden';
                    div.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                    
                    const img = document.createElement('img');
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    
                    const btn = document.createElement('button');
                    btn.innerHTML = '×';
                    btn.style.position = 'absolute';
                    btn.style.top = '5px';
                    btn.style.right = '5px';
                    btn.style.background = 'rgba(239, 68, 68, 0.9)';
                    btn.style.color = 'white';
                    btn.style.border = 'none';
                    btn.style.borderRadius = '50%';
                    btn.style.width = '24px';
                    btn.style.height = '24px';
                    btn.style.cursor = 'pointer';
                    btn.style.display = 'flex';
                    btn.style.alignItems = 'center';
                    btn.style.justifyContent = 'center';
                    btn.style.fontWeight = 'bold';
                    
                    btn.onclick = function(e) {
                        e.preventDefault();
                        dt.items.remove(i);
                        updatePreviews();
                    };
                    
                    if(i === 0) {
                        const badge = document.createElement('div');
                        badge.innerText = 'Portada';
                        badge.style.position = 'absolute';
                        badge.style.bottom = '0';
                        badge.style.left = '0';
                        badge.style.width = '100%';
                        badge.style.background = 'rgba(139, 92, 246, 0.9)';
                        badge.style.color = 'white';
                        badge.style.fontSize = '0.7rem';
                        badge.style.textAlign = 'center';
                        badge.style.padding = '2px 0';
                        badge.style.fontWeight = 'bold';
                        div.appendChild(badge);
                    }
                    
                    reader.onload = function(e) {
                        img.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                    
                    div.appendChild(img);
                    div.appendChild(btn);
                    previewContainer.appendChild(div);
                }
            }
        }
    });
    </script>
</body>
</html>
