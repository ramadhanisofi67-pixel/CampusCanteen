<?php
session_start();
include '../config/koneksi.php';
include '../config/auth.php';
require_user();

// Menu tersedia
$resultMenu = mysqli_query($conn, "SELECT * FROM menu WHERE stok > 0 ORDER BY nama_menu ASC");
$totalMenuAvail = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM menu WHERE stok > 0"))['t'];

// Pesanan terbaru (publik — 10 pesanan terakhir)
$riwayat = mysqli_query($conn,
    "SELECT p.*, m.nama_menu, m.kategori
     FROM pesanan p
     JOIN menu m ON p.id_menu = m.id_menu
     ORDER BY p.tanggal_pesan DESC
     LIMIT 10"
);
$totalPesananHariIni = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as t FROM pesanan WHERE DATE(tanggal_pesan) = CURDATE()"
))['t'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantin Ibun Sofi - Pesan Makanan</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); font-family:'Segoe UI',sans-serif; margin:0; }

        /* Navbar */
        .user-navbar {
            background: linear-gradient(to right, #1a472a, #2ea44f);
            padding: 1rem 5%;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .user-navbar .logo { color:#fff; font-size:1.4rem; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:10px; }

        /* Hero */
        .hero-banner {
            background: linear-gradient(135deg, #1a472a, #2ea44f);
            color:#fff; padding:55px 5%; text-align:center;
        }
        .hero-banner h1 { font-size:2.2rem; margin-bottom:8px; }
        .hero-banner p  { opacity:.85; margin-bottom:22px; font-size:1rem; }
        .hero-badges    { display:flex; justify-content:center; gap:12px; flex-wrap:wrap; }
        .hero-badge {
            display:inline-flex; align-items:center; gap:7px;
            background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3);
            border-radius:30px; padding:8px 18px; font-size:0.85rem; font-weight:600;
        }

        /* Container */
        .container { padding:35px 5%; max-width:1200px; margin:0 auto; }
        .section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; flex-wrap:wrap; gap:10px; }
        .section-header h2 { font-size:1.4rem; color:#1a472a; margin:0; }

        /* Controls */
        .controls-container { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:22px; align-items:center; }
        .search-box { flex:1; min-width:240px; position:relative; }
        .search-box i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#2ea44f; }
        .search-box input {
            width:100%; box-sizing:border-box;
            padding:11px 15px 11px 40px; border:2px solid #ddd;
            border-radius:10px; font-size:0.95rem; background:#fff;
        }
        .search-box input:focus { outline:none; border-color:#2ea44f; }
        .filter-box { display:flex; gap:8px; flex-wrap:wrap; }
        .btn { padding:9px 18px; border-radius:10px; border:none; cursor:pointer; font-weight:600; font-size:0.88rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:all 0.25s; }
        .btn-primary { background:linear-gradient(135deg,#2ea44f,#1a472a); color:#fff; }
        .btn-outline  { background:#fff; color:#2ea44f; border:2px solid #2ea44f; }
        .btn-outline:hover, .btn-primary:hover { opacity:0.88; transform:translateY(-1px); }

        /* Menu Grid */
        .menu-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(250px,1fr)); gap:20px; margin-bottom:40px; }
        .menu-card { background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.07); transition:all 0.3s; display:flex; flex-direction:column; }
        .menu-card:hover { transform:translateY(-7px); box-shadow:0 15px 30px rgba(0,0,0,0.12); }
        .menu-img { height:170px; background-size:cover; background-position:center; position:relative; }
        .menu-category { position:absolute; top:12px; right:12px; background:#2ea44f; color:#fff; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:700; }
        .menu-content   { padding:18px; flex:1; display:flex; flex-direction:column; }
        .menu-title  { font-size:1rem; font-weight:700; color:#222; margin-bottom:7px; }
        .menu-price  { font-size:1.15rem; color:#f39c12; font-weight:800; margin-bottom:9px; }
        .stok-badge  { display:inline-flex; align-items:center; gap:5px; font-size:0.78rem; color:#555; background:#f0f0f0; padding:3px 10px; border-radius:20px; margin-bottom:13px; }
        .btn-pesan   {
            width:100%; padding:11px; background:linear-gradient(135deg,#2ea44f,#1a472a);
            color:#fff; border:none; border-radius:10px; font-size:0.92rem; font-weight:700;
            cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px;
            transition:all 0.3s; margin-top:auto;
        }
        .btn-pesan:hover { transform:translateY(-2px); box-shadow:0 6px 15px rgba(46,164,79,0.4); }
        .btn-pesan:disabled { background:#ccc; cursor:not-allowed; transform:none; box-shadow:none; }

        /* Riwayat */
        .riwayat-section { margin-top:10px; }
        .riwayat-table-wrap { background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.07); }
        .riwayat-table-header { padding:18px 22px; background:linear-gradient(135deg,#2ea44f,#1a472a); color:#fff; font-weight:700; font-size:1rem; display:flex; align-items:center; gap:10px; }
        table.rtable { width:100%; border-collapse:collapse; }
        table.rtable th { padding:13px 15px; text-align:left; background:#f8f8f8; color:#555; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid #eee; }
        table.rtable td { padding:14px 15px; font-size:0.9rem; color:#333; border-bottom:1px solid #f0f0f0; }
        table.rtable tr:last-child td { border-bottom:none; }
        table.rtable tr:hover td { background:#fafff8; }
        .badge-cat { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:700; }
        .badge-makan { background:rgba(46,164,79,0.15); color:#2ea44f; }
        .badge-minum { background:rgba(52,152,219,0.15); color:#2980b9; }

        /* Alert */
        .alert { padding:14px 18px; border-radius:12px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-size:0.9rem; }
        .alert-success { background:#e8f5e9; color:#1a6b2e; border:1px solid #c8e6c9; }
        .alert-error   { background:#fdecea; color:#c0392b; border:1px solid #f5c6cb; }

        /* Modal */
        .modal { display:none; position:fixed; z-index:2000; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(5px); justify-content:center; align-items:center; }
        .modal-box { background:#fff; border-radius:20px; padding:35px; width:90%; max-width:470px; box-shadow:0 25px 50px rgba(0,0,0,0.2); animation:popIn 0.3s ease; position:relative; }
        @keyframes popIn { from{opacity:0;transform:scale(0.9)} to{opacity:1;transform:scale(1)} }
        .modal-close { position:absolute; top:18px; right:20px; font-size:24px; color:#aaa; cursor:pointer; }
        .modal-close:hover { color:#333; }
        .modal-box h2 { font-size:1.25rem; color:#1a472a; margin-bottom:5px; }
        .modal-menu-name { color:#f39c12; font-weight:700; margin-bottom:18px; font-size:1rem; }
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; font-weight:600; font-size:0.88rem; color:#444; margin-bottom:5px; }
        .form-control { width:100%; box-sizing:border-box; padding:11px 14px; border:2px solid #e8e8e8; border-radius:10px; font-size:0.95rem; transition:border 0.3s; }
        .form-control:focus { outline:none; border-color:#2ea44f; }
        .form-control[readonly] { background:#f8f8f8; color:#1a472a; font-weight:700; }
        .btn-submit-modal { width:100%; padding:13px; background:linear-gradient(135deg,#2ea44f,#1a472a); color:#fff; border:none; border-radius:10px; font-size:0.95rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:all 0.3s; margin-top:10px; }
        .btn-submit-modal:hover { transform:translateY(-2px); box-shadow:0 6px 15px rgba(46,164,79,0.4); }

        footer { text-align:center; padding:22px; background:#1a472a; color:rgba(255,255,255,0.55); font-size:0.82rem; margin-top:10px; }
        @media(max-width:600px) { .hero-banner h1{font-size:1.6rem;} .user-navbar{flex-direction:column;gap:8px;} }
    </style>
</head>
<body>
    <nav class="user-navbar">
        <a href="index.php" class="logo">
            <i class="fa-solid fa-utensils"></i> Kantin Ibun Sofi
        </a>
        <a href="../logout.php" style="color:rgba(255,255,255,0.8);text-decoration:none;border:1px solid rgba(255,255,255,0.3);padding:5px 15px;border-radius:20px;font-size:0.85rem;"><i class="fa-solid fa-sign-out-alt"></i> Keluar</a>
    </nav>

    <!-- Hero -->
    <div class="hero-banner">
        <h1>🍽️ Selamat Datang!</h1>
        <p>Pilih menu favoritmu dan pesan sekarang — cepat, mudah, tanpa antre.</p>
        <div class="hero-badges">
            <span class="hero-badge"><i class="fa-solid fa-circle-check"></i> <?= $totalMenuAvail ?> Menu Tersedia</span>
            <span class="hero-badge"><i class="fa-solid fa-receipt"></i> <?= $totalPesananHariIni ?> Pesanan Hari Ini</span>
        </div>
    </div>

    <div class="container">
        <?php if(isset($_SESSION['pesan'])): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i><?= $_SESSION['pesan'] ?><?php unset($_SESSION['pesan']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i><?= $_SESSION['error'] ?><?php unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- ── DAFTAR MENU ── -->
        <div class="section-header">
            <h2><i class="fa-solid fa-bowl-food"></i> Daftar Menu</h2>
        </div>

        <div class="controls-container">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Cari nama menu...">
            </div>
            <div class="filter-box">
                <button class="btn btn-primary filter-btn active-filter" data-filter="all">Semua</button>
                <button class="btn btn-outline filter-btn" data-filter="Makanan">Makanan</button>
                <button class="btn btn-outline filter-btn" data-filter="Minuman">Minuman</button>
            </div>
        </div>

        <div class="menu-grid" id="menuGrid">
            <?php
            $menuRows = [];
            while($row = mysqli_fetch_assoc($resultMenu)) $menuRows[] = $row;
            foreach($menuRows as $row):
                $lc = strtolower($row['nama_menu']);
                if(strpos($lc,'nasi goreng')!==false) $img='https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400&h=300&fit=crop';
                elseif(strpos($lc,'ayam')!==false)    $img='https://images.unsplash.com/photo-1626082896492-766af4eb65ed?w=400&h=300&fit=crop';
                elseif(strpos($lc,'mie')!==false)     $img='https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&h=300&fit=crop';
                elseif(strpos($lc,'kopi')!==false)    $img='https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=400&h=300&fit=crop';
                elseif(strpos($lc,'teh')!==false)     $img='https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&h=300&fit=crop';
                elseif(strpos($lc,'jeruk')!==false)   $img='https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=400&h=300&fit=crop';
                elseif($row['kategori']=='Minuman')   $img='https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&h=300&fit=crop';
                else                                   $img='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop';
            ?>
            <div class="menu-card" data-category="<?= $row['kategori'] ?>">
                <div class="menu-img" style="background-image:url('<?= $img ?>')">
                    <span class="menu-category"><?= $row['kategori'] ?></span>
                </div>
                <div class="menu-content">
                    <div class="menu-title"><?= htmlspecialchars($row['nama_menu']) ?></div>
                    <div class="menu-price">Rp <?= number_format($row['harga'],0,',','.') ?></div>
                    <span class="stok-badge"><i class="fa-solid fa-box"></i> Stok: <?= $row['stok'] ?> porsi</span>
                    <button class="btn-pesan" onclick="openOrderModal('<?= $row['id_menu'] ?>','<?= addslashes($row['nama_menu']) ?>',<?= $row['harga'] ?>,<?= $row['stok'] ?>)">
                        <i class="fa-solid fa-cart-plus"></i> Pesan Sekarang
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if($totalMenuAvail == 0): ?>
        <div style="text-align:center;padding:60px;color:#888;">
            <i class="fa-solid fa-bowl-rice fa-3x" style="margin-bottom:15px;display:block;"></i>
            Maaf, belum ada menu yang tersedia saat ini.
        </div>
        <?php endif; ?>

        <!-- ── RIWAYAT PESANAN TERBARU ── -->
        <div class="riwayat-section">
            <div class="section-header">
                <h2><i class="fa-solid fa-clock-rotate-left"></i> Pesanan Terbaru</h2>
            </div>
            <div class="riwayat-table-wrap">
                <div class="riwayat-table-header">
                    <i class="fa-solid fa-list-ul"></i> 10 Pesanan Terakhir
                </div>
                <?php if(mysqli_num_rows($riwayat) > 0): ?>
                <table class="rtable">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Nama</th>
                            <th>Menu</th>
                            <th>Kategori</th>
                            <th>No. Meja</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = mysqli_fetch_assoc($riwayat)): ?>
                        <tr>
                            <td><?= date('d M, H:i', strtotime($r['tanggal_pesan'])) ?></td>
                            <td><strong><?= htmlspecialchars($r['nama_mahasiswa']) ?></strong></td>
                            <td><?= htmlspecialchars($r['nama_menu']) ?></td>
                            <td><span class="badge-cat <?= $r['kategori']=='Makanan'?'badge-makan':'badge-minum' ?>"><?= $r['kategori'] ?></span></td>
                            <td><?= htmlspecialchars($r['nim']) ?></td>
                            <td><?= $r['jumlah'] ?></td>
                            <td><strong>Rp <?= number_format($r['total_harga'],0,',','.') ?></strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="text-align:center;padding:40px;color:#aaa;">
                    <i class="fa-solid fa-receipt fa-2x" style="margin-bottom:10px;display:block;"></i>
                    Belum ada pesanan hari ini.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Pemesanan -->
    <div id="orderModal" class="modal">
        <div class="modal-box">
            <span class="modal-close" onclick="closeOrderModal()">&times;</span>
            <h2><i class="fa-solid fa-cart-plus"></i> Konfirmasi Pesanan</h2>
            <div class="modal-menu-name" id="modalMenuName"></div>
            <form action="proses_pesan.php" method="POST">
                <input type="hidden" name="id_menu" id="modal_id_menu">
                <input type="hidden" id="modal_harga">
                <div class="form-group">
                    <label>Nama Pemesan</label>
                    <input type="text" name="nama_mahasiswa" id="modal_nama" class="form-control" required placeholder="Masukkan nama kamu">
                </div>
                <div class="form-group">
                    <label>Nomor Meja</label>
                    <input type="text" name="nim" id="modal_meja" class="form-control" required placeholder="Contoh: Meja 5">
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" id="modal_jumlah" class="form-control" required min="1" value="1" oninput="hitungTotal()">
                </div>
                <div class="form-group">
                    <label>Total Harga</label>
                    <input type="text" id="modal_total" class="form-control" readonly>
                </div>
                <button type="submit" class="btn-submit-modal">
                    <i class="fa-solid fa-paper-plane"></i> Pesan Sekarang
                </button>
            </form>
        </div>
    </div>

    <footer><p>&copy; 2026 Kantin Ibun Sofi</p></footer>

    <script>
    // Search & Filter
    const searchInput = document.getElementById('searchInput');
    const cards       = document.querySelectorAll('.menu-card');
    const filterBtns  = document.querySelectorAll('.filter-btn');
    let activeFilter  = 'all';

    function filterMenu() {
        const term = searchInput.value.toLowerCase();
        cards.forEach(card => {
            const title = card.querySelector('.menu-title').textContent.toLowerCase();
            const cat   = card.getAttribute('data-category');
            card.style.display = (title.includes(term) && (activeFilter==='all' || cat===activeFilter)) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterMenu);
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => { b.classList.remove('btn-primary'); b.classList.add('btn-outline'); });
            btn.classList.add('btn-primary'); btn.classList.remove('btn-outline');
            activeFilter = btn.getAttribute('data-filter');
            filterMenu();
        });
    });

    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(a => {
        setTimeout(() => { a.style.opacity='0'; a.style.transition='opacity .3s'; setTimeout(()=>a.style.display='none',300); }, 4000);
    });

    // Modal
    window.onclick = e => { if(e.target.id==='orderModal') closeOrderModal(); };

    function openOrderModal(id, nama, harga, stok) {
        document.getElementById('modal_id_menu').value = id;
        document.getElementById('modalMenuName').textContent = '🍴 ' + nama;
        document.getElementById('modal_harga').value = harga;
        document.getElementById('modal_jumlah').value = 1;
        document.getElementById('modal_jumlah').max = stok;
        hitungTotal();
        document.getElementById('orderModal').style.display = 'flex';
    }
    function closeOrderModal() { document.getElementById('orderModal').style.display = 'none'; }
    function hitungTotal() {
        const h = document.getElementById('modal_harga').value;
        const q = document.getElementById('modal_jumlah').value;
        document.getElementById('modal_total').value = 'Rp ' + (h*q).toLocaleString('id-ID');
    }
    </script>
</body>
</html>
