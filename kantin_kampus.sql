CREATE DATABASE IF NOT EXISTS kantin_kampus;
USE kantin_kampus;

CREATE TABLE IF NOT EXISTS menu (
  id_menu INT AUTO_INCREMENT PRIMARY KEY,
  nama_menu VARCHAR(100) NOT NULL,
  kategori ENUM('Makanan', 'Minuman') NOT NULL,
  harga DECIMAL(10,2) NOT NULL,
  stok INT NOT NULL
);

CREATE TABLE IF NOT EXISTS pesanan (
  id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
  nama_mahasiswa VARCHAR(100) NOT NULL,
  nim VARCHAR(20) NOT NULL,
  id_menu INT NOT NULL,
  jumlah INT NOT NULL,
  total_harga DECIMAL(10,2) NOT NULL,
  tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_menu) REFERENCES menu(id_menu) ON DELETE CASCADE
);

INSERT INTO menu (nama_menu, kategori, harga, stok) VALUES
('Nasi Goreng Spesial', 'Makanan', 15000.00, 50),
('Ayam Geprek', 'Makanan', 12000.00, 30),
('Mie Goreng Telur', 'Makanan', 10000.00, 40),
('Es Teh Manis', 'Minuman', 3000.00, 100),
('Es Jeruk', 'Minuman', 5000.00, 80),
('Kopi Hitam', 'Minuman', 4000.00, 50);

INSERT INTO pesanan (nama_mahasiswa, nim, id_menu, jumlah, total_harga, tanggal_pesan) VALUES
('Andi Susanto', '12345678', 1, 2, 30000.00, NOW()),
('Budi Raharjo', '87654321', 4, 3, 9000.00, NOW());
