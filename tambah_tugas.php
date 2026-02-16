<?php
$page_title = 'Manajemen Tugas & Upload';
$css_path = 'css/style.css';
$js_path = 'js/script.js';

require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Path upload folder
$upload_dir = 'uploads/tugas/';
$allowed_extensions = ['pdf', 'docx', 'zip', 'doc', 'txt'];
$max_file_size = 10 * 1024 * 1024; // 10MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $matakuliah_id = $_POST['matakuliah_id'] ?? '';
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $deadline = $_POST['deadline'] ?? '';
        $status = $_POST['status'] ?? 'Belum Dikerjakan';

        // Validasi input
        if (empty($matakuliah_id) || empty($judul) || empty($deadline)) {
            $error = '❌ Judul, Mata Kuliah, dan Deadline harus diisi!';
        } elseif (strtotime($deadline) < time()) {
            $error = '❌ Deadline tidak boleh tanggal yang sudah lewat!';
        } else {
            $file_path = null;

            // Proses upload file jika ada
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['file']['tmp_name'];
                $file_name = $_FILES['file']['name'];
                $file_size = $_FILES['file']['size'];

                // Validasi ukuran file
                if ($file_size > $max_file_size) {
                    $error = '❌ Ukuran file terlalu besar (max 10MB)!';
                } else {
                    // Validasi ekstensi
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    if (!in_array($file_ext, $allowed_extensions)) {
                        $error = '❌ Tipe file tidak diizinkan! (PDF, DOCX, ZIP, DOC, TXT)';
                    } else {
                        // Buat nama file unik
                        $new_filename = uniqid('tugas_') . '.' . $file_ext;
                        $file_path_full = $upload_dir . $new_filename;

                        // Buat folder jika belum ada
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        // Pindahkan file
                        if (move_uploaded_file($file_tmp, $file_path_full)) {
                            $file_path = $file_path_full;
                        } else {
                            $error = '❌ Gagal upload file!';
                        }
                    }
                }
            }

            // Jika tidak ada error, simpan tugas ke database
            if (empty($error)) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO tugas (user_id, matakuliah_id, judul, deskripsi, deadline, status, file_path) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$user_id, $matakuliah_id, $judul, $deskripsi, $deadline, $status, $file_path]);
                    $success = '✅ Tugas berhasil ditambahkan!';
                } catch (PDOException $e) {
                    $error = '❌ Error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Ambil daftar mata kuliah user
$stmt = $pdo->prepare("SELECT id, nama_mk FROM matakuliah WHERE user_id = ? ORDER BY nama_mk ASC");
$stmt->execute([$user_id]);
$daftar_mk = $stmt->fetchAll();

// Ambil semua tugas user
$stmt = $pdo->prepare("
    SELECT t.*, mk.nama_mk 
    FROM tugas t 
    JOIN matakuliah mk ON t.matakuliah_id = mk.id
    WHERE t.user_id = ? 
    ORDER BY t.deadline ASC
");
$stmt->execute([$user_id]);
$semua_tugas = $stmt->fetchAll();
?>

<main class="container">
    <div class="page-header">
        <h2>📝 Manajemen Tugas & Upload File</h2>
        <p>Kelola tugas akademik dan upload file dengan mudah</p>
    </div>

    <!-- Form Tambah Tugas -->
    <section class="form-section">
        <h3>➕ Tambah Tugas Baru</h3>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label for="matakuliah_id">Pilih Mata Kuliah</label>
                <select id="matakuliah_id" name="matakuliah_id" required>
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <?php foreach ($daftar_mk as $mk): ?>
                        <option value="<?php echo $mk['id']; ?>"><?php echo htmlspecialchars($mk['nama_mk']); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (count($daftar_mk) === 0): ?>
                    <p style="color: #e74c3c; margin-top: 5px;">⚠️ Tambahkan mata kuliah terlebih dahulu!</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="judul">Judul Tugas</label>
                <input type="text" id="judul" name="judul" placeholder="Contoh: Buat Program Java" required>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" placeholder="Jelaskan tugas Anda di sini..."
                    rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="deadline">Deadline</label>
                <input type="date" id="deadline" name="deadline" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Belum Dikerjakan">Belum Dikerjakan</option>
                    <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div class="form-group">
                <label for="file">Upload File (Opsional)</label>
                <input type="file" id="file" name="file" accept=".pdf,.docx,.zip,.doc,.txt">
                <small>📁 Format: PDF, DOCX, ZIP, DOC, TXT (Max 10MB)</small>
            </div>

            <button type="submit" class="btn btn-primary btn-block">💾 Simpan Tugas</button>
        </form>
    </section>

    <!-- Daftar Tugas -->
    <section class="list-section">
        <h3>📚 Daftar Tugas Anda</h3>

        <?php if (count($semua_tugas) > 0): ?>
            <div class="task-list">
                <?php foreach ($semua_tugas as $tugas):
                    $deadline = new DateTime($tugas['deadline']);
                    $today = new DateTime();
                    $diff = $today->diff($deadline)->days;

                    // Tentukan class status
                    if ($tugas['status'] === 'Selesai') {
                        $status_class = 'completed';
                    } elseif ($diff < 0) {
                        $status_class = 'overdue';
                    } elseif ($diff <= 3) {
                        $status_class = 'urgent';
                    } else {
                        $status_class = 'normal';
                    }
                    ?>
                    <div class="task-card <?php echo $status_class; ?>">
                        <div class="task-header">
                            <h4><?php echo htmlspecialchars($tugas['judul']); ?></h4>
                            <span
                                class="status-badge status-<?php echo str_replace(' ', '-', strtolower($tugas['status'])); ?>">
                                <?php echo htmlspecialchars($tugas['status']); ?>
                            </span>
                        </div>
                        <p class="task-mata-kuliah">📖 <?php echo htmlspecialchars($tugas['nama_mk']); ?></p>
                        <?php if (!empty($tugas['deskripsi'])): ?>
                            <p class="task-deskripsi">📝
                                <?php echo htmlspecialchars(substr($tugas['deskripsi'], 0, 100)) . '...'; ?></p>
                        <?php endif; ?>
                        <p class="task-deadline">📅 Deadline: <?php echo date('d M Y', strtotime($tugas['deadline'])); ?></p>

                        <?php if (!empty($tugas['file_path'])): ?>
                            <p class="task-file">
                                📎 <a href="<?php echo htmlspecialchars($tugas['file_path']); ?>" target="_blank">Lihat File</a>
                            </p>
                        <?php endif; ?>

                        <div class="task-actions">
                            <a href="edit_tugas.php?id=<?php echo $tugas['id']; ?>" class="btn btn-small btn-edit">Edit</a>
                            <a href="delete_tugas.php?id=<?php echo $tugas['id']; ?>" class="btn btn-small btn-delete"
                                onclick="return confirm('Yakin hapus tugas ini?')">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>📭 Belum ada tugas. Nikmati waktu luangmu! 😎</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>