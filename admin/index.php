<?php
session_start();
require_once 'config/database.php';

// Si ya está logueado, redirigir a noticias
if(isset($_SESSION['admin_id'])) {
    header("Location: noticias.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // En producción $pdo debería estar instanciado en config/database.php
        if (isset($pdo)) {
            $stmt = $pdo->prepare("SELECT id, password_hash, role FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Usamos password_verify que es el estándar más seguro (BCRYPT / ARGON2I)
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_role'] = $user['role'];
                header("Location: noticias.php");
                exit();
            } else {
                $error = "Usuario o contraseña incorrectos.";
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
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body class="login-body">

    <div class="login-box">
        <!-- Puedes usar tu logo.glb pero como es el panel de admin, una simple imagen o texto es más rápido -->
        <h2 style="color: var(--accent-purple); margin-bottom: 5px;">Manos Inclusivas</h2>
        <p style="color: var(--text-secondary); margin-bottom: 25px;">Panel de Administración</p>

        <?php if($error): ?>
            <div class="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group" style="text-align: left;">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" class="form-control" required autocomplete="username">
            </div>
            <div class="form-group" style="text-align: left;">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Entrar al Panel</button>
        </form>
    </div>

</body>
</html>
