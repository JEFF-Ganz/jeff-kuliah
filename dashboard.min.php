<?php $p = 'dashboard';
$c = 'css/style.min.css';
$j = 'js/script.min.js';
require 'includes/header.php';
requireLogin();
$u = $_SESSION['user_id'];
$h = date('l');
$hi = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'][$h];
$s = $pdo->prepare("SELECT * FROM matakuliah WHERE user_id = ? AND hari = ? ORDER BY jam_mulai ASC");
$s->execute([$u, $hi]);
$jh = $s->fetchAll();
$s = $pdo->prepare("SELECT t.*, mk.nama_mk FROM tugas t JOIN matakuliah mk ON t.matakuliah_id = mk.id WHERE t.user_id = ? AND t.status != 'Selesai' ORDER BY t.deadline ASC LIMIT 5");
$s->execute([$u]);
$tm = $s->fetchAll();
$s = $pdo->prepare("SELECT COUNT(*) as total FROM matakuliah WHERE user_id = ?");
$s->execute([$u]);
$tmk = $s->fetch()['total'];
$s = $pdo->prepare("SELECT COUNT(*) as total FROM tugas WHERE user_id = ?");
$s->execute([$u]);
$ttg = $s->fetch()['total'];
$s = $pdo->prepare("SELECT COUNT(*) as total FROM tugas WHERE user_id = ? AND status = 'Selesai'");
$s->execute([$u]);
$ts = $s->fetch()['total'];
$s = $pdo->prepare("SELECT COUNT(*) as total FROM tugas WHERE user_id = ? AND status != 'Selesai'");
$s->execute([$u]);
$tb = $s->fetch()['total']; ?>
<main class="container">
    <div class="dashboard-header">
        <div class="welcome-section">
            <h2>🎓 Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Kelola jadwal, tugas, dan materi akademikmu dengan lebih efisien.</p>
        </div>
        <div class="digital-clock">
            <div class="clock-display" id="clock">00:00:00</div>
            <p class="clock-date" id="date">Loading...</p>
        </div>
    </div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-content">
                <h3><?php echo $tmk; ?></h3>
                <p>Mata Kuliah</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <h3><?php echo $ttg; ?></h3>
                <p>Total Tugas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <h3><?php echo $ts; ?></h3>
                <p>Tugas Selesai</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
                <h3><?php echo $tb; ?></h3>
                <p>Tugas Pending</p>
            </div>
        </div>
    </div>
    <section class="dashboard-section">
        <h3>📅 Jadwal Kuliah Hari Ini (<?php echo $hi; ?>)</h3><?php if (count($jh) > 0): ?>
            <div class="schedule-list"><?php foreach ($jh as $jw): ?>
                    <div class="schedule-card">
                        <div class="schedule-time">
                            <div class="time-badge"><?php echo htmlspecialchars($jw['jam_mulai']); ?></div>
                            <div class="time-duration">s/d <?php echo htmlspecialchars($jw['jam_selesai']); ?></div>
                        </div>
                        <div class="schedule-info">
                            <h4><?php echo htmlspecialchars($jw['nama_mk']); ?></h4>
                            <p>👨‍🏫 Dosen: <?php echo htmlspecialchars($jw['dosen']); ?></p>
                            <p>🏫 Ruang: <?php echo htmlspecialchars($jw['ruang']); ?></p>
                        </div>
                        <div class="schedule-actions"><a href="edit_jadwal.php?id=<?php echo $jw['id']; ?>"
                                class="btn btn-small btn-edit">Edit</a><a href="delete_jadwal.php?id=<?php echo $jw['id']; ?>"
                                class="btn btn-small btn-delete" onclick="return confirm('Yakin hapus?')">Hapus</a></div>
                    </div><?php endforeach; ?>
            </div><?php else: ?>
            <div class="empty-state">
                <p>📭 Tidak ada jadwal kuliah hari ini. Tenang, istirahat yang cukup! 😴</p><a href="tambah_jadwal.php"
                    class="btn btn-primary">+ Tambah Jadwal</a>
            </div><?php endif; ?>
    </section>
    <section class="dashboard-section">
        <h3>⏰ Tugas Mendekati Deadline</h3><?php if (count($tm) > 0): ?>
            <div class="task-list">
                <?php foreach ($tm as $tsk) {
                    $dl = new DateTime($tsk['deadline']);
                    $td = new DateTime();
                    $df = $td->diff($dl)->days;
                    if ($df < 0) {
                        $sc = 'overdue';
                        $lb = '⚠️ TERLAMBAT!';
                    } elseif ($df == 0) {
                        $sc = 'due-today';
                        $lb = '🔴 Hari Ini';
                    } elseif ($df <= 3) {
                        $sc = 'urgent';
                        $lb = '🟡 ' . $df . ' hari lagi';
                    } else {
                        $sc = 'normal';
                        $lb = '🟢 ' . $df . ' hari lagi';
                    } ?>
                    <div class="task-card <?php echo $sc; ?>">
                        <div class="task-header">
                            <h4><?php echo htmlspecialchars($tsk['judul']); ?></h4><span
                                class="deadline-badge"><?php echo $lb; ?></span>
                        </div>
                        <p class="task-mata-kuliah">📖 <?php echo htmlspecialchars($tsk['nama_mk']); ?></p>
                        <p class="task-deadline">📅 Deadline: <?php echo date('d M Y', strtotime($tsk['deadline'])); ?></p>
                        <p class="task-status">Status: <strong><?php echo htmlspecialchars($tsk['status']); ?></strong></p>
                        <div class="task-actions"><a href="edit_tugas.php?id=<?php echo $tsk['id']; ?>"
                                class="btn btn-small btn-edit">Edit</a><a href="delete_tugas.php?id=<?php echo $tsk['id']; ?>"
                                class="btn btn-small btn-delete" onclick="return confirm('Yakin hapus?')">Hapus</a></div>
                    </div><?php } ?>
            </div><?php else: ?>
            <div class="empty-state">
                <p>🎉 Tidak ada tugas pending. Kamu hebat! 🏆</p><a href="tambah_tugas.php" class="btn btn-primary">+ Tambah
                    Tugas</a>
            </div><?php endif; ?>
    </section>
    <div id="reminder-notification" class="reminder-notification" style="display: none;"></div>
</main><?php require 'includes/footer.php'; ?>