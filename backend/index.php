<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
require_admin();


// Statistik Dashboard
$totalMenu      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM menu"))['t'];
$totalPesanan   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pesanan"))['t'];
$totalPendapatan= mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_harga),0) as t FROM pesanan"))['t'];
$totalMakanan   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM menu WHERE kategori='Makanan'"))['t'];
$totalMinuman   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM menu WHERE kategori='Minuman'"))['t'];

// 5 Pesanan Terbaru
$recentOrders = mysqli_query($conn, "SELECT p.*, m.nama_menu FROM pesanan p JOIN menu m ON p.id_menu=m.id_menu ORDER BY p.tanggal_pesan DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kantin Ibun Sofi</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #eefaf0 0%, #d7eedc 100%); color: #333; }
        .admin-navbar {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .admin-navbar .logo { color: #1a472a; font-size: 1.4rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .logo-badge { background: linear-gradient(135deg, #e74c3c, #c0392b); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .admin-nav-links { list-style: none; display: flex; gap: 5px; }
        .admin-nav-links a {
            color: #555;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .admin-nav-links a:hover, .admin-nav-links a.active {
            background: #2ea44f;
            color: #fff;
        }
        .nav-user { display: flex; align-items: center; gap: 12px; color: #333; font-size: 0.9rem; }
        .btn-logout { background: rgba(231,76,60,0.2); color: #e74c3c; border: 1px solid rgba(231,76,60,0.3); padding: 7px 16px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; gap: 6px; }
        .btn-logout:hover { background: #e74c3c; color: #fff; }

        .container { padding: 35px 5%; max-width: 1300px; margin: 0 auto; }
        .page-header { margin-bottom: 30px; }
        .page-header h1 { color: #1a472a; font-size: 1.8rem; margin-bottom: 5px; }
        .page-header p  { color: #666; font-size: 0.9rem; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: #fff;
            backdrop-filter: blur(10px);
            border: 1px solid #eee;
            border-radius: 16px;
            padding: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.3); }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; font-size: 1.3rem; }
        .stat-icon.green  { background: rgba(46,164,79,0.2);  color: #2ea44f; }
        .stat-icon.blue   { background: rgba(52,152,219,0.2);  color: #3498db; }
        .stat-icon.orange { background: rgba(243,156,18,0.2);  color: #f39c12; }
        .stat-icon.red    { background: rgba(231,76,60,0.2);   color: #e74c3c; }
        .stat-icon.purple { background: rgba(155,89,182,0.2);  color: #9b59b6; }
        .stat-value { font-size: 1.9rem; font-weight: 800; color: #222; margin-bottom: 5px; }
        .stat-label { color: #777; font-size: 0.82rem; }

        /* Quick Actions */
        .section-title { color: #1a472a; font-size: 1.1rem; font-weight: 700; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
        .section-title i { color: #2ea44f; }
        .actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .action-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 14px;
            padding: 22px 15px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            display: block;
        }
        .action-card:hover { background: #fafafa; transform: translateY(-4px); }
        .action-card i { font-size: 1.8rem; display: block; margin-bottom: 10px; }
        .action-card span { font-size: 0.85rem; font-weight: 600; color: #444; }
        .action-card.primary i { color: #2ea44f; }
        .action-card.warning i { color: #f39c12; }
        .action-card.info    i { color: #3498db; }
        .action-card.danger  i { color: #e74c3c; }

        /* Recent Orders Table */
        .panel {
            background: #fff;
            backdrop-filter: blur(10px);
            border: 1px solid #eee;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
        }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { padding: 12px 15px; text-align: left; color: #666; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; }
        .admin-table td { padding: 13px 15px; color: #333; font-size: 0.9rem; border-bottom: 1px solid #eee; }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background: #fdfdfd; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-green { background: rgba(46,164,79,0.2); color: #2ea44f; }

        footer { text-align: center; padding: 20px; color: #999; font-size: 0.82rem; }

        @media(max-width:768px) {
            .admin-navbar { flex-direction: column; gap: 12px; }
            .admin-nav-links { flex-wrap: wrap; justify-content: center; }
        }
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <a href="index.php" class="logo">
            <i class="fa-solid fa-utensils"></i>
            Kantin Ibun Sofi
            <span class="logo-badge">Admin</span>
        </a>
        <ul class="admin-nav-links">
            <li><a href="index.php" class="active"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="menu/index.php"><i class="fa-solid fa-bowl-food"></i> Menu</a></li>
            <li><a href="pesanan/index.php"><i class="fa-solid fa-receipt"></i> Pesanan</a></li>
            <li><a href="../about.php"><i class="fa-solid fa-circle-info"></i> About</a></li>
        </ul>
        <div class="nav-user">
            <i class="fa-solid fa-user-shield"></i>
            <?= htmlspecialchars($_SESSION['username']) ?>
            <a href="/CampusCanteen/logout.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>📊 Dashboard Admin</h1>
            <p>Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>! Berikut ringkasan data kantin hari ini.</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa-solid fa-bowl-food"></i></div>
                <div class="stat-value"><?= $totalMenu ?></div>
                <div class="stat-label">Total Menu</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa-solid fa-utensils"></i></div>
                <div class="stat-value"><?= $totalMakanan ?></div>
                <div class="stat-label">Jenis Makanan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fa-solid fa-glass-water"></i></div>
                <div class="stat-value"><?= $totalMinuman ?></div>
                <div class="stat-label">Jenis Minuman</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-value"><?= $totalPesanan ?></div>
                <div class="stat-label">Total Pesanan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-value" style="font-size:1.2rem;">Rp <?= number_format($totalPendapatan,0,',','.') ?></div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section-title"><i class="fa-solid fa-bolt"></i> Aksi Cepat</div>
        <div class="actions-grid">
            <a href="menu/tambah.php" class="action-card primary">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Menu</span>
            </a>
            <a href="menu/index.php" class="action-card warning">
                <i class="fa-solid fa-list"></i>
                <span>Kelola Menu</span>
            </a>
            <a href="pesanan/index.php" class="action-card info">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Semua Pesanan</span>
            </a>
            <a href="../frontend/index.php" class="action-card danger">
                <i class="fa-solid fa-eye"></i>
                <span>Lihat Tampilan User</span>
            </a>
        </div>

        <!-- Recent Orders -->
        <div class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> 5 Pesanan Terbaru</div>
        <div class="panel">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pemesan</th>
                        <th>No. Meja</th>
                        <th>Menu</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($recentOrders)): ?>
                    <tr>
                        <td><?= date('d M Y H:i', strtotime($row['tanggal_pesan'])) ?></td>
                        <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                        <td><span class="badge badge-green"><?= htmlspecialchars($row['nim']) ?></span></td>
                        <td><?= htmlspecialchars($row['nama_menu']) ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td>Rp <?= number_format($row['total_harga'],0,',','.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if($totalPesanan == 0): ?>
                    <tr><td colspan="6" style="text-align:center;color:rgba(255,255,255,0.3);padding:30px;">Belum ada pesanan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer><p>&copy; 2026 Kantin Ibun Sofi — Admin Panel</p></footer>
    <script src="../assets/js/script.js?v=3"></script>
</body>
</html>


