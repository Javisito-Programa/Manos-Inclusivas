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

// AUTO-MIGRATION: Crear nueva tabla de transacciones robusta (SAT/BBVA)
try {
    // Purgar tabla antigua temporalmente para desarrollo (solo porque el user lo aprobó)
    // En producción ya no se debe hacer DROP TABLE.
    $pdo->exec("DROP TABLE IF EXISTS transacciones");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS transacciones (
        id_donacion VARCHAR(36) PRIMARY KEY,
        transaccion_id_openpay VARCHAR(100) NULL,
        numero_autorizacion VARCHAR(100) NULL,
        fecha_hora_cobro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        monto_bruto DECIMAL(10,2) NOT NULL,
        comision_pasarela DECIMAL(10,2) DEFAULT 0.00,
        monto_neto DECIMAL(10,2) NOT NULL,
        moneda VARCHAR(10) DEFAULT 'MXN',
        metodo_pago_marca VARCHAR(50) NOT NULL,
        ultimos_4_digitos_tarjeta VARCHAR(4) NULL,
        tipo_donacion VARCHAR(50) DEFAULT 'Unica',
        estatus_pago VARCHAR(50) DEFAULT 'Completado',
        datos_donante TEXT NULL,
        requiere_factura TINYINT(1) DEFAULT 0,
        datos_fiscales_json TEXT NULL,
        estatus_cfdi VARCHAR(50) NULL
    )");
} catch (PDOException $e) {
    error_log("Error creando tabla transacciones: " . $e->getMessage());
}

// Filtro de fechas (Día, Semana, Mes) o Custom
$filtro = $_GET['filtro'] ?? 'mes';
$where_clause = "1=1";
if ($filtro == 'dia') {
    $where_clause = "DATE(fecha_hora_cobro) = CURDATE()";
} elseif ($filtro == 'semana') {
    $where_clause = "YEARWEEK(fecha_hora_cobro, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filtro == 'mes') {
    $where_clause = "MONTH(fecha_hora_cobro) = MONTH(CURDATE()) AND YEAR(fecha_hora_cobro) = YEAR(CURDATE())";
}

// Procesar nuevo donativo manual
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['monto'])) {
    $metodo = htmlspecialchars($_POST['tipo']);
    $monto = floatval($_POST['monto']);
    $autorizacion = htmlspecialchars(trim($_POST['autorizacion']));
    $fecha = htmlspecialchars($_POST['fecha']); // YYYY-MM-DD
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $rfc = htmlspecialchars(trim($_POST['rfc']));
    $email = htmlspecialchars(trim($_POST['email']));
    $requiere_factura = isset($_POST['requiere_factura']) ? 1 : 0;
    
    $id_donacion = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    $datos_donante = json_encode(['name' => $name, 'email' => $email]);
    $datos_fiscales = null;
    if ($requiere_factura && $rfc) {
        $datos_fiscales = json_encode(['rfc' => $rfc]); // Para manual solo pedíamos RFC antes
    }

    $stmt = $pdo->prepare("INSERT INTO transacciones (id_donacion, monto_bruto, monto_neto, numero_autorizacion, metodo_pago_marca, datos_donante, requiere_factura, datos_fiscales_json, fecha_hora_cobro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$id_donacion, $monto, $monto, $autorizacion, $metodo, $datos_donante, $requiere_factura, $datos_fiscales, $fecha . ' 00:00:00'])) {
        $mensaje = "¡Donativo registrado correctamente!";
    } else {
        $mensaje = "Error al registrar el donativo.";
    }
}

// Obtener totales para los KPIs (basado en monto neto o bruto según se decida. Usaremos monto_bruto para el KPI visual)
$stmt_total = $pdo->query("SELECT SUM(monto_bruto) FROM transacciones WHERE estatus_pago = 'Completado' AND $where_clause");
$total_recaudado = $stmt_total->fetchColumn() ?: 0.00;

$stmt_online = $pdo->query("SELECT COUNT(*) FROM transacciones WHERE metodo_pago_marca = 'Openpay' AND estatus_pago = 'Completado' AND $where_clause");
$total_online = $stmt_online->fetchColumn() ?: 0;

$stmt_manual = $pdo->query("SELECT COUNT(*) FROM transacciones WHERE metodo_pago_marca != 'Openpay' AND estatus_pago = 'Completado' AND $where_clause");
$total_manual = $stmt_manual->fetchColumn() ?: 0;

// Obtener filas
$stmt_filas = $pdo->query("SELECT * FROM transacciones WHERE $where_clause ORDER BY fecha_hora_cobro DESC LIMIT 50");
$transacciones = $stmt_filas->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas - Administración</title>
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
    <style>
        .badge-bbva { background: #004481; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
        .badge-transfer { background: #38A169; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
        .badge-manual { background: #D69E2E; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
        
        .premium-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }
    </style>
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
        <div class="sidebar-header" style="text-align: center; margin-bottom: 20px;">
            <img src="https://miic-neurodesarrollo.org/img/Logo%20circular.webp" alt="Logo" style="width: 60px; height: 60px; border-radius: 50%; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); background: white; padding: 2px;">
            <h2 style="font-size: 1.2rem; font-weight: 700; margin: 0; color: #1f2937;">Panel Admin</h2>
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
                <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Administrador'); ?></span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>

        <?php if($mensaje): ?>
        <div style="background: #C6F6D5; color: #22543D; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
            ✅ <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>

        <!-- Filtros rápidos -->
        <div style="margin-bottom: 30px; display: flex; gap: 15px;">
            <a href="?filtro=dia" class="btn <?php echo $filtro=='dia' ? 'btn-success' : 'btn-logout'; ?>" style="<?php echo $filtro=='dia' ? '' : 'background: var(--glass-bg); backdrop-filter: blur(10px); border: var(--glass-border); color: var(--text-primary);'; ?>">Hoy</a>
            <a href="?filtro=semana" class="btn <?php echo $filtro=='semana' ? 'btn-success' : 'btn-logout'; ?>" style="<?php echo $filtro=='semana' ? '' : 'background: var(--glass-bg); backdrop-filter: blur(10px); border: var(--glass-border); color: var(--text-primary);'; ?>">Esta Semana</a>
            <a href="?filtro=mes" class="btn <?php echo $filtro=='mes' ? 'btn-success' : 'btn-logout'; ?>" style="<?php echo $filtro=='mes' ? '' : 'background: var(--glass-bg); backdrop-filter: blur(10px); border: var(--glass-border); color: var(--text-primary);'; ?>">Este Mes</a>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card green premium-card">
                <h3>Total Recaudado (<?php echo ucfirst($filtro); ?>)</h3>
                <div class="amount">$<?php echo number_format($total_recaudado, 2); ?> MXN</div>
            </div>
            <div class="card premium-card">
                <h3>Donativos en Línea (BBVA)</h3>
                <div class="amount"><?php echo $total_online; ?></div>
            </div>
            <div class="card premium-card">
                <h3>Donativos Manuales</h3>
                <div class="amount"><?php echo $total_manual; ?></div>
            </div>
        </div>

        <!-- Formulario Registro Manual -->
        <div class="form-container premium-card" style="border-left: 6px solid var(--accent-green); margin-bottom: 40px;">
            <h3 style="margin-bottom: 25px; color: var(--accent-green); font-size: 1.2rem; font-weight: 700;">Registrar Donativo Manual / Efectivo</h3>
            <form action="" method="POST">
                <div class="responsive-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Tipo de Donativo</label>
                        <select name="tipo" class="form-control" required>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="cheque">Cheque Físico/Correo</option>
                            <option value="efectivo">Efectivo</option>
                            <!-- Nota: Tarjeta en Línea entra por API automáticamente, pero se deja por si requiere registro manual excepcional -->
                            <option value="tarjeta_en_linea">Tarjeta en Línea (Registro Excepcional)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Monto (MXN)</label>
                        <input type="number" step="0.01" name="monto" class="form-control" required placeholder="Ej: 1500.00">
                    </div>
                    <div class="form-group">
                        <label>Auth / Folio / Referencia</label>
                        <input type="text" name="autorizacion" class="form-control" placeholder="Ej: TXN-982374 o Auth BBVA">
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
        <div class="data-table-wrapper premium-card">
            <div style="padding: 20px 25px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: var(--text-primary); font-size: 1.2rem; font-weight: 700;">Historial de Donativos (<?php echo ucfirst($filtro); ?>)</h3>
                <button class="btn btn-primary" style="padding: 10px 20px; font-size: 0.9rem; background: var(--text-secondary);" onclick="window.print();">⬇ Exportar a PDF / Imprimir</button>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.02);">
                            <th style="padding: 15px; text-align: left;">Fecha / Status</th>
                            <th style="padding: 15px; text-align: left;">Tipo / Pasarela</th>
                            <th style="padding: 15px; text-align: left;">Donante</th>
                            <th style="padding: 15px; text-align: left;">Detalles SAT (CFDI)</th>
                            <th style="padding: 15px; text-align: right;">Montos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($transacciones) > 0): ?>
                            <?php foreach($transacciones as $t): 
                                $donante = json_decode($t['datos_donante'], true);
                                $fiscal = json_decode($t['datos_fiscales_json'], true);
                                $nombre = $donante['name'] ?? 'N/A';
                                $email = $donante['email'] ?? 'N/A';
                                $rfc = $fiscal['rfc'] ?? 'Sin RFC';
                            ?>
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <td style="padding: 15px; font-weight: 500; color: var(--text-secondary);">
                                    <?php echo date('d/m/Y H:i', strtotime($t['fecha_hora_cobro'])); ?><br>
                                    <small style="color: <?php echo $t['estatus_pago'] == 'Completado' ? 'green' : 'orange'; ?>;"><?php echo htmlspecialchars($t['estatus_pago']); ?></small>
                                </td>
                                <td style="padding: 15px;">
                                    <?php if(strtolower($t['metodo_pago_marca']) == 'openpay'): ?>
                                        <span class="badge-bbva">💳 BBVA / Openpay</span><br>
                                        <small style="color:gray;">Auth: <?php echo htmlspecialchars($t['numero_autorizacion'] ?: 'N/A'); ?></small>
                                    <?php elseif($t['metodo_pago_marca'] == 'transferencia'): ?>
                                        <span class="badge-transfer">🏦 Transferencia</span>
                                    <?php else: ?>
                                        <span class="badge-manual">📝 <?php echo ucfirst($t['metodo_pago_marca']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; font-weight: 600;">
                                    <?php echo htmlspecialchars($nombre); ?><br>
                                    <small style="color: var(--text-secondary); font-weight: normal;"><?php echo htmlspecialchars($email); ?></small>
                                </td>
                                <td style="padding: 15px; font-size: 0.9rem; color: #4A5568;">
                                    <strong>RFC:</strong> <?php echo htmlspecialchars($rfc); ?><br>
                                    <?php if($t['requiere_factura']): ?>
                                        <span style="display: inline-block; background: #EBF8FF; color: #2B6CB0; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; margin-top: 5px;">CFDI: <?php echo htmlspecialchars($t['estatus_cfdi'] ?: 'Pendiente'); ?></span>
                                    <?php else: ?>
                                        <span style="font-size: 0.8rem; color: #a0aec0;">No requerido</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; text-align: right;">
                                    <div style="font-size: 0.85rem; color: gray;">Bruto: $<?php echo number_format($t['monto_bruto'], 2); ?></div>
                                    <div style="font-size: 0.85rem; color: #E53E3E;">Comisión: -$<?php echo number_format($t['comision_pasarela'], 2); ?></div>
                                    <div style="font-weight: 800; color: var(--accent-green); font-size: 1.1rem; margin-top: 4px;">
                                        $<?php echo number_format($t['monto_neto'], 2); ?> <?php echo $t['moneda']; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 40px;">
                                    <em style="font-size: 1.1rem;">No hay donativos registrados en este periodo.</em>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>

