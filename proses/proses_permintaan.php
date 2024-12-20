<?php
include 'koneksi.php';

    // Proses mengubah status permintaan akses
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        $id_permintaan = $_POST['id_permintaan'] ?? '';
        $status = $_POST['status'] ?? '';

        if ($action === 'ubah_status' && !empty($id_permintaan) && !empty($status)) {
            // Update status permintaan akses
            $update_query = "UPDATE permintaan_akses SET status = ? WHERE id = ?";
            $stmt = $koneksi->prepare($update_query);
            
            if ($stmt) {
                $stmt->bind_param('si', $status, $id_permintaan);
                if ($stmt->execute()) {
                    $_SESSION['notif'] = "Status permintaan akses berhasil diperbarui!";
                } else {
                    $_SESSION['notif'] = "Terjadi kesalahan saat mengubah status: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['notif'] = "Kesalahan dalam persiapan statement.";
            }

            // Redirect ke halaman admin setelah mengubah status
            header('Location: ../dashboard/admin.php');
            exit;
        } else {
            $_SESSION['notif'] = "ID permintaan atau status tidak valid.";
            header('Location: ../dashboard/admin.php');
            exit;
        }
    }
        
        // Ambil semua permintaan akses dari database
        $query_permintaan = "SELECT pa.*, u.nama_lengkap, r.nama_ruangan FROM permintaan_akses pa
                            JOIN users u ON pa.mahasiswa_id = u.id
                            JOIN ruangan r ON pa.ruangan_id = r.id";

        $result_permintaan = $koneksi->query($query_permintaan);
        if ($result_permintaan) {
            $permintaan_akses = $result_permintaan->fetch_all(MYSQLI_ASSOC);
        } else {
            $_SESSION['notif'] = "Gagal mengambil data permintaan akses: " . $koneksi->error;
        }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_permintaan = $_POST['id_permintaan'] ?? '';
    
        if (!empty($id_permintaan)) {
            $stmt = $koneksi->prepare("DELETE FROM permintaan_akses WHERE id = ?");
            $stmt->bind_param("i", $id_permintaan);
            $stmt->execute();
            $stmt->close();
            
            // Notifikasi atau redirect
            session_start();
            $_SESSION['notif'] = "Permintaan akses berhasil dihapus!";
            header('Location: ../admin.php');
            exit;
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_permintaan = $_POST['id_permintaan'] ?? '';

    if (!empty($id_permintaan)) {
        $stmt = $koneksi->prepare("DELETE FROM permintaan_akses WHERE id = ?");
        $stmt->bind_param("i", $id_permintaan);
        $stmt->execute();
        $stmt->close();
        
        // Notifikasi atau redirect
        session_start();
        $_SESSION['notif'] = "Permintaan akses berhasil dihapus!";
        header('Location: ..dashboard/admin.php');
        exit;
    }
}
?>