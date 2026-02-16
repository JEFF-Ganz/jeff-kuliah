<?php
$page_title = 'Manajemen Jadwal Kuliah';
$css_path = 'css/style.css';
$js_path = 'js/script.js';

require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Array untuk hari-hari dalam seminggu
$daftar_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $nama_mk = trim($_POST['nama_mk'] ?? '');
        $dosen = trim($_POST['dosen'] ?? '');
        $hari = $_POST['hari'] ?? '';
        $jam_mulai = $_POST['jam_mulai'] ?? '';
        $jam_selesai = $_POST['jam_selesai'] ?? '';
        $ruang = trim($_POST['ruang'] ?? '');

        // Validasi input
        if (empty($nama_mk) || empty($dosen) || empty($hari) || empty($jam_mulai) || empty($jam_selesai) || empty($ruang)) {
            $error = '❌ Semua field harus diisi!';
        } elseif ($jam_mulai >= $jam_selesai) {
            $error = '❌ Jam mulai harus lebih kecil dari jam selesai!';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO matakuliah (user_id, nama_mk, dosen, hari, jam_mulai, jam_selesai, ruang) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$user_id, $nama_mk, $dosen, $hari, $jam_mulai, $jam_selesai, $ruang]);
                $success = '✅ Jadwal kuliah berhasil ditambahkan!';
            } catch (PDOException $e) {
                $error = '❌ Error: ' . $e->getMessage();
            }
        }
    }
}

// Ambil semua jadwal kuliah user
$stmt = $pdo->prepare("SELECT * FROM matakuliah WHERE user_id = ? ORDER BY hari ASC, jam_mulai ASC");
$stmt->execute([$user_id]);
$semua_jadwal = $stmt->fetchAll();
?>

<main class="container">
    <div class="page-header">
        <h2>📅 Manajemen Jadwal Kuliah</h2>
        <p>Kelola jadwal kuliah Anda dengan mudah</p>
    </div>

    <!-- Form Tambah Jadwal -->
    <section class="form-section">
        <h3>➕ Tambah Jadwal Baru</h3>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" class="form-grid">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label for="nama_mk">Nama Mata Kuliah</label>
                <input type="text" id="nama_mk" name="nama_mk" placeholder="Contoh: Pemrograman Web" required>
            </div>

            <div class="form-group">
                <label for="dosen">Nama Dosen</label>
                <input type="text" id="dosen" name="dosen" placeholder="Contoh: Dr. Ahmad Wijaya" required>
            </div>

            <div class="form-group">
                <label for="hari">Hari</label>
                <select id="hari" name="hari" required>
                    <option value="">-- Pilih Hari --</option>
                    <?php foreach ($daftar_hari as $h): ?>
                        <option value="<?php echo $h; ?>"><?php echo $h; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" id="jam_mulai" name="jam_mulai" required>
            </div>

            <div class="form-group">
                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" id="jam_selesai" name="jam_selesai" required>
            </div>

            <div class="form-group">
                <label for="ruang">Ruang Kelas</label>
                <input type="text" id="ruang" name="ruang" placeholder="Contoh: A101" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">💾 Simpan Jadwal</button>
        </form>
    </section>

    <!-- Daftar Jadwal -->
    <section class="list-section">
        <h3>📚 Daftar Jadwal Kuliah Anda</h3>

        <?php if (count($semua_jadwal) > 0): ?>
            <div class="schedule-list">
                <?php foreach ($semua_jadwal as $jadwal): ?>
                    <div class="schedule-card">
                        <div class="schedule-time">
                            <div class="time-badge"><?php echo htmlspecialchars($jadwal['jam_mulai']); ?></div>
                            <div class="time-duration">s/d <?php echo htmlspecialchars($jadwal['jam_selesai']); ?></div>
                        </div>
                        <div class="schedule-info">
                            <h4><?php echo htmlspecialchars($jadwal['nama_mk']); ?></h4>
                            <p>👨‍🏫 Dosen: <?php echo htmlspecialchars($jadwal['dosen']); ?></p>
                            <p>📅 Hari: <strong><?php echo htmlspecialchars($jadwal['hari']); ?></strong></p>
                            <p>🏫 Ruang: <?php echo htmlspecialchars($jadwal['ruang']); ?></p>
                        </div>
                        <div class="schedule-actions">
                            <a href="edit_jadwal.php?id=<?php echo $jadwal['id']; ?>" class="btn btn-small btn-edit">Edit</a>
                            <a href="delete_jadwal.php?id=<?php echo $jadwal['id']; ?>" class="btn btn-small btn-delete"
                                onclick="return confirm('Yakin hapus jadwal ini?')">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>📭 Belum ada jadwal kuliah. Mulai dengan menambahkan jadwal baru!</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>