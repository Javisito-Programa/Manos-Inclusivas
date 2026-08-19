<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'config/database.php';

// Manejo de acciones (Crear, Eliminar) iría aquí
// ...

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Panel Admin</h2>
        </div>
        <ul class="nav-links">
            <li><a href="noticias.php" class="active"><span class="nav-icon">📰</span> Noticias</a></li>
            <li><a href="finanzas.php"><span class="nav-icon">💰</span> Finanzas</a></li>
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
            <h3 style="margin-bottom: 20px; color: var(--accent-purple);">Publicar Nueva Noticia</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Título de la Noticia</label>
                    <input type="text" name="titulo" class="form-control" required placeholder="Ej: Nueva alianza con escuelas locales">
                </div>
                <div class="form-group">
                    <label>Imagen de Portada</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Contenido</label>
                    <textarea name="contenido" class="form-control" rows="5" required placeholder="Escribe el desarrollo de la noticia aquí..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Publicar Noticia</button>
            </form>
        </div>

        <div>
            <h3 style="margin-bottom: 20px; color: var(--accent-purple);">Noticias Publicadas</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Fecha de Publicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($pdo)): ?>
                        <?php 
                        $stmt = $pdo->query("SELECT * FROM noticias ORDER BY created_at DESC");
                        while($row = $stmt->fetch()): 
                        ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <?php if($row['imagen_path']): ?>
                                    <img src="../<?php echo htmlspecialchars($row['imagen_path']); ?>" width="50" style="border-radius: 4px;">
                                <?php else: ?>
                                    Sin imagen
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">Editar</button>
                                <button class="btn btn-logout" style="padding: 6px 12px; font-size: 0.8rem;">Eliminar</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary);">
                                <em>Base de datos no conectada. (Modo de demostración visual)</em>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
