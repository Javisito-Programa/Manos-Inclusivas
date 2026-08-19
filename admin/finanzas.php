<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
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
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Panel Admin</h2>
        </div>
        <ul class="nav-links">
            <li><a href="noticias.php"><span class="nav-icon">📰</span> Noticias</a></li>
            <li><a href="finanzas.php" class="active"><span class="nav-icon">💰</span> Finanzas</a></li>
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
        <div style="margin-bottom: 20px;">
            <a href="?filtro=dia" class="btn <?php echo $filtro=='dia' ? 'btn-success' : 'btn-logout'; ?>" style="margin-right: 10px;">Hoy</a>
            <a href="?filtro=semana" class="btn <?php echo $filtro=='semana' ? 'btn-success' : 'btn-logout'; ?>" style="margin-right: 10px;">Esta Semana</a>
            <a href="?filtro=mes" class="btn <?php echo $filtro=='mes' ? 'btn-success' : 'btn-logout'; ?>">Este Mes</a>
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
        <div class="form-container" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
            <h3 style="margin-bottom: 20px; color: var(--accent-green);">Registrar Donativo Manual (Cheques, Transferencias, Efectivo)</h3>
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
                        <h4 style="margin-bottom: 10px; margin-top: 10px; color: var(--text-secondary);">Datos para Deducibilidad (SAT)</h4>
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
                        <input type="checkbox" name="requiere_factura" id="factura" style="margin-right: 10px; width: 20px; height: 20px;">
                        <label for="factura" style="margin: 0; cursor: pointer;">El donante solicitó recibo deducible</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success" style="margin-top: 20px;">Registrar en Contabilidad</button>
            </form>
        </div>

        <!-- Tabla Histórica -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: var(--text-primary);">Historial de Donativos (<?php echo ucfirst($filtro); ?>)</h3>
                <button class="btn btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">⬇ Exportar a Excel (.csv)</button>
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
                            <td><?php echo date('d/m/Y'); ?></td>
                            <td><span class="badge badge-card">Tarjeta en Línea</span></td>
                            <td>Anónimo<br><small style="color: gray;">Sin RFC</small></td>
                            <td>STRIPE-ch_12345</td>
                            <td style="font-weight: bold; color: var(--accent-green);">$500.00 MXN</td>
                        </tr>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime('-2 days')); ?></td>
                            <td><span class="badge badge-transfer">Transferencia</span></td>
                            <td>Empresa S.A. de C.V.<br><small style="color: gray;">EMP123456789</small></td>
                            <td>SPEI-998877</td>
                            <td style="font-weight: bold; color: var(--accent-green);">$5,000.00 MXN</td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); background: #fdfbf7;">
                                <em>Modo de demostración visual. (Base de datos MySQL no conectada).</em>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
