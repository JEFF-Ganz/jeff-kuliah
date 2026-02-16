<?php
$page_title = 'Dashboard';
$css_path = 'css/style.css';
$js_path = 'js/script.js';

require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Ambil hari hari ini (nama hari dalam Inggris untuk di-format)
$hari_ini = date('l'); // Monday, Tuesday, dst
$hari_indonesia = [
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu'
];
$hari_display = $hari_indonesia[$hari_ini];

// Query jadwal kuliah hari ini
$stmt = $pdo->prepare("
    SELECT * FROM matakuliah 
    WHERE user_id = ? AND hari = ? 
    ORDER BY jam_mulai ASC
");
$stmt->execute([$user_id, $hari_display]);
$jadwal_hari_ini = $stmt->fetchAll();

// Query tugas dengan deadline terdekat (belum selesai)
$stmt = $pdo->prepare("
    SELECT t.*, mk.nama_mk 
    FROM tugas t 
    JOIN matakuliah mk ON t.matakuliah_id = mk.id
    WHERE t.user_id = ? AND t.status != 'Selesai'
    ORDER BY t.deadline ASC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$tugas_mendekati = $stmt->fetchAll();

// Hitung statistik
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM matakuliah WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_mk = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tugas WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_tugas = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tugas WHERE user_id = ? AND status = 'Selesai'");
$stmt->execute([$user_id]);
$tugas_selesai = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tugas WHERE user_id = ? AND status != 'Selesai'");
$stmt->execute([$user_id]);
$tugas_belum = $stmt->fetch()['total'];
?>

<main class="container">
    <div class="dashboard-header">
        <div class="welcome-section">
            <h2>🎓 Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Kelola jadwal, tugas, dan materi akademikmu dengan lebih efisien.</p>
        </div>

        <!-- Jam Digital Real-Time -->
        <div class="digital-clock">
            <div class="clock-display" id="clock">00:00:00</div>
            <p class="clock-date" id="date">Loading...</p>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-content">
                <h3><?php echo $total_mk; ?></h3>
                <p>Mata Kuliah</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <h3><?php echo $total_tugas; ?></h3>
                <p>Total Tugas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <h3><?php echo $tugas_selesai; ?></h3>
                <p>Tugas Selesai</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
                <h3><?php echo $tugas_belum; ?></h3>
                <p>Tugas Pending</p>
            </div>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <section class="dashboard-section">
        <h3>📅 Jadwal Kuliah Hari Ini (<?php echo $hari_display; ?>)</h3>
        <?php if (count($jadwal_hari_ini) > 0): ?>
            <div class="schedule-list">
                <?php foreach ($jadwal_hari_ini as $jadwal): ?>
                    <div class="schedule-card">
                        <div class="schedule-time">
                            <div class="time-badge"><?php echo htmlspecialchars($jadwal['jam_mulai']); ?></div>
                            <div class="time-duration">
                                s/d <?php echo htmlspecialchars($jadwal['jam_selesai']); ?>
                            </div>
                        </div>
                        <div class="schedule-info">
                            <h4><?php echo htmlspecialchars($jadwal['nama_mk']); ?></h4>
                            <p>👨‍🏫 Dosen: <?php echo htmlspecialchars($jadwal['dosen']); ?></p>
                            <p>🏫 Ruang: <?php echo htmlspecialchars($jadwal['ruang']); ?></p>
                        </div>
                        <div class="schedule-actions">
                            <a href="edit_jadwal.php?id=<?php echo $jadwal['id']; ?>" class="btn btn-small btn-edit">Edit</a>
                            <a href="delete_jadwal.php?id=<?php echo $jadwal['id']; ?>" class="btn btn-small btn-delete"
                                onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>📭 Tidak ada jadwal kuliah hari ini. Tenang, istirahat yang cukup! 😴</p>
                <a href="tambah_jadwal.php" class="btn btn-primary">+ Tambah Jadwal</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Tugas Mendekati Deadline -->
    <section class="dashboard-section">
        <h3>⏰ Tugas Mendekati Deadline</h3>
        <?php if (count($tugas_mendekati) > 0): ?>
            <div class="task-list">
                <?php foreach ($tugas_mendekati as $tugas):
                    $deadline = new DateTime($tugas['deadline']);
                    $today = new DateTime();
                    $diff = $today->diff($deadline)->days;
                    $status_class = '';

                    if ($diff < 0) {
                        $status_class = 'overdue';
                        $label = '⚠️ TERLAMBAT!';
                    } elseif ($diff == 0) {
                        $status_class = 'due-today';
                        $label = '🔴 Hari Ini';
                    } elseif ($diff <= 3) {
                        $status_class = 'urgent';
                        $label = '🟡 ' . $diff . ' hari lagi';
                    } else {
                        $status_class = 'normal';
                        $label = '🟢 ' . $diff . ' hari lagi';
                    }
                    ?>
                    <div class="task-card <?php echo $status_class; ?>">
                        <div class="task-header">
                            <h4><?php echo htmlspecialchars($tugas['judul']); ?></h4>
                            <span class="deadline-badge"><?php echo $label; ?></span>
                        </div>
                        <p class="task-mata-kuliah">📖 <?php echo htmlspecialchars($tugas['nama_mk']); ?></p>
                        <p class="task-deadline">📅 Deadline: <?php echo date('d M Y', strtotime($tugas['deadline'])); ?></p>
                        <p class="task-status">Status: <strong><?php echo htmlspecialchars($tugas['status']); ?></strong></p>
                        <div class="task-actions">
                            <a href="edit_tugas.php?id=<?php echo $tugas['id']; ?>" class="btn btn-small btn-edit">Edit</a>
                            <a href="delete_tugas.php?id=<?php echo $tugas['id']; ?>" class="btn btn-small btn-delete"
                                onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>🎉 Tidak ada tugas pending. Kamu hebat! 🏆</p>
                <a href="tambah_tugas.php" class="btn btn-primary">+ Tambah Tugas</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Notification Banner untuk Reminder -->
    <div id="reminder-notification" class="reminder-notification" style="display: none;"></div>
</main>

<?php require_once 'includes/footer.php'; ?>