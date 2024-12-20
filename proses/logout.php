<?php
session_start();
session_destroy(); // Menghapus semua data sesi
header('Location: ../login.php'); // Redirect ke halaman login
exit;
?>
