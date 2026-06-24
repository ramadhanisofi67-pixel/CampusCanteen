<?php
session_start();
include '../../config/auth.php';
require_admin();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../../config/koneksi.php';

if(!isset($_GET['id'])) { header("Location: index.php"); exit; }

$id_pesanan = intval($_GET['id']);
$result     = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'");
$data       = mysqli_fetch_assoc($result);

if(!$data) { header("Location: index.php"); exit; }

$resultMenu = mysqli_query($conn, "SELECT * FROM menu ORDER BY nama_menu ASC");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama         = mysqli_real_escape_string($conn, $_POST['nama_mahasiswa']);
    $nim          = mysqli_real_escape_string($conn, $_POST['nim']);
    $id_menu_baru = $_POST['id_menu'];
    $jumlah_baru  = intval($_POST['jumlah']);
    $id_menu_lama = $data['id_menu'];
    $jumlah_lama  = $data['jumlah'];

    $menu_baru_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga, stok FROM menu WHERE id_menu = '$id_menu_baru'"));

    if($menu_baru_data) {
        $total_harga_baru = $menu_baru_data['harga'] * $jumlah_baru;
        mysqli_begin_transaction($conn);

        if($id_menu_lama == $id_menu_baru) {
            $selisih = $jumlah_baru - $jumlah_lama;
            if($menu_baru_data['stok'] >= $selisih) {
                $q1 = "UPDATE pesanan SET nama_mahasiswa='$nama', nim='$nim', jumlah='$jumlah_baru', total_harga='$total_harga_baru' WHERE id_pesanan='$id_pesanan'";
                $q2 = "UPDATE menu SET stok = stok - $selisih WHERE id_menu='$id_menu_baru'";
                if(mysqli_query($conn,$q1) && mysqli_query($conn,$q2)) { mysqli_commit($conn); $_SESSION['pesan']="Pesanan berhasil diubah!"; header("Location: index.php"); exit; }
                else { mysqli_rollback($conn); $_SESSION['error']="Gagal update pesanan!"; }
            } else { $_SESSION['error']="Stok tidak mencukupi!"; }
        } else {
            if($menu_baru_data['stok'] >= $jumlah_baru) {
                $q1 = "UPDATE menu SET stok = stok + $jumlah_lama WHERE id_menu='$id_menu_lama'";
                $q2 = "UPDATE menu SET stok = stok - $jumlah_baru WHERE id_menu='$id_menu_baru'";
                $q3 = "UPDATE pesanan SET nama_mahasiswa='$nama', nim='$nim', id_menu='$id_menu_baru', jumlah='$jumlah_baru', total_harga='$total_harga_baru' WHERE id_pesanan='$id_pesanan'";
                if(mysqli_query($conn,$q1) && mysqli_query($conn,$q2) && mysqli_query($conn,$q3)) { mysqli_commit($conn); $_SESSION['pesan']="Pesanan berhasil diubah!"; header("Location: index.php"); exit; }
                else { mysqli_rollback($conn); $_SESSION['error']="Gagal update!"; }
            } else { $_SESSION['error']="Stok menu baru tidak mencukupi!"; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan - Kantin Ibun Sofi</title>
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
        <div class="glass-card" style="max-width:600px;margin:0 auto;">
            <h2>Edit Pesanan</h2>
            <?php if(isset($_SESSION['error'])) { echo '<div class="alert alert-error">'.$_SESSION['error'].'</div>'; unset($_SESSION['error']); } ?>
            <form action="" method="POST" id="orderForm">
                <div class="form-group">
                    <label>Nama Mahasiswa</label>
                    <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" class="form-control" value="<?= htmlspecialchars($data['nama_mahasiswa']) ?>" required>
                </div>
                <div class="form-group">
                    <label>NIM / No. Meja</label>
                    <input type="text" name="nim" id="nim" class="form-control" value="<?= htmlspecialchars($data['nim']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Pilih Menu</label>
                    <select name="id_menu" id="id_menu" class="form-control" required>
                        <?php while($row = mysqli_fetch_assoc($resultMenu)): ?>
                        <option value="<?= $row['id_menu'] ?>" data-harga="<?= $row['harga'] ?>" <?= ($data['id_menu'] == $row['id_menu']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['nama_menu']) ?> - Rp <?= number_format($row['harga'],0,',','.') ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" id="jumlah" class="form-control" value="<?= $data['jumlah'] ?>" required min="1">
                </div>
                <div class="form-group">
                    <label>Total Harga (Rp)</label>
                    <input type="text" id="total_harga" class="form-control" value="<?= $data['total_harga'] ?>" readonly style="background:#e9ecef;">
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-save"></i> Update</button>
                    <a href="index.php" class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
    <footer><p>&copy; 2026 Kantin Ibun Sofi. All rights reserved.</p></footer>
    <script src="../../assets/js/script.js?v=3"></script>
</body>
</html>


