<?php
session_start();
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (isset($_GET['id']) && $user_id) {
    $materi_id = intval($_GET['id']);

    // Verifikasi dan ambil materi
    $stmt = $pdo->prepare("SELECT user_id, file_path FROM materi WHERE id = ?");
    $stmt->execute([$materi_id]);
    $materi = $stmt->fetch();

    if ($materi && $materi['user_id'] == $user_id) {
        // Hapus file
        if (file_exists($materi['file_path'])) {
            unlink($materi['file_path']);
        }

        // Hapus materi dari database
        $stmt = $pdo->prepare("DELETE FROM materi WHERE id = ? AND user_id = ?");
        $stmt->execute([$materi_id, $user_id]);
    }
}

// Redirect ke halaman materi
header('Location: tambah_materi.php');
exit();
?>