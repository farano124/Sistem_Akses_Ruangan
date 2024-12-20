<?php
session_start();

include '../proses/koneksi.php';

if ($_SESSION['role'] != 'dosen') {
    header('Location: ../login.php');
    exit;
}
if (!isset($_SESSION['id'])) {
    die("ID mahasiswa tidak ditemukan. Silakan login kembali.");
}

// Variabel untuk notifikasi
$notif = "";

// Ambil data ruangan dan jadwal
$query_ruangan = "SELECT * FROM ruangan";
$result_ruangan = $koneksi->query($query_ruangan);
$ruangan = $result_ruangan->fetch_all(MYSQLI_ASSOC);

// Ambil jadwal ruangan
$query_jadwal = "SELECT * FROM jadwal_ruangan";
$result_jadwal = $koneksi->query($query_jadwal);
$jadwal = $result_jadwal->fetch_all(MYSQLI_ASSOC);

// Ambil permintaan akses yang disetujui beserta jadwalnya
$query_akses_diterima = "
    SELECT r.nama_ruangan, r.jenis, r.kapasitas, j.hari, j.jam_mulai, j.jam_selesai 
    FROM permintaan_akses pa
    JOIN ruangan r ON pa.ruangan_id = r.id
    JOIN jadwal_ruangan j ON pa.jadwal_id = j.id
    WHERE pa.mahasiswa_id = ? AND pa.status = 'diterima'
";
$stmt = $koneksi->prepare($query_akses_diterima);
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$result_akses_diterima = $stmt->get_result();
$ruangan_diterima = $result_akses_diterima->fetch_all(MYSQLI_ASSOC);

// Proses logika berdasarkan `action`
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'minta_akses') {
        // Proses meminta akses ruangan
        $id_ruangan = $_POST['id_ruangan'] ?? '';
        $tipe_akses = $_POST['tipe_akses'] ?? '';
        $jadwal_id = $_POST['jadwal_id'] ?? ''; // Ambil ID jadwal jika diperlukan

        if (empty($id_ruangan) || empty($tipe_akses)) {
            $notif = "Pilih ruangan dan tipe akses terlebih dahulu!";
        } else {
            // Ambil ID mahasiswa dari sesi
            $mahasiswa_id = $_SESSION['id']; // Pastikan ID mahasiswa ada di sesi

            // Simpan permintaan akses ke database
            $insert_query = "INSERT INTO permintaan_akses (mahasiswa_id, ruangan_id, tipe_akses, jadwal_id) VALUES (?, ?, ?, ?)";
            $stmt = $koneksi->prepare($insert_query);
            $stmt->bind_param('iisi', $mahasiswa_id, $id_ruangan, $tipe_akses, $jadwal_id);
            $stmt->execute();

            $notif = "Permintaan akses berhasil dikirim!";
        }
    }
}

if (isset($_SESSION['notif'])) {
    echo $_SESSION['notif'];
    unset($_SESSION['notif']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        h2 {
            margin: 0 0 10px 0;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            background-color: #f9f9f9;
            padding: 10px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .form-inline {
            display: inline;
        }
        .form-inline select {
            margin-left: 10px;
            padding: 5px;
        }
    </style>
</head>

<body>
    <header>
        <h1 style="margin: 0; font-size: 24px;">Dashboard Dosen</h1>
        <?php if ($notif): ?>
            <p style="color: green;"><?= $notif; ?></p>
        <?php endif; ?>
        
        <nav>
            <a href="../proses/logout.php" style="position: absolute; right: 20px; top: 20px; background-color: #ff4d4d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 14px;">Logout</a>
        </nav>
    </header>

    <!-- Daftar Ruangan -->
    <div style="background-color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-top: 20px; padding: 20px;">
        <h2>Ruangan yang Dapat Diakses</h2>
        <?php if (empty($ruangan)): ?>
            <p style="font-size: 14px; color: #666;">Tidak ada ruangan tersedia.</p>
        <?php else: ?>
            <table border="1">
                <tr>
                    <th>Nama Ruangan</th>
                    <th>Jenis</th>
                    <th>Kapasitas</th>
                    <th>Jadwal</th>
                </tr>
                <?php foreach ($ruangan as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nama_ruangan']); ?></td>
                        <td><?= htmlspecialchars($r['jenis']); ?></td>
                        <td><?= htmlspecialchars($r['kapasitas']); ?></td>
                        <td style="text-align: center;">
                            <?php 
                            // Tampilkan jadwal untuk ruangan ini
                            $jadwal_ruangan = array_filter($jadwal, function($j) use ($r) {
                                return $j['ruangan_id'] == $r['id'];
                            });
                            ?>
                            <?php if (empty($jadwal_ruangan)): ?>
                                <p>Tidak ada jadwal.</p>
                            <?php else: ?>
                                <ul style="list-style: none; padding: 0; margin: 0;">
                                    <?php foreach ($jadwal_ruangan as $j): ?>
                                        <li style="margin-bottom: 10px;">
                                            <?= htmlspecialchars($j['hari']) . ': ' . htmlspecialchars($j['jam_mulai']) . ' - ' . htmlspecialchars($j['jam_selesai']); ?>
                                            <form action="" method="POST" class="form-inline">
                                                <input type="hidden" name="action" value="minta_akses">
                                                <input type="hidden" name="id_ruangan" value="<?= $r['id']; ?>">
                                                <input type="hidden" name="jadwal_id" value="<?= $j['id']; ?>">
                                                <select name="tipe_akses" required>
                                                    <option value="" disabled selected>Pilih Akses</option>
                                                    <option value="reguler">Reguler</option>
                                                    <option value="sementara">Sementara</option>
                                                </select>
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin meminta akses ruangan ini?');">Minta Akses</button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

<!-- Daftar Ruangan yang Disetujui -->
<h2>Ruangan yang Disetujui</h2>
<?php if (empty($ruangan_diterima)): ?>
    <p style="font-size: 14px; color: #666;">Tidak ada ruangan yang disetujui.</p>
<?php else: ?>
    <table border="1">
        <tr>
            <th>Nama Ruangan</th>
            <th>Jenis</th>
            <th>Kapasitas</th>
            <th>Jadwal</th>
        </tr>
        <?php foreach ($ruangan_diterima as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['nama_ruangan']); ?></td>
                <td><?= htmlspecialchars($r['jenis']); ?></td>
                <td><?= htmlspecialchars($r['kapasitas']); ?></td>
                <td>
                    <p><?= htmlspecialchars($r['hari']) . ': ' . htmlspecialchars($r['jam_mulai']) . ' - ' . htmlspecialchars($r['jam_selesai']); ?></p>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
    </div>
</body>
</html>
