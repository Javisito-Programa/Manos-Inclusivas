<?php
require_once 'config/session.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
$permisos = $_SESSION['admin_permisos'] ?? [];
if (!isset($permisos['finanzas']) || $permisos['finanzas'] !== true) {
    header("Location: perfil.php");
    exit();
}
require_once 'config/database.php';

// Filtro de fechas (Día, Semana, Mes) o Custom
$filtro = $_GET['filtro'] ?? 'mes';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas - Administración</title>
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
</head>
<body>
    <!-- Animación de ondas de fondo -->
    <div style="position: fixed; bottom: 0; left: 0; width: 100%; overflow: hidden; z-index: -1; opacity: 0.2;">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" style="width: 200%; height: 250px; transform: translateX(0); animation: waveAnimate 20s linear infinite;">
            <path d="M0,60 C300,120 300,0 600,60 C900,120 900,0 1200,60 C1500,120 1500,0 1800,60 C2100,120 2100,0 2400,60 L2400,120 L0,120 Z" fill="#38A169"></path>
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
            <li><a href="finanzas.php" class="active"><span class="nav-icon">💰</span> Finanzas</a></li>
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
            <h1>Contabilidad y Donativos</h1>
            <div class="user-profile">
                <span>Administrador</span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>

        <!-- Filtros rápidos -->
        <div style="margin-bottom: 30px; display: flex; gap: 15px;">
            <a href="?filtro=dia" class="btn <?php echo $filtro=='dia' ? 'btn-success' : 'btn-logout'; ?>" style="<?php echo $filtro=='dia' ? '' : 'background: var(--glass-bg); backdrop-filter: blur(10px); border: var(--glass-border); color: var(--text-primary);'; ?>">Hoy</a>
            <a href="?filtro=semana" class="btn <?php echo $filtro=='semana' ? 'btn-success' : 'btn-logout'; ?>" style="<?php echo $filtro=='semana' ? '' : 'background: var(--glass-bg); backdrop-filter: blur(10px); border: var(--glass-border); color: var(--text-primary);'; ?>">Esta Semana</a>
            <a href="?filtro=mes" class="btn <?php echo $filtro=='mes' ? 'btn-success' : 'btn-logout'; ?>" style="<?php echo $filtro=='mes' ? '' : 'background: var(--glass-bg); backdrop-filter: blur(10px); border: var(--glass-border); color: var(--text-primary);'; ?>">Este Mes</a>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card green">
                <h3>Total Recaudado (<?php echo ucfirst($filtro); ?>)</h3>
                <div class="amount">$0.00 MXN</div>
            </div>
            <div class="card">
                <h3>Donativos en Línea (Tarjetas)</h3>
                <div class="amount">0</div>
            </div>
            <div class="card">
                <h3>Donativos Manuales (Cheque/Transf.)</h3>
                <div class="amount">0</div>
            </div>
        </div>

        <!-- Formulario Registro Manual -->
        <div class="form-container" style="border-left: 6px solid var(--accent-green);">
            <h3 style="margin-bottom: 25px; color: var(--accent-green); font-size: 1.2rem; font-weight: 700;">Registrar Donativo Manual (Cheques, Transferencias, Efectivo)</h3>
            <form action="" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Tipo de Donativo</label>
                        <select name="tipo" class="form-control" required>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="cheque">Cheque Físico/Correo</option>
                            <option value="efectivo">Efectivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Monto (MXN)</label>
                        <input type="number" step="0.01" name="monto" class="form-control" required placeholder="Ej: 1500.00">
                    </div>
                    <div class="form-group">
                        <label>Número de Autorización / Folio (Si aplica)</label>
                        <input type="text" name="autorizacion" class="form-control" placeholder="Ej: TXN-982374">
                    </div>
                    <div class="form-group">
                        <label>Fecha de Recibo</label>
                        <input type="date" name="fecha" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <h4 style="margin-bottom: 10px; margin-top: 15px; color: var(--text-primary); font-weight: 700; font-size: 1.1rem;">Datos para Deducibilidad (SAT)</h4>
                        <div style="height: 1px; background: rgba(0,0,0,0.1); margin-bottom: 15px;"></div>
                    </div>

                    <div class="form-group">
                        <label>Nombre Completo / Razón Social</label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Nombre del donante">
                    </div>
                    <div class="form-group">
                        <label>RFC (Opcional)</label>
                        <input type="text" name="rfc" class="form-control" placeholder="Ej: XAXX010101000">
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico (Para enviar comprobante)</label>
                        <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com">
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; padding-top: 30px;">
                        <input type="checkbox" name="requiere_factura" id="factura" style="margin-right: 10px; width: 20px; height: 20px; accent-color: var(--accent-green);">
                        <label for="factura" style="margin: 0; cursor: pointer;">El donante solicitó recibo deducible</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success" style="margin-top: 20px;">Registrar en Contabilidad</button>
            </form>
        </div>

        <!-- Tabla Histórica -->
        <div class="data-table-wrapper">
            <div style="padding: 20px 25px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: var(--text-primary); font-size: 1.2rem; font-weight: 700;">Historial de Donativos (<?php echo ucfirst($filtro); ?>)</h3>
                <button class="btn btn-primary" style="padding: 10px 20px; font-size: 0.9rem; background: var(--text-secondary);">⬇ Exportar a Excel (.csv)</button>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Donante / RFC</th>
                        <th>Autorización</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($pdo)): ?>
                        <?php 
                        // Aquí iría la lógica SQL filtrada por fechas ($filtro)
                        // Por ahora mostramos vacío si no hay data
                        ?>
                    <?php else: ?>
                        <!-- Mock Data Demo -->
                        <tr>
                            <td style="font-weight: 500; color: var(--text-secondary);"><?php echo date('d/m/Y'); ?></td>
                            <td><span class="badge badge-card">Tarjeta en Línea</span></td>
                            <td style="font-weight: 600;">Anónimo<br><small style="color: var(--text-secondary); font-weight: normal;">Sin RFC</small></td>
                            <td style="font-family: monospace;">STRIPE-ch_12345</td>
                            <td style="font-weight: 800; color: var(--accent-green); font-size: 1.1rem;">$500.00 MXN</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 500; color: var(--text-secondary);"><?php echo date('d/m/Y', strtotime('-2 days')); ?></td>
                            <td><span class="badge badge-transfer">Transferencia</span></td>
                            <td style="font-weight: 600;">Empresa S.A. de C.V.<br><small style="color: var(--text-secondary); font-weight: normal;">EMP123456789</small></td>
                            <td style="font-family: monospace;">SPEI-998877</td>
                            <td style="font-weight: 800; color: var(--accent-green); font-size: 1.1rem;">$5,000.00 MXN</td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                <em style="font-size: 1.1rem;">Modo de demostración visual. (Base de datos MySQL no conectada).</em>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
