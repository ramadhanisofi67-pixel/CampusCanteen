<?php
session_start();
include '../../config/auth.php';
require_admin();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../../config/koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_menu']);
    $kategori = $_POST['kategori'];
    $harga    = $_POST['harga'];
    $stok     = $_POST['stok'];

    $query = "INSERT INTO menu (nama_menu, kategori, harga, stok) VALUES ('$nama', '$kategori', '$harga', '$stok')";
    if(mysqli_query($conn, $query)) {
        $_SESSION['pesan'] = "Menu berhasil ditambahkan!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambah menu: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu - Kantin Ibun Sofi</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="logo"><i class="fa-solid fa-utensils"></i> Kantin Ibun Sofi</a>
        <ul class="nav-links">
            <li><a href="../index.php">Dashboard Admin</a></li>
            <li><a href="../menu/index.php" class="active">Menu</a></li>
            <li><a href="../pesanan/index.php">Pesanan</a></li>
            <li><a href="/CampusCanteen/logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="glass-card" style="max-width: 600px; margin: 0 auto;">
            <h2><i class="fa-solid fa-plus"></i> Tambah Menu Baru</h2>
            <?php if(isset($_SESSION['error'])) { echo '<div class="alert alert-error">'.$_SESSION['error'].'</div>'; unset($_SESSION['error']); } ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control" required placeholder="Contoh: Nasi Goreng Spesial">
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" required min="0" placeholder="Contoh: 15000">
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control" required min="0" placeholder="Contoh: 50">
                </div>
                <div style="margin-top: 20px; display:flex; gap:10px;">
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-save"></i> Simpan</button>
                    <a href="index.php" class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
    <footer><p>&copy; 2026 Kantin Ibun Sofi. All rights reserved.</p></footer>
    <script src="../../assets/js/script.js?v=3"></script>
</body>
</html>


