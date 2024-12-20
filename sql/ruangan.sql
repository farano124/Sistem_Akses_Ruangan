-- Active: 1732194082372@@127.0.0.1@3306@ruangan


-- Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'dosen', 'mahasiswa') NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL
);

-- Tabel Ruangan
CREATE TABLE ruangan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_ruangan VARCHAR(50) NOT NULL,
    jenis VARCHAR(30) NOT NULL,
    kapasitas INT NOT NULL
);

-- Tabel Jadwal Ruangan
CREATE TABLE jadwal_ruangan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruangan_id INT NOT NULL,
    tanggal DATE NOT NULL,
    hari VARCHAR(20) NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    FOREIGN KEY (ruangan_id) REFERENCES ruangan(id) ON DELETE CASCADE
);


-- Tabel Akses Ruangan
CREATE TABLE permintaan_akses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    ruangan_id INT NOT NULL,
    jadwal_id INT NOT NULL,
    tipe_akses ENUM('reguler', 'sementara') NOT NULL,
    status ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
    tanggal_permintaan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ruangan_id) REFERENCES ruangan(id) ON DELETE CASCADE,
    FOREIGN KEY (jadwal_id) REFERENCES jadwal_ruangan(id) ON DELETE CASCADE
);


INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama_lengkap`) 
VALUES  ('1', 'admin', 'admin123', 'admin', 'admin'), 
        ('2', 'dosen', 'dosen123', 'dosen', 'nobunaga'), 
        ('3', 'mahasiswa', 'mahasiswa123', 'mahasiswa', 'YUKIIIII');