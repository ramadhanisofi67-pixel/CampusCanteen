<?php
session_start();

// Jika admin sudah login, langsung redirect ke dashboard admin
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && isset($_SESSION['token'])) {
    include '../config/koneksi.php';
    $u  = mysqli_real_escape_string($conn, $_SESSION['username'] ?? '');
    $tk = mysqli_real_escape_string($conn, $_SESSION['token'] ?? '');
    $chk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT session_token FROM users WHERE username='$u' LIMIT 1"));
    if ($chk && $chk['session_token'] === $tk) {
        header("Location: index.php");
        exit;
    }
}

include '../config/koneksi.php';
$error = '';
$info  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong!';
    } else {
        $uname = mysqli_real_escape_string($conn, $username);
        $upass = mysqli_real_escape_string($conn, $password);

        $user = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT * FROM users WHERE username='$uname' AND password='$upass' AND role='admin' LIMIT 1"
        ));

        if ($user) {
            if (!empty($user['session_token'])) {
                $info = "Sesi admin lama otomatis diakhiri.";
            }
            $new_token = bin2hex(random_bytes(32));
            $now       = date('Y-m-d H:i:s');
            mysqli_query($conn,
                "UPDATE users SET session_token='$new_token', last_login='$now' WHERE id_user='{$user['id_user']}'"
            );
            $_SESSION['id_user']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = 'admin';
            $_SESSION['token']    = $new_token;

            header("Location: index.php");
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kantin Ibun Sofi</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a472a 0%, #2ea44f 60%, #a8d5b5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-wrapper { width: 100%; max-width: 420px; padding: 20px; }
        .login-card {
            background: #fff;
            border-radius: 24px;
            padding: 45px 40px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.25);
            animation: slideUp 0.4s ease;
        }
        @keyframes slideUp { from{opacity:0;transform:translateY(25px)} to{opacity:1;transform:translateY(0)} }

        .login-logo { text-align:center; margin-bottom:30px; }
        .icon-wrap {
            width:80px; height:80px;
            background: linear-gradient(135deg,#1a472a,#2ea44f);
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 14px;
            box-shadow:0 10px 25px rgba(46,164,79,0.35);
        }
        .icon-wrap i { color:#fff; font-size:2rem; }
        .login-logo h1 { font-size:1.55rem; color:#1a472a; margin:0 0 4px; }
        .login-logo p  { color:#777; font-size:0.85rem; }
        .admin-badge {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(231,76,60,0.1); color:#c0392b;
            border:1px solid rgba(231,76,60,0.25);
            border-radius:20px; padding:5px 14px;
            font-size:0.78rem; font-weight:700;
            margin-top:8px;
        }

        .form-group { margin-bottom:18px; }
        .form-group label { display:block; font-weight:600; font-size:0.87rem; color:#333; margin-bottom:6px; }
        .input-wrap { position:relative; }
        .input-wrap .ico { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#2ea44f; font-size:0.9rem; }
        .form-control {
            width:100%; padding:12px 15px 12px 42px; box-sizing:border-box;
            border:2px solid #e5e5e5; border-radius:10px;
            font-size:0.95rem; background:#fafafa; transition:border 0.25s;
        }
        .form-control:focus { outline:none; border-color:#2ea44f; background:#fff; box-shadow:0 0 0 3px rgba(46,164,79,0.1); }

        .alert-error { background:#fdecea; color:#c0392b; border:1px solid #f5c6cb; border-radius:10px; padding:12px 15px; margin-bottom:18px; font-size:0.87rem; display:flex; align-items:center; gap:8px; }
        .alert-info  { background:#e8f5e9; color:#1a6b2e; border:1px solid #c8e6c9; border-radius:10px; padding:12px 15px; margin-bottom:18px; font-size:0.87rem; display:flex; align-items:center; gap:8px; }

        .btn-login {
            width:100%; padding:13px;
            background:linear-gradient(135deg,#1a472a,#2ea44f);
            color:#fff; border:none; border-radius:12px;
            font-size:1rem; font-weight:700; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:8px;
            transition:all 0.3s; margin-top:8px;
        }
        .btn-login:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(46,164,79,0.4); }

        .back-link { display:block; text-align:center; margin-top:20px; color:#888; font-size:0.85rem; text-decoration:none; }
        .back-link:hover { color:#2ea44f; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <div class="icon-wrap"><i class="fa-solid fa-utensils"></i></div>
            <h1>Kantin Ibun Sofi</h1>
            <p>Panel Administrasi</p>
            <div class="admin-badge"><i class="fa-solid fa-user-shield"></i> Admin Only</div>
        </div>

        <?php if($error): ?>
        <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if($info): ?>
        <div class="alert-info"><i class="fa-solid fa-circle-info"></i><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Username Admin</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user ico"></i>
                    <input type="text" name="username" class="form-control" required
                           placeholder="Username" autocomplete="username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock ico"></i>
                    <input type="password" name="password" class="form-control" required
                           placeholder="Password" autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk sebagai Admin
            </button>
        </form>

        <a href="../frontend/index.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke halaman pemesanan
        </a>
    </div>
</div>
</body>
</html>

