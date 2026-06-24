CREATE DATABASE IF NOT EXISTS kantin_kampus;
USE kantin_kampus;

CREATE TABLE IF NOT EXISTS menu (
  id_menu INT AUTO_INCREMENT PRIMARY KEY,
  nama_menu VARCHAR(100) NOT NULL,
  kategori ENUM('Makanan', 'Minuman') NOT NULL,
  harga DECIMAL(10,2) NOT NULL,
  stok INT NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,    -- hanya huruf (a-z)
  password VARCHAR(20) NOT NULL,           -- hanya angka
  role ENUM('admin','user') DEFAULT 'user',
  session_token VARCHAR(64) DEFAULT NULL,  -- token sesi aktif
  last_login DATETIME DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS pesanan (
  id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
  username_user VARCHAR(50) DEFAULT NULL,  -- FK ke users.username (opsional)
  nama_mahasiswa VARCHAR(100) NOT NULL,
  nim VARCHAR(20) NOT NULL,
  id_menu INT NOT NULL,
  jumlah INT NOT NULL,
  total_harga DECIMAL(10,2) NOT NULL,
  tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_menu) REFERENCES menu(id_menu) ON DELETE CASCADE
);

-- Akun users: username = huruf, password = angka
INSERT IGNORE INTO users (username, password, role) VALUES
('admin',    '123456', 'admin'),
('adminka',  '654321', 'admin'),
('budi',     '111111', 'user'),
('andi',     '222222', 'user'),
('sari',     '333333', 'user'),
('dewi',     '444444', 'user'),
('reza',     '555555', 'user');

-- Data menu awal
INSERT IGNORE INTO menu (id_menu, nama_menu, kategori, harga, stok) VALUES
(1, 'Nasi Goreng Spesial', 'Makanan', 15000.00, 50),
(2, 'Ayam Geprek', 'Makanan', 12000.00, 30),
(3, 'Mie Goreng Telur', 'Makanan', 10000.00, 40),
(4, 'Es Teh Manis', 'Minuman', 3000.00, 100),
(5, 'Es Jeruk', 'Minuman', 5000.00, 80),
(6, 'Kopi Hitam', 'Minuman', 4000.00, 50);

-- Contoh pesanan awal
INSERT IGNORE INTO pesanan (id_pesanan, username_user, nama_mahasiswa, nim, id_menu, jumlah, total_harga) VALUES
(1, 'budi', 'Budi', 'Meja 2', 1, 2, 30000.00),
(2, 'andi', 'Andi', 'Meja 3', 4, 3, 9000.00);
