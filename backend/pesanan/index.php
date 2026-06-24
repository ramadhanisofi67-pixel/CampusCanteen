<?php
session_start();
include '../../config/auth.php';
require_admin();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../../config/koneksi.php';

$query = "SELECT p.*, m.nama_menu, m.harga
          FROM pesanan p
          JOIN menu m ON p.id_menu = m.id_menu
          ORDER BY p.tanggal_pesan DESC";
$result = mysqli_query($conn, $query);

$totalPesanan    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pesanan"))['t'];
$totalPendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_harga),0) as t FROM pesanan"))['t'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan - Kantin Ibun Sofi</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="logo"><i class="fa-solid fa-utensils"></i> Kantin Ibun Sofi</a>
        <ul class="nav-links">
            <li><a href="../index.php">Dashboard</a></li>
            <li><a href="../menu/index.php">Menu</a></li>
            <li><a href="index.php" class="active">Pesanan</a></li>
            <li><a href="/CampusCanteen/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php
        if(isset($_SESSION['pesan']))  { echo '<div class="alert alert-success">'.$_SESSION['pesan'].'</div>'; unset($_SESSION['pesan']); }
        if(isset($_SESSION['error']))  { echo '<div class="alert alert-error">'.$_SESSION['error'].'</div>';   unset($_SESSION['error']); }
        ?>

        <div class="glass-card">
            <h2>Daftar Semua Pesanan</h2>

            <div class="dashboard-cards" style="margin-top:20px;">
                <div class="dash-card green">
                    <i class="fa-solid fa-receipt fa-3x" style="color:var(--secondary-color)"></i>
                    <h3><?= $totalPesanan ?></h3>
                    <p>Total Pesanan Masuk</p>
                </div>
                <div class="dash-card">
                    <i class="fa-solid fa-wallet fa-3x" style="color:var(--primary-color)"></i>
                    <h3>Rp <?= number_format($totalPendapatan,0,',','.') ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Pemesan</th>
                            <th>No. Meja</th>
                            <th>Menu</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d-M-Y H:i', strtotime($row['tanggal_pesan'])) ?></td>
                            <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                            <td><?= htmlspecialchars($row['nim']) ?></td>
                            <td><?= htmlspecialchars($row['nama_menu']) ?></td>
                            <td><?= $row['jumlah'] ?></td>
                            <td>Rp <?= number_format($row['total_harga'],0,',','.') ?></td>
                            <td>
                                <a href="edit.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen"></i></a>
                                <a href="hapus.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if($totalPesanan == 0): ?>
                <p style="text-align:center;margin-top:20px;">Belum ada data pesanan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer><p>&copy; 2026 Kantin Ibun Sofi. All rights reserved.</p></footer>
    <script src="../../assets/js/script.js?v=3"></script>
</body>
</html>


