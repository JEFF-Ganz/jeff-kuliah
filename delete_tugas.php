<?php
session_start();
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (isset($_GET['id']) && $user_id) {
    $tugas_id = intval($_GET['id']);

    // Verifikasi dan ambil tugas
    $stmt = $pdo->prepare("SELECT user_id, file_path FROM tugas WHERE id = ?");
    $stmt->execute([$tugas_id]);
    $tugas = $stmt->fetch();

    if ($tugas && $tugas['user_id'] == $user_id) {
        // Hapus file jika ada
        if (!empty($tugas['file_path']) && file_exists($tugas['file_path'])) {
            unlink($tugas['file_path']);
        }

        // Hapus tugas dari database
        $stmt = $pdo->prepare("DELETE FROM tugas WHERE id = ? AND user_id = ?");
        $stmt->execute([$tugas_id, $user_id]);
    }
}

// Redirect ke halaman tugas
header('Location: tambah_tugas.php');
exit();
?>