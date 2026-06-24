<?php
/**
 * auth.php — Validasi sesi aktif
 * Dipanggil di setiap halaman yang membutuhkan login.
 */

function get_db_conn() {
    static $conn = null;
    if ($conn === null) {
        $conn = mysqli_connect("localhost", "root", "", "kantin_kampus");
        if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());
    }
    return $conn;
}

/**
 * Validasi session token.
 * Jika token di DB berbeda dengan yang di SESSION → sesi sudah diambil alih → paksa logout.
 */
function validate_session() {
    if (!isset($_SESSION['username']) || !isset($_SESSION['token'])) {
        return false;
    }
    $conn     = get_db_conn();
    $username = mysqli_real_escape_string($conn, $_SESSION['username']);
    $token    = mysqli_real_escape_string($conn, $_SESSION['token']);

    $row = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT session_token FROM users WHERE username='$username' LIMIT 1"
    ));

    if (!$row || $row['session_token'] !== $token) {
        // Token tidak cocok — ada sesi lain yang login → paksa logout
        session_destroy();
        return false;
    }
    return true;
}

function require_admin() {
    if (!isset($_SESSION['role']) || !validate_session() || $_SESSION['role'] !== 'admin') {
        header("Location: /CampusCanteen/login.php");
        exit;
    }
}

function require_user() {
    if (!isset($_SESSION['role']) || !validate_session() || $_SESSION['role'] !== 'user') {
        header("Location: /CampusCanteen/login.php");
        exit;
    }
}
?>
