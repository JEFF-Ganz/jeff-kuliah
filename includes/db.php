<?php
// Load production config
if (file_exists(__DIR__ . '/../config.php')) {
    define('APP_ENV', 'production');
    require_once __DIR__ . '/../config.php';
}

// Database Config
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_akademik');

try {
    // Buat koneksi PDO
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Gagal koneksi database: ' . $e->getMessage());
}

/**
 * Fungsi untuk Register User
 */
function registerUser($pdo, $username, $password)
{
    // Cek apakah username sudah ada
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Username sudah terdaftar!'];
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert user baru
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");

    try {
        $stmt->execute([$username, $password_hash]);
        return ['success' => true, 'message' => 'Registrasi berhasil! Silahkan login.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

/**
 * Fungsi untuk Login User
 */
function loginUser($pdo, $username, $password)
{
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->execute([$username]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return ['success' => true, 'message' => 'Login berhasil!'];
    } else {
        return ['success' => false, 'message' => 'Username atau password salah!'];
    }
}

/**
 * Fungsi untuk Check Session User
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Fungsi untuk Redirect jika belum login
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}
?>