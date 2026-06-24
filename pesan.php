<?php
session_start();
include 'config/koneksi.php';

// Ambil data menu untuk dropdown
$queryMenu = "SELECT * FROM menu WHERE stok > 0 ORDER BY nama_menu ASC";
$resultMenu = mysqli_query($conn, $queryMenu);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pemesanan - Campus Canteen</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo"><i class="fa-solid fa-utensils"></i> Campus Canteen</a>
        <ul class="nav-links">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="menu/index.php">Menu</a></li>
            <li><a href="pesan.php" class="active">Pesan</a></li>
            <li><a href="pesanan/index.php">Pesanan Admin</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php
        if(isset($_SESSION['pesan'])) {
            echo '<div class="alert alert-success">'.$_SESSION['pesan'].'</div>';
            unset($_SESSION['pesan']);
        }
        if(isset($_SESSION['error'])) {
            echo '<div class="alert alert-error">'.$_SESSION['error'].'</div>';
            unset($_SESSION['error']);
        }
        ?>
        <div class="glass-card" style="max-width: 600px; margin: 0 auto;">
            <h2><i class="fa-solid fa-cart-plus"></i> Form Pemesanan Makanan</h2>
            <p style="margin-bottom: 20px; color: #666;">Silakan isi form di bawah ini untuk memesan makanan/minuman.</p>
            
            <form action="pesanan/tambah.php" method="POST" id="orderForm">
                <div class="form-group">
                    <label>Nama Mahasiswa</label>
                    <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" class="form-control" required placeholder="Masukkan nama lengkap">
                </div>
                <div class="form-group">
                    <label>NIM</label>
                    <input type="text" name="nim" id="nim" class="form-control" required placeholder="Masukkan NIM">
                </div>
                <div class="form-group">
                    <label>Pilih Menu</label>
                    <select name="id_menu" id="id_menu" class="form-control" required>
                        <option value="">-- Pilih Menu --</option>
                        <?php while($row = mysqli_fetch_assoc($resultMenu)): ?>
                            <option value="<?php echo $row['id_menu']; ?>" data-harga="<?php echo $row['harga']; ?>">
                                <?php echo htmlspecialchars($row['nama_menu']); ?> - Rp <?php echo number_format($row['harga'],0,',','.'); ?> (Stok: <?php echo $row['stok']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" id="jumlah" class="form-control" required min="1" value="1">
                </div>
                <div class="form-group">
                    <label>Total Harga (Rp)</label>
                    <input type="text" id="total_harga" class="form-control" readonly style="background: #e9ecef; font-weight: bold; color: var(--secondary-dark);">
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-paper-plane"></i> Proses Pesanan</button>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2026 Campus Canteen. All rights reserved.</p>
    </footer>
    <script src="assets/js/script.js"></script>
</body>
</html>

