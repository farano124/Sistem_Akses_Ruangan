<?php
session_start();
// Mulai output buffering
ob_start();

error_log('Session role: ' . ($_SESSION['role'] ?? 'not set'));
include '../proses/koneksi.php';
include '../proses/proses_permintaan.php';
include '../proses/proses_ruangan.php';
include '../proses/proses_jadwal.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_SESSION['id'])) {
    die("ID mahasiswa tidak ditemukan. Silakan login kembali.");
    }
// Ambil notifikasi dari session
$notif = $_SESSION['notif'] ?? '';
unset($_SESSION['notif']);

// Ambil semua ruangan dari database
$query = "SELECT * FROM ruangan";
$result = $koneksi->query($query);
$ruangan = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/dashboard_admin.css">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0;">

  <!-- Header -->
  <header style="background-color: #007bff; color: white; padding: 20px; text-align: center; position: relative;">
        <h1 style="margin: 0; font-size: 24px;">Dashboard Admin</h1>
        <a href="../proses/logout.php" style="position: absolute; right: 20px; top: 20px; background-color: #ff4d4d; color: white; border: none; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 14px;">Logout</a>
    </header>

    <!-- Container -->
    <div style="width: 80%; margin: 20px auto;">

<body>
    <h1 style="margin: 20px 0; font-size: 24px;">Dashboard Admin</h1>
    <?php if ($notif): ?>
        <p style="color: green; font-size: 16px; margin: 10px 0;"><?= $notif; ?></p>
    <?php endif; ?>


    <!-- Button to open the modal for creating room -->
    <button id="myBtn" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px;">Buat Ruangan</button>

    <!-- Modal for creating room -->
    <div id="myModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); padding-top: 60px;">
        <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%;">
            <span class="close" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2 style="font-size: 20px; margin-bottom: 20px;">Buat Ruangan</h2>
            <form action="../proses/proses_ruangan.php" method="POST">
                <input type="hidden" name="action" value="buat_ruangan">
                <label style="display: block; margin-bottom: 10px;">Nama Ruangan:</label>
                <input type="text" name="nama_ruangan" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px;">Jenis:</label>
                <select name="jenis" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
                    <option value="kelas">Kelas</option>
                    <option value="labor">Labor</option>
                    <option value="ruang_sidang">Ruang Sidang</option>
                    <option value="aula">Aula</option>
                </select>
                <label style="display: block; margin-bottom: 10px;">Kapasitas:</label>
                <input type="number" name="kapasitas" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin membuat ruangan?');" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Buat Ruangan</button>
            </form>
        </div>
    </div>

<div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); padding-top: 60px;">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%;">
    <span class="close-edit-schedule" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h2 style="font-size: 20px; margin-bottom: 20px;">Edit Ruangan</h2>
        <form action="../proses/proses_ruangan.php" method="POST">
            <input type="hidden" name="action" value="edit_ruangan">
            <input type="hidden" name="id" id="editRuanganId"> <!-- ID ruangan -->
            <label style="display: block; margin-bottom: 10px;">Nama Ruangan:</label>
            <input type="text" name="nama_ruangan" id="editNamaRuangan" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
            
            <label style="display: block; margin-bottom: 10px;">Jenis:</label>
            <select name="jenis" id="editJenis" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
                <option value="kelas">Kelas</option>
                <option value="labor">Labor</option>
                <option value="ruang_sidang">Ruang Sidang</option>
                <option value="aula">Aula</option>
            </select>
            
            <label style="display: block; margin-bottom: 10px;">Kapasitas:</label>
            <input type="number" name="kapasitas" id="editKapasitas" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
            
            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyimpan perubahan ini?');" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Simpan Perubahan</button>
        </form>
    </div>
    </div>

    <div id="addScheduleModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); padding-top: 60px;">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%;">
        <span class="close-add-schedule" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h2 style="font-size: 20px; margin-bottom: 20px;">Tambah Jadwal</h2>
        
        <form action="../proses/proses_jadwal.php" method="POST">
            <input type="hidden" name="action" value="tambah_jadwal">
            <input type="hidden" name="ruanganId" id="ruanganId">
            
            <label style="display: block; margin-bottom: 10px;">Nama Ruangan:</label>
            <span id="ruanganNama" style="font-weight: bold; margin-bottom: 20px;"></span>
            
            <label style="display: block; margin-bottom: 10px;">Tanggal:</label>
            <input type="date" name="tanggal" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
            
            <label style="display: block; margin-bottom: 10px;">Jam Mulai:</label>
            <input type="time" name="jam_mulai" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
            
            <label style="display: block; margin-bottom: 10px;">Jam Selesai:</label>
            <input type="time" name="jam_selesai" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
            
            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin membuat jadwal ini?');" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Simpan Jadwal</button>
        </form>
    </div>
</div>

    <!-- Daftar Ruangan -->
    <div style="background-color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-top: 20px; padding: 20px;">
    <h2 style="margin: 0 0 10px 0; color: #007bff;">Daftar Ruangan</h2>
    <?php if (empty($ruangan)): ?>
        <p style="font-size: 14px; color: #666;">Tidak ada ruangan.</p>
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
                    <td style="background-color: #f9f9f9; padding: 10px; text-align: center;">
                    <button class="editBtn" style="background-color:rgb(0, 94, 255); color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;" data-id="<?= $r['id']; ?>" data-nama="<?= $r['nama_ruangan']; ?>" data-jenis="<?= $r['jenis']; ?>" data-kapasitas="<?= $r['kapasitas']; ?>">Edit</button>
                        <form action="../proses/proses_ruangan.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="hapus_ruangan">
                            <input type="hidden" name="id" value="<?= $r['id']; ?>">
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus ruangan ini?');" style="background-color: #ff4d4d; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">Hapus</button>
                        </form>
                        <button class="addScheduleBtn"  style="background-color:rgb(0, 255, 76); color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;" data-id="<?= $r['id']; ?>" data-nama="<?= $r['nama_ruangan']; ?>">Tambah Jadwal</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php
        // Ambil data jadwal dari database
    $query = "SELECT * FROM jadwal_ruangan";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        // Simpan data ke dalam sesi
        $_SESSION['jadwal_list'] = [];
        while ($row = $result->fetch_assoc()) {
            $_SESSION['jadwal_list'][] = $row;
        }
    } else {
        $_SESSION['jadwal_list'] = []; // Jika tidak ada data
    }

if (isset($_SESSION['notif'])) {
    echo $_SESSION['notif'];
    unset($_SESSION['notif']);
}
?>

<!-- Daftar Jadwal -->
<h2>Daftar Jadwal</h2>
<?php if (isset($_SESSION['jadwal_list']) && !empty($_SESSION['jadwal_list'])): ?>
    <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">ID</th>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Ruangan ID</th>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Tanggal</th>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Jam Mulai</th>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Jam Selesai</th>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Hari</th>
                <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['jadwal_list'] as $jadwal): ?>
                <tr>
                    <td><?= htmlspecialchars($jadwal['id']); ?></td>
                    <td><?= htmlspecialchars($jadwal['ruangan_id']); ?></td>
                    <td><?= htmlspecialchars($jadwal['tanggal']); ?></td>
                    <td><?= htmlspecialchars($jadwal['jam_mulai']); ?></td>
                    <td><?= htmlspecialchars($jadwal['jam_selesai']); ?></td>
                    <td><?= htmlspecialchars($jadwal['hari']); ?></td>
                    <td>
                    <button class="editScheduleBtn" style="background-color:rgb(0, 94, 255); color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;" data-id="<?= htmlspecialchars($jadwal['id']); ?>" data-tanggal="<?= htmlspecialchars($jadwal['tanggal']); ?>" data-jam_mulai="<?= htmlspecialchars($jadwal['jam_mulai']); ?>" data-jam_selesai="<?= htmlspecialchars($jadwal['jam_selesai']); ?>">Edit</button>
                        <form method="POST" action="../proses/proses_jadwal.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($jadwal['id']); ?>">
                            <input type="hidden" name="action" value="hapus_jadwal">
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');" style="background-color:rgb(255, 0, 0); color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Tidak ada jadwal yang tersedia.</p>
<?php endif; ?>

<!-- Modal untuk Edit Jadwal -->
<div id="editScheduleModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); padding-top: 60px;">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%;">
        <span class="close-edit-schedule" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h2 style="font-size: 20px; margin-bottom: 20px;">Edit Jadwal</h2>
        <form action="../proses/proses_jadwal.php" method="POST">
            <input type="hidden" name="action" value="edit_jadwal">
            <input type="hidden" id="editJadwalId" name="id">
            
            <label style="display: block; margin-bottom: 10px;">Tanggal:</label>
            <input type="date" id="editTanggal" name="tanggal" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
            
            <label style="display: block; margin-bottom: 10px;">Jam Mulai:</label>
            <input type="time" id="editJamMulai" name="jam_mulai" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
            
            <label style="display: block; margin-bottom: 10px;">Jam Selesai:</label>
            <input type="time" id="editJamSelesai" name="jam_selesai" required style="padding: 10px; width: 100%; margin-bottom: 20px;">
            
            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengedit jadwal ini?');" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Simpan Perubahan</button>
        </form>
    </div>
</div>


<h2 style="font-size: 24px; margin-bottom: 20px;">Permintaan Akses</h2>
<?php if (empty($permintaan_akses)): ?>
    <p>Tidak ada permintaan akses.</p>
<?php else: ?>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <tr>
            <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Nama Dosen</th>
            <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Nama Ruangan</th>
            <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Tipe Akses</th>
            <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Status</th>
            <th style="background-color: #007bff; color: white; padding: 10px; text-align: left;">Aksi</th>
        </tr>
        <?php foreach ($permintaan_akses as $p): ?>
            <tr>
                <td style="padding: 10px;"><?= htmlspecialchars($p['nama_lengkap']); ?></td>
                <td style="padding: 10px;"><?= htmlspecialchars($p['nama_ruangan']); ?></td>
                <td style="padding: 10px;"><?= htmlspecialchars($p['tipe_akses']); ?></td>
                <td style="padding: 10px;"><?= htmlspecialchars($p['status']); ?></td>
                <td style="padding: 10px;">
                    <form action="../proses/proses_permintaan.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="ubah_status">
                        <input type="hidden" name="id_permintaan" value="<?= $p['id']; ?>">
                        <select name="status" required style="padding: 5px; margin-right: 10px;">
                            <option value="" disabled selected>Pilih Status</option>
                            <option value="diterima">Diterima</option>
                            <option value="pending">Pending</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <button type="submit" style="background-color: #007bff; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Ubah Status</button>
                    </form>
                    <!-- Form untuk menghapus permintaan akses -->
                    <form action="../proses/proses_permintaan.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id_permintaan" value="<?= $p['id']; ?>">
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus permintaan akses ini?');" style="background-color: #dc3545; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>


    <script src="../js/script.js"></script>
</body>
</html>

<?php
// Hentikan output buffering dan kirim output
ob_end_flush();