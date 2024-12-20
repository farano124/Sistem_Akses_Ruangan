<?php
session_start();

include '../proses/koneksi.php'; // Pastikan path ini benar

// Ambil data dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// Query untuk mengambil data pengguna dari database
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);

// Validasi login
if ($result) {
    $user = mysqli_fetch_assoc($result);
    if ($user && $user['password'] === $password) { // Langsung membandingkan password
        $_SESSION['username'] = $username;
        $_SESSION['id'] = $user['id']; // Simpan ID pengguna
        $_SESSION['role'] = $user['role']; // Simpan role pengguna dalam session

        // Redirect sesuai role
        if ($user['role'] == 'admin') {
            header('Location: ../dashboard/admin.php');
        } elseif ($user['role'] == 'dosen') {
            header('Location: ../dashboard/dosen.php');
        } elseif ($user['role'] == 'mahasiswa') {
            header('Location: ../dashboard/mahasiswa.php');
        }
        exit;
    }
}

// Jika login gagal
header('Location: ../login.php?error=1');
exit;
?>