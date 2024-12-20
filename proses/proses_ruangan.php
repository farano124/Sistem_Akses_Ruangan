<?php
include 'koneksi.php'; // Pastikan path ini benar

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'buat_ruangan') {
        $nama_ruangan = $_POST['nama_ruangan'] ?? '';
        $jenis = $_POST['jenis'] ?? '';
        $kapasitas = $_POST['kapasitas'] ?? '';

        // Validasi input
        if (empty($nama_ruangan) || empty($jenis) || empty($kapasitas)) {
            $_SESSION['notif'] = "Semua field ruangan harus diisi!";
            header('Location: ../dashboard/admin.php');
            exit;
        } elseif ($kapasitas <= 0) {
            $_SESSION['notif'] = "Kapasitas harus lebih dari 0!";
            header('Location: ../dashboard/admin.php');
            exit;
        } else {
            // Insert ruangan
            $stmt = $koneksi->prepare("INSERT INTO ruangan (nama_ruangan, jenis, kapasitas) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $nama_ruangan, $jenis, $kapasitas);
            $stmt->execute();
            $stmt->close();
            $_SESSION['notif'] = "Ruangan berhasil dibuat!";
        }

        header('Location: ../dashboard/admin.php');
        exit;

    } elseif ($action === 'edit_ruangan') {
        $id = $_POST['id'] ?? '';
        $nama_ruangan = $_POST['nama_ruangan'] ?? '';
        $jenis = $_POST['jenis'] ?? '';
        $kapasitas = $_POST['kapasitas'] ?? '';

        if (!empty($id) && !empty($nama_ruangan) && !empty($jenis) && !empty($kapasitas)) {
            $stmt = $koneksi->prepare("UPDATE ruangan SET nama_ruangan = ?, jenis = ?, kapasitas = ? WHERE id = ?");
            $stmt->bind_param("ssii", $nama_ruangan, $jenis, $kapasitas, $id);
            $stmt->execute();
            $stmt->close();
            $_SESSION['notif'] = "Ruangan berhasil diperbarui!";
        }

        header('Location: ../dashboard/admin.php');
        exit;

    } elseif ($action === 'hapus_ruangan') {
        $id = $_POST['id'] ?? '';
        if (!empty($id)) {
            $stmt_hapus = $koneksi->prepare("DELETE FROM ruangan WHERE id = ?");
            if ($stmt_hapus) {
                $stmt_hapus->bind_param("i", $id);
                $stmt_hapus->execute();
                $stmt_hapus->close();
                $_SESSION['notif'] = "Ruangan berhasil dihapus!";
            }
        }

        header('Location: ../dashboard/admin.php');
        exit;
    } 
    
        header('Location: ../dashboard/admin.php'); // Ganti dengan halaman yang sesuai
        exit;
    }

    if (isset($_SESSION['notif'])) {
        echo $_SESSION['notif'];
        unset($_SESSION['notif']);
    }
?>