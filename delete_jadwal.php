<?php
session_start();
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (isset($_GET['id']) && $user_id) {
    $jadwal_id = intval($_GET['id']);

    // Verifikasi bahwa jadwal milik user yang login
    $stmt = $pdo->prepare("SELECT user_id FROM matakuliah WHERE id = ?");
    $stmt->execute([$jadwal_id]);
    $jadwal = $stmt->fetch();

    if ($jadwal && $jadwal['user_id'] == $user_id) {
        // Hapus jadwal
        $stmt = $pdo->prepare("DELETE FROM matakuliah WHERE id = ? AND user_id = ?");
        $stmt->execute([$jadwal_id, $user_id]);
    }
}

// Redirect ke halaman jadwal
header('Location: tambah_jadwal.php');
exit();
?>