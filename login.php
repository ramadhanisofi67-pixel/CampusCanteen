<?php
session_start();

// Jika sudah login, redirect
if (isset($_SESSION['role']) && isset($_SESSION['token'])) {
    include 'config/koneksi.php';
    $u  = mysqli_real_escape_string($conn, $_SESSION['username'] ?? '');
    $tk = mysqli_real_escape_string($conn, $_SESSION['token'] ?? '');
    $chk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT session_token, role FROM users WHERE username='$u' LIMIT 1"));
    if ($chk && $chk['session_token'] === $tk) {
        if ($chk['role'] === 'admin') {
            header("Location: backend/index.php");
            exit;
        } else {
            header("Location: frontend/index.php");
            exit;
        }
    }
}

include 'config/koneksi.php';
$error = '';
$info  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? 'user');

    // Validasi input
    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong!';
    } elseif ($role === 'user' && !preg_match('/^[A-Za-z]+$/', $username)) {
        $error = 'Untuk Mahasiswa: Username hanya boleh mengandung huruf (tanpa angka/spasi/simbol).';
    } elseif ($role === 'user' && !preg_match('/^[0-9]+$/', $password)) {
        $error = 'Untuk Mahasiswa: Password hanya boleh mengandung angka (tanpa huruf/spasi/simbol).';
    } else {
        $uname = mysqli_real_escape_string($conn, $username);
        $upass = mysqli_real_escape_string($conn, $password);

        if ($role === 'admin') {
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE username='$uname' AND password='$upass' AND role='admin' LIMIT 1"));
        } else {
            // Untuk Mahasiswa: Cek username dulu (apapun role-nya)
            $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$uname' LIMIT 1");
            if (mysqli_num_rows($cek) > 0) {
                $userCheck = mysqli_fetch_assoc($cek);
                if ($userCheck['role'] !== 'user') {
                    $error = 'Username ini milik Admin, tidak bisa digunakan untuk Mahasiswa!';
                    $user = false;
                } else {
                    if ($userCheck['password'] !== $password) {
                        $error = 'Username sudah terdaftar, tetapi password salah!';
                        $user = false;
                    } else {
                        $user = $userCheck;
                    }
                }
            } else {
                // Auto-register
                mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$uname', '$upass', 'user')");
                $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE username='$uname' AND role='user' LIMIT 1"));
            }
        }

        if (isset($user) && $user) {
            // Cek apakah akun sedang dipakai (token tidak kosong dan last_login < 2 jam yang lalu untuk mencegah nyangkut)
            if (!empty($user['session_token'])) {
                $last = strtotime($user['last_login']);
                if (time() - $last < 7200) { // 2 jam timeout
                    $error = "Akun ini sedang digunakan oleh perangkat lain! Silakan ganti/gunakan akun yang lain.";
                }
            }

            if (!$error) {
                $new_token = bin2hex(random_bytes(32));
                $now       = date('Y-m-d H:i:s');
                mysqli_query($conn,
                    "UPDATE users SET session_token='$new_token', last_login='$now' WHERE id_user='{$user['id_user']}'"
                );
                $_SESSION['id_user']  = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $role;
                $_SESSION['token']    = $new_token;

                if ($role === 'admin') {
                    header("Location: backend/index.php");
                } else {
                    header("Location: frontend/index.php");
                }
                exit;
            }
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
    <title>Login - Kantin Ibun Sofi</title>
    <link rel="stylesheet" href="assets/css/style.css?v=6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-wrapper { width: 100%; max-width: 420px; margin: 40px auto; padding: 20px; }
        .login-card {
            background: #fff; border-radius: 24px; padding: 40px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
            animation: slideUp 0.4s ease;
        }
        @keyframes slideUp { from{opacity:0;transform:translateY(25px)} to{opacity:1;transform:translateY(0)} }
        
        .login-logo { text-align:center; margin-bottom:25px; }
        .icon-wrap {
            width:70px; height:70px; background: linear-gradient(135deg,#1a472a,#2ea44f);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            margin:0 auto 10px; box-shadow:0 10px 25px rgba(46,164,79,0.3);
        }
        .icon-wrap i { color:#fff; font-size:1.8rem; }
        .login-logo h1 { font-size:1.4rem; color:#1a472a; margin:0; }

        .tabs { display:flex; gap:10px; margin-bottom:25px; }
        .tab-btn {
            flex:1; padding:12px; border:none; background:#f4f7f6; border-radius:12px;
            font-weight:600; color:#555; cursor:pointer; transition:all 0.3s;
        }
        .tab-btn.active { background:var(--primary-color); color:#fff; box-shadow:0 5px 15px rgba(46,164,79,0.3); }

        .form-group { margin-bottom:18px; }
        .form-group label { display:block; font-weight:600; font-size:0.87rem; color:#333; margin-bottom:6px; }
        .input-wrap { position:relative; }
        .input-wrap .ico { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#2ea44f; font-size:0.9rem; }
        .form-control {
            width:100%; padding:12px 15px 12px 42px; border:2px solid #e5e5e5; border-radius:10px;
            font-size:0.95rem; background:#fafafa; transition:all 0.25s;
        }
        .form-control:focus { outline:none; border-color:#2ea44f; background:#fff; }
        
        .btn-login {
            width:100%; padding:13px; background:linear-gradient(135deg,#1a472a,#2ea44f);
            color:#fff; border:none; border-radius:12px; font-weight:700; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:8px;
            transition:all 0.3s; margin-top:10px; font-size:1rem;
        }
        .btn-login:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(46,164,79,0.4); }

        .alert-error { background:#fdecea; color:#c0392b; border:1px solid #f5c6cb; border-radius:10px; padding:12px 15px; margin-bottom:18px; font-size:0.87rem; display:flex; align-items:center; gap:8px; }
        
        .validation-hint { font-size: 0.75rem; color: #888; margin-top: 5px; display: block; }
        
        .account-list { margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; }
        .account-list h4 { font-size: 0.9rem; color: #555; margin-bottom: 10px; }
        .account-badges { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; }
        .badge { background: #e8f5e9; color: #2ea44f; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; border: 1px solid #c8e6c9; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <div class="icon-wrap"><i class="fa-solid fa-utensils"></i></div>
            <h1>Kantin Ibun Sofi</h1>
            <p style="color:#777; font-size:0.85rem; margin-top:5px;">Silakan Login Terlebih Dahulu</p>
        </div>

        <?php if($error): ?>
        <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-btn <?= (($_POST['role']??'user')==='user') ? 'active' : '' ?>" onclick="switchRole('user')" id="tab-user">
                <i class="fa-solid fa-user-graduate"></i> Mahasiswa
            </button>
            <button class="tab-btn <?= (($_POST['role']??'')==='admin') ? 'active' : '' ?>" onclick="switchRole('admin')" id="tab-admin">
                <i class="fa-solid fa-user-shield"></i> Admin
            </button>
        </div>

        <form method="POST" action="login.php" id="loginForm">
            <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($_POST['role']??'user') ?>">
            
            <div class="form-group">
                <label id="label-username">Username Mahasiswa</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user ico"></i>
                    <input type="text" name="username" id="username" class="form-control" required
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock ico"></i>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                
            </div>
            
            <button type="submit" class="btn-login" id="btn-submit">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
            </button>
        </form>

        </div>
    </div>
</div>

<script>
    function switchRole(role) {
        document.getElementById('roleInput').value = role;
        document.getElementById('tab-user').classList.toggle('active', role === 'user');
        document.getElementById('tab-admin').classList.toggle('active', role === 'admin');
        
        const uLabel = document.getElementById('label-username');
        const uHint = document.getElementById('hint-username');
        const pHint = document.getElementById('hint-password');
        const accList = document.getElementById('accountList');
        const btn = document.getElementById('btn-submit');
        const uname = document.getElementById('username');
        const pass = document.getElementById('password');

        if(role === 'admin') {
            uLabel.innerText = 'Username Admin';
            
            
            
            btn.innerHTML = '<i class="fa-solid fa-user-shield"></i> Masuk sebagai Admin';
            
            uname.placeholder = 'Username Admin';
            pass.placeholder = 'Password Admin';
            
            
        } else {
            uLabel.innerText = 'Username Mahasiswa';
            
            
            
            btn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang';
            
            uname.placeholder = 'Ketik username bebas...';
            pass.placeholder = 'Ketik password bebas...';
            
            
        }
    }

    // Initialize UI on load
    switchRole(document.getElementById('roleInput').value);

    </script>
</body>
</html>


