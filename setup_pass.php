<?php
// Script sekali pakai — jalankan sekali lalu hapus
include 'config/koneksi.php';

// Update password sofi
$result = mysqli_query($conn, "UPDATE users SET password='16092006' WHERE username='sofi'");
if ($result) {
    echo "✅ Password sofi berhasil diubah ke '16092006'<br>";
} else {
    echo "❌ Gagal: " . mysqli_error($conn);
}

// Tampilkan semua user
$rows = mysqli_query($conn, "SELECT username, password, role FROM users");
echo "<table border='1' cellpadding='8'><tr><th>Username</th><th>Password</th><th>Role</th></tr>";
while($r = mysqli_fetch_assoc($rows)) {
    echo "<tr><td>{$r['username']}</td><td>{$r['password']}</td><td>{$r['role']}</td></tr>";
}
echo "</table><br><a href='user/index.php'>Ke Halaman User</a> | <a href='login.php'>Login Admin</a>";
?>
