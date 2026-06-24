<?php
session_start();
include '../config/koneksi.php';

// Tidak perlu login — mahasiswa langsung bisa pesan
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$nama = mysqli_real_escape_string($conn, <?php
session_start();
include '../config/koneksi.php';

// Tidak perlu login — mahasiswa langsung bisa pesan
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$nama    = mysqli_real_escape_string($conn, $_POST['nama_mahasiswa'] ?? '');
$nim     = mysqli_real_escape_string($conn, $_POST['nim'] ?? '');
$id_menu = intval($_POST['id_menu'] ?? 0);
$jumlah  = intval($_POST['jumlah'] ?? 1);

if (!$nim || !$id_menu || $jumlah < 1) {
    $_SESSION['error'] = "Data pesanan tidak lengkap!";
    header("Location: index.php");
    exit;
}

$menuData = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT harga, stok, nama_menu FROM menu WHERE id_menu = '$id_menu'"
));

if (!$menuData) {
    $_SESSION['error'] = "Menu tidak ditemukan!";
    header("Location: index.php");
    exit;
}

if ($menuData['stok'] < $jumlah) {
    $_SESSION['error'] = "Stok tidak mencukupi! Sisa stok: " . $menuData['stok'];
    header("Location: index.php");
    exit;
}

$total_harga = $menuData['harga'] * $jumlah;

mysqli_begin_transaction($conn);

$q1 = "INSERT INTO pesanan (nama_mahasiswa, nim, id_menu, jumlah, total_harga, tanggal_pesan)
        VALUES ('$nama', '$nim', '$id_menu', '$jumlah', '$total_harga', NOW())";
$q2 = "UPDATE menu SET stok = stok - $jumlah WHERE id_menu = '$id_menu'";

if (mysqli_query($conn, $q1) && mysqli_query($conn, $q2)) {
    mysqli_commit($conn);
    $_SESSION['pesan'] = "✅ Pesanan <strong>" . htmlspecialchars($menuData['nama_menu']) . "</strong> berhasil! Total: <strong>Rp " . number_format($total_harga, 0, ',', '.') . "</strong>";
} else {
    mysqli_rollback($conn);
    $_SESSION['error'] = "Gagal memproses pesanan: " . mysqli_error($conn);
}

header("Location: index.php");
exit;
?>

SESSION['username']);
$nim     = mysqli_real_escape_string($conn, $_POST['nim'] ?? '');
$id_menu = intval($_POST['id_menu'] ?? 0);
$jumlah  = intval($_POST['jumlah'] ?? 1);

if (!$nim || !$id_menu || $jumlah < 1) {
    $_SESSION['error'] = "Data pesanan tidak lengkap!";
    header("Location: index.php");
    exit;
}

$menuData = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT harga, stok, nama_menu FROM menu WHERE id_menu = '$id_menu'"
));

if (!$menuData) {
    $_SESSION['error'] = "Menu tidak ditemukan!";
    header("Location: index.php");
    exit;
}

if ($menuData['stok'] < $jumlah) {
    $_SESSION['error'] = "Stok tidak mencukupi! Sisa stok: " . $menuData['stok'];
    header("Location: index.php");
    exit;
}

$total_harga = $menuData['harga'] * $jumlah;

mysqli_begin_transaction($conn);

$q1 = "INSERT INTO pesanan (nama_mahasiswa, nim, id_menu, jumlah, total_harga, tanggal_pesan)
        VALUES ('$nama', '$nim', '$id_menu', '$jumlah', '$total_harga', NOW())";
$q2 = "UPDATE menu SET stok = stok - $jumlah WHERE id_menu = '$id_menu'";

if (mysqli_query($conn, $q1) && mysqli_query($conn, $q2)) {
    mysqli_commit($conn);
    $_SESSION['pesan'] = "✅ Pesanan <strong>" . htmlspecialchars($menuData['nama_menu']) . "</strong> berhasil! Total: <strong>Rp " . number_format($total_harga, 0, ',', '.') . "</strong>";
} else {
    mysqli_rollback($conn);
    $_SESSION['error'] = "Gagal memproses pesanan: " . mysqli_error($conn);
}

header("Location: index.php");
exit;
?>


