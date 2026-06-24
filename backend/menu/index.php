<?php
session_start();
include '../../config/auth.php';
require_admin();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../../config/koneksi.php';

$query  = "SELECT * FROM menu ORDER BY nama_menu ASC LIMIT 20";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Kantin Ibun Sofi</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="logo"><i class="fa-solid fa-utensils"></i> Kantin Ibun Sofi</a>
        <ul class="nav-links">
            <li><a href="../index.php">Dashboard</a></li>
            <li><a href="index.php" class="active">Menu</a></li>
            <li><a href="../pesanan/index.php">Pesanan</a></li>
            <li><a href="/CampusCanteen/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php
        if(isset($_SESSION['pesan']))  { echo '<div class="alert alert-success">'.$_SESSION['pesan'].'</div>'; unset($_SESSION['pesan']); }
        if(isset($_SESSION['error']))  { echo '<div class="alert alert-error">'.$_SESSION['error'].'</div>';   unset($_SESSION['error']); }
        ?>

        <div class="glass-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
                <h1>Kelola Menu</h1>
                <a href="tambah.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Menu</a>
            </div>

            <div class="controls-container">
                <div class="search-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama menu...">
                </div>
                <div class="filter-box">
                    <button class="btn btn-primary filter-btn" data-filter="all">Semua</button>
                    <button class="btn btn-outline filter-btn" data-filter="Makanan">Makanan</button>
                    <button class="btn btn-outline filter-btn" data-filter="Minuman">Minuman</button>
                </div>
            </div>

            <div class="menu-grid">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <?php
                    $namaLcase = strtolower($row['nama_menu']);
                    if(strpos($namaLcase,'nasi goreng') !== false) $img='https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400&h=300&fit=crop';
                    elseif(strpos($namaLcase,'ayam') !== false) $img='https://images.unsplash.com/photo-1626082896492-766af4eb65ed?w=400&h=300&fit=crop';
                    elseif(strpos($namaLcase,'mie') !== false) $img='https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&h=300&fit=crop';
                    elseif(strpos($namaLcase,'kopi') !== false) $img='https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=400&h=300&fit=crop';
                    elseif(strpos($namaLcase,'teh') !== false) $img='https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&h=300&fit=crop';
                    elseif(strpos($namaLcase,'jeruk') !== false) $img='https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=400&h=300&fit=crop';
                    elseif($row['kategori'] == 'Minuman') $img='https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&h=300&fit=crop';
                    else $img='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop';
                ?>
                <div class="menu-card" data-category="<?= $row['kategori'] ?>">
                    <div class="menu-img" style="background-image:url('<?= $img ?>');">
                        <span class="menu-category"><?= $row['kategori'] ?></span>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title"><?= htmlspecialchars($row['nama_menu']) ?></div>
                        <div class="menu-price">Rp <?= number_format($row['harga'],0,',','.') ?></div>
                        <div class="menu-stock">Stok: <strong><?= $row['stok'] ?></strong> porsi</div>
                        <div class="menu-action" style="display:flex;gap:8px;">
                            <a href="edit.php?id=<?= $row['id_menu'] ?>" class="btn btn-warning btn-sm" style="flex:1;" title="Edit"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a href="hapus.php?id=<?= $row['id_menu'] ?>" class="btn btn-danger btn-sm" style="flex:1;" title="Hapus" onclick="return confirm('Yakin hapus menu ini?')"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <?php if(mysqli_num_rows($result) == 0): ?>
            <div style="text-align:center;padding:40px;">Belum ada menu.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer><p>&copy; 2026 Kantin Ibun Sofi. All rights reserved.</p></footer>
    <script src="../../assets/js/script.js?v=3"></script>
</body>
</html>


