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
    $query = "DELETE FROM menu WHERE id_menu = '$id'";
    if(mysqli_query($conn, $query)) {
        $_SESSION['pesan'] = "Menu berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus menu: " . mysqli_error($conn);
    }
}
header("Location: index.php");
exit;
?>

