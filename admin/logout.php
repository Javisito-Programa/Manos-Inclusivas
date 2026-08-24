<?php
session_start();
session_unset();
session_destroy();
$errorMsg = isset($_GET['error']) ? '?error=' . urlencode($_GET['error']) : '';
echo "<script>sessionStorage.removeItem('admin_session_active'); window.location.href='index.php' + $errorMsg;</script>";
exit();
?>
