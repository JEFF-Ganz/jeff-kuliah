<?php
session_start();
require_once 'includes/db.php';

// Jika sudah login, arahkan ke dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validasi input
        if (empty($username) || empty($password)) {
            $error = '❌ Username dan password tidak boleh kosong!';
        } else {
            $result = loginUser($pdo, $username, $password);
            if ($result['success']) {
                header('Location: dashboard.php');
                exit();
            } else {
                $error = '❌ ' . $result['message'];
            }
        }
    } elseif ($action === 'register') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validasi input
        if (empty($username) || empty($password) || empty($password_confirm)) {
            $error = '❌ Semua field harus diisi!';
        } elseif ($password !== $password_confirm) {
            $error = '❌ Password tidak cocok!';
        } elseif (strlen($password) < 6) {
            $error = '❌ Password minimal 6 karakter!';
        } else {
            $result = registerUser($pdo, $username, $password);
            if ($result['success']) {
                $success = '✅ ' . $result['message'];
            } else {
                $error = '❌ ' . $result['message'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login & Register - Student Academic Planner</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body class="auth-page">
        <div class="auth-container">
            <div class="auth-box">
                <h1 class="auth-title">📚 JEFF Kuliah</h1>
                <p class="auth-subtitle">Kelola jadwal & tugas akademikmu dengan mudah!</p>

                <!-- Tab Navigation -->
                <div class="tabs">
                    <button class="tab-button active" onclick="switchTab('login')">Login</button>
                    <button class="tab-button" onclick="switchTab('register')">Daftar</button>
                </div>

                <!-- LOGIN FORM -->
                <form id="login-form" class="auth-form active" method="POST">
                    <input type="hidden" name="action" value="login">

                    <?php if ($error && strpos($_POST['action'] ?? '', 'login') !== false): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="login-username">Username</label>
                        <input type="text" id="login-username" name="username" placeholder="Masukkan username" required>
                    </div>

                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" placeholder="Masukkan password"
                            required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">🔓 Login</button>
                </form>

                <!-- REGISTER FORM -->
                <form id="register-form" class="auth-form" method="POST">
                    <input type="hidden" name="action" value="register">

                    <?php if ($error && strpos($_POST['action'] ?? '', 'register') !== false): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="register-username">Username</label>
                        <input type="text" id="register-username" name="username" placeholder="Pilih username" required>
                    </div>

                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" placeholder="Min. 6 karakter"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="register-password-confirm">Konfirmasi Password</label>
                        <input type="password" id="register-password-confirm" name="password_confirm"
                            placeholder="Ulangi password" required>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">✔️ Daftar Sekarang</button>
                </form>

                <div class="auth-info">
                    <p>Akun dummy untuk testing:</p>
                    <p><strong>Username:</strong> testuser</p>
                    <p><strong>Password:</strong> password123</p>
                </div>
            </div>
        </div>

        <script>
            function switchTab(tab) {
                // Hide all forms
                document.querySelectorAll('.auth-form').forEach(form => {
                    form.classList.remove('active');
                });

                // Remove active class from all buttons
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active');
                });

                // Show selected form
                document.getElementById(tab + '-form').classList.add('active');

                // Add active class to clicked button
                event.target.classList.add('active');
            }
        </script>
    </body>

</html>