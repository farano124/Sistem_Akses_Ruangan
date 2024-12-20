<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $ruangan_id = $_POST['ruangan_id'] ?? '';

    if ($action === 'tambah_jadwal') {
        // Ambil dan inisialisasi variabel
        $ruangan_id = $_POST['ruanganId'] ?? ''; // Pastikan ini diambil dari input yang sesuai
        $tanggal = $_POST['tanggal'] ?? '';
        $jam_mulai = $_POST['jam_mulai'] ?? '';
        $jam_selesai = $_POST['jam_selesai'] ?? '';
    
        // Validasi input
        if (empty($ruangan_id) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai)) {
            $_SESSION['notif'] = "Semua field jadwal harus diisi!";
            header('Location: ../dashboard/admin.php');
            exit;
        }
    
        // Cek bentrokan jadwal
        $stmt_check = $koneksi->prepare("SELECT * FROM jadwal_ruangan WHERE tanggal = ? AND ruangan_id = ? AND ((jam_mulai BETWEEN ? AND ?) OR (jam_selesai BETWEEN ? AND ?))");
        $stmt_check->bind_param("siisss", $tanggal, $ruangan_id, $jam_mulai, $jam_selesai, $jam_mulai, $jam_selesai);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
    
        if ($result_check->num_rows > 0) {
            $_SESSION['notif'] = "Jadwal bentrok dengan jadwal yang sudah ada!";
            $stmt_check->close();
            header('Location: ../dashboard/admin.php');
            exit;
        } 
    
        // Dapatkan nama hari
        $hari = date('l', strtotime($tanggal)); 
    
        // Insert jadwal ruangan
        $stmt_jadwal = $koneksi->prepare("INSERT INTO jadwal_ruangan (ruangan_id, hari, tanggal, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?, ?)");
        $stmt_jadwal->bind_param("issss", $ruangan_id, $hari, $tanggal, $jam_mulai, $jam_selesai);
        
        if ($stmt_jadwal->execute()) {
            $_SESSION['notif'] = "Jadwal berhasil ditambahkan!";
        } else {
            $_SESSION['notif'] = "Gagal menambahkan jadwal: " . $stmt_jadwal->error;
        }
        
        $stmt_jadwal->close();
        $stmt_check->close(); // Tutup statement check
    
        header('Location: ../dashboard/admin.php');
        exit;

    } elseif ($action === 'edit_jadwal') {
        $id = $_POST['id'];
        $tanggal = $_POST['tanggal'];
        $jam_mulai = $_POST['jam_mulai'];
        $jam_selesai = $_POST['jam_selesai'];
    
        // Validasi input dan update jadwal di database
        $stmt = $koneksi->prepare("UPDATE jadwal_ruangan SET tanggal = ?, jam_mulai = ?, jam_selesai = ? WHERE id = ?");
        $stmt->bind_param("sssi", $tanggal, $jam_mulai, $jam_selesai, $id);
    
        if ($stmt->execute()) {
            $_SESSION['notif'] = "Jadwal berhasil diperbarui!";
        } else {
            $_SESSION['notif'] = "Gagal memperbarui jadwal: " . $stmt->error;
        }
    
        $stmt->close();
        header('Location: ../dashboard/admin.php');
        exit;
    } elseif ($action === 'hapus_jadwal') {
        $id = $_POST['id'] ?? '';
        if (!empty($id)) {
            $stmt_hapus = $koneksi->prepare("DELETE FROM jadwal_ruangan WHERE id = ?");
            if ($stmt_hapus) {
                $stmt_hapus->bind_param("i", $id);
                $stmt_hapus->execute();
                $stmt_hapus->close();
                $_SESSION['notif'] = "Jadwal berhasil dihapus!";
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