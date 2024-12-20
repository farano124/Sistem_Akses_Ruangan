<?php

session_start();

include '../proses/koneksi.php';

if ($_SESSION['role'] != 'mahasiswa') {

header('Location: ../login.php');

exit;

}

// Variabel untuk notifikasi

$notif = "";

// Ambil data ruangan dan jadwal

$query_ruangan = "SELECT * FROM ruangan";
$result_ruangan = $koneksi->query($query_ruangan);
$ruangan = $result_ruangan->fetch_all(MYSQLI_ASSOC);

// Proses logika minta akses sementara

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'minta_akses') {
        // Proses meminta akses ruangan
        $id_ruangan = $_POST['id_ruangan'] ?? '';
        if (empty($id_ruangan)) {
            $notif = "Pilih ruangan terlebih dahulu!";
        } else {
            // Ambil ID mahasiswa dari sesi
            $mahasiswa_id = $_SESSION['id']; // Pastikan ID mahasiswa ada di sesi
            // Simpan permintaan akses ke database dengan tipe akses 'sementara'
            $tipe_akses = 'sementara'; // Tipe akses hanya 'sementara'
            $insert_query = "INSERT INTO permintaan_akses (mahasiswa_id, ruangan_id, tipe_akses) VALUES (?, ?, ?)";
            $stmt = $koneksi->prepare($insert_query);
            $stmt->bind_param('iis', $mahasiswa_id, $id_ruangan, $tipe_akses);
            $stmt->execute();
            $notif = "Permintaan akses berhasil dikirim!";
        }
    }
}

// Ambil data ruangan yang disetujui
$query_akses_diterima = "
    SELECT r.nama_ruangan, r.jenis, r.kapasitas
    FROM permintaan_akses pa
    JOIN ruangan r ON pa.ruangan_id = r.id
    WHERE pa.mahasiswa_id = ? AND pa.status = 'diterima'
";
$stmt = $koneksi->prepare($query_akses_diterima);
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$result_akses_diterima = $stmt->get_result();
$ruangan_diterima = $result_akses_diterima->fetch_all(MYSQLI_ASSOC);
// Tampilkan halaman
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0;">
    <header style="background-color: #007bff; color: white; padding: 20px; text-align: center; position: relative;">
        <h1 style="margin: 0; font-size: 24px;">Dashboard Mahasiswa</h1>
        <?php if ($notif): ?>
        <p style="color: green;"><?= $notif; ?></p>
        <?php endif; ?>
        <nav>
            <a href="../proses/logout.php"style="position: absolute; right: 20px; top: 20px; background-color: #ff4d4d; color: white; border: none; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 14px;">Logout</a>
        </nav>
    </header>
    <!-- Daftar Ruangan -->
    <div style="background-color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-top: 20px; padding: 20px;">
    <h2 style="margin: 0 0 10px 0; color: #007bff;">Ruangan yang Dapat Diakses</h2>
    <?php if (empty($ruangan)): ?>
        <p style="font-size: 14px; color: #666;">Tidak ada ruangan tersedia.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" border="1">
            <tr>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Nama Ruangan</th>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Jenis</th>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Kapasitas</th>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Aksi</th>
            </tr>
            <?php foreach ($ruangan as $r): ?>
                <tr>
                    <td style="background-color: #f9f9f9; padding: 10px;"><?= $r['nama_ruangan']; ?></td>
                    <td style="background-color: #f9f9f9; padding: 10px;"><?= $r['jenis']; ?></td>
                    <td style="background-color: #f9f9f9; padding: 10px;"><?= $r['kapasitas']; ?></td>
                    <td style="background-color: #f9f9f9; padding: 10px;">
                        
                        <form action="" method="POST">
                            <input type="hidden" name="action" value="minta_akses">
                            <input type="hidden" name="id_ruangan" value="<?= $r['id']; ?>">
                            <input type="hidden" name="tipe_akses" value="sementara"> <!-- Tipe akses tetap 'sementara' -->
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin meminta akses ruangan ini?');" style="background-color: #ff4d4d; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">Minta Akses Sementara</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>Ruangan yang Disetujui</h2>
<?php if (empty($ruangan_diterima)): ?>
    <p style="font-size: 14px; color: #666;">Tidak ada ruangan yang disetujui.</p>
<?php else: ?>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" border="1">
        <tr>
            <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Nama Ruangan</th>
            <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Jenis</th>
            <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Kapasitas</th>
        </tr>
        <?php foreach ($ruangan_diterima as $r): ?>
            <tr>
                <td style="background-color: #f9f9f9; padding: 10px;"><?= htmlspecialchars($r['nama_ruangan']); ?></td>
                <td style="background-color: #f9f9f9; padding: 10px;"><?= htmlspecialchars($r['jenis']); ?></td>
                <td style="background-color: #f9f9f9; padding: 10px;"><?= htmlspecialchars($r['kapasitas']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>