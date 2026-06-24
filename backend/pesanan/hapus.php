<?php
session_start();
include '../../config/auth.php';
require_admin();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../../config/koneksi.php';

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data pesanan dulu untuk kembalikan stok
    $resultGet = mysqli_query($conn, "SELECT id_menu, jumlah FROM pesanan WHERE id_pesanan = '$id'");
    $data = mysqli_fetch_assoc($resultGet);

    if($data) {
        $id_menu = $data['id_menu'];
        $jumlah  = $data['jumlah'];

        mysqli_begin_transaction($conn);

        $q1 = "DELETE FROM pesanan WHERE id_pesanan = '$id'";
        $q2 = "UPDATE menu SET stok = stok + $jumlah WHERE id_menu = '$id_menu'";

        if(mysqli_query($conn, $q1) && mysqli_query($conn, $q2)) {
            mysqli_commit($conn);
            $_SESSION['pesan'] = "Pesanan berhasil dibatalkan dan stok dikembalikan!";
        } else {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal membatalkan pesanan!";
        }
    }
}
header("Location: index.php");
exit;
?>

