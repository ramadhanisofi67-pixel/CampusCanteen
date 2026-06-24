<?php
session_start();
if (isset($_SESSION['username'])) {
    include 'config/koneksi.php';
    $u = mysqli_real_escape_string($conn, $_SESSION['username']);
    mysqli_query($conn, "UPDATE users SET session_token=NULL WHERE username='$u'");
}
session_destroy();
header("Location: login.php");
exit;
?>
