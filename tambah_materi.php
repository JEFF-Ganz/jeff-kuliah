<?php
$page_title = 'Manajemen Materi Kuliah';
$css_path = 'css/style.css';
$js_path = 'js/script.js';

require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Path upload folder
$upload_dir = 'uploads/materi/';
$allowed_extensions = ['pdf', 'docx', 'pptx', 'xlsx', 'doc', 'txt'];
$max_file_size = 20 * 1024 * 1024; // 20MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $matakuliah_id = $_POST['matakuliah_id'] ?? '';
        $judul = trim($_POST['judul'] ?? '');

        // Validasi input
        if (empty($matakuliah_id) || empty($judul)) {
            $error = '❌ Judul dan Mata Kuliah harus diisi!';
        } else if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
            $error = '❌ File materi harus diunggah!';
        } else {
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];

            // Validasi ukuran file
            if ($file_size > $max_file_size) {
                $error = '❌ Ukuran file terlalu besar (max 20MB)!';
            } else {
                // Validasi ekstensi
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($file_ext, $allowed_extensions)) {
                    $error = '❌ Tipe file tidak diizinkan! (PDF, DOCX, PPTX, XLSX, DOC, TXT)';
                } else {
                    // Buat nama file unik
                    $new_filename = uniqid('materi_') . '.' . $file_ext;
                    $file_path_full = $upload_dir . $new_filename;

                    // Buat folder jika belum ada
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    // Pindahkan file
                    if (move_uploaded_file($file_tmp, $file_path_full)) {
                        // Simpan ke database
                        try {
                            $stmt = $pdo->prepare("
                                INSERT INTO materi (user_id, matakuliah_id, judul, file_path) 
                                VALUES (?, ?, ?, ?)
                            ");
                            $stmt->execute([$user_id, $matakuliah_id, $judul, $file_path_full]);
                            $success = '✅ Materi berhasil diunggah!';
                        } catch (PDOException $e) {
                            $error = '❌ Error: ' . $e->getMessage();
                        }
                    } else {
                        $error = '❌ Gagal upload file!';
                    }
                }
            }
        }
    }
}

// Ambil daftar mata kuliah user
$stmt = $pdo->prepare("SELECT id, nama_mk FROM matakuliah WHERE user_id = ? ORDER BY nama_mk ASC");
$stmt->execute([$user_id]);
$daftar_mk = $stmt->fetchAll();

// Ambil semua materi user
$stmt = $pdo->prepare("
    SELECT m.*, mk.nama_mk 
    FROM materi m 
    JOIN matakuliah mk ON m.matakuliah_id = mk.id
    WHERE m.user_id = ? 
    ORDER BY m.created_at DESC
");
$stmt->execute([$user_id]);
$semua_materi = $stmt->fetchAll();
?>

<main class="container">
    <div class="page-header">
        <h2>📚 Manajemen Materi Kuliah</h2>
        <p>Upload dan kelola bahan ajar kuliah Anda</p>
    </div>

    <!-- Form Upload Materi -->
    <section class="form-section">
        <h3>📤 Upload Materi Baru</h3>

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
                <label for="judul">Judul Materi</label>
                <input type="text" id="judul" name="judul" placeholder="Contoh: BAB 1 - Pengenalan Pemrograman"
                    required>
            </div>

            <div class="form-group">
                <label for="file">Upload File Materi</label>
                <input type="file" id="file" name="file" required accept=".pdf,.docx,.pptx,.xlsx,.doc,.txt">
                <small>📁 Format: PDF, DOCX, PPTX, XLSX, DOC, TXT (Max 20MB)</small>
            </div>

            <button type="submit" class="btn btn-primary btn-block">📤 Upload Materi</button>
        </form>
    </section>

    <!-- Daftar Materi -->
    <section class="list-section">
        <h3>📖 Daftar Materi Anda</h3>

        <?php if (count($semua_materi) > 0): ?>
            <div class="materi-grid">
                <?php foreach ($semua_materi as $materi):
                    $file_ext = strtoupper(pathinfo($materi['file_path'], PATHINFO_EXTENSION));
                    ?>
                    <div class="materi-card">
                        <div class="materi-icon">
                            <?php
                            $ext = strtolower(pathinfo($materi['file_path'], PATHINFO_EXTENSION));
                            if ($ext === 'pdf')
                                echo '📄';
                            elseif (in_array($ext, ['doc', 'docx']))
                                echo '📝';
                            elseif (in_array($ext, ['ppt', 'pptx']))
                                echo '🎁';
                            elseif (in_array($ext, ['xls', 'xlsx']))
                                echo '📊';
                            else
                                echo '📎';
                            ?>
                        </div>
                        <h4><?php echo htmlspecialchars($materi['judul']); ?></h4>
                        <p class="materi-mk">📚 <?php echo htmlspecialchars($materi['nama_mk']); ?></p>
                        <p class="materi-date">📅 <?php echo date('d M Y H:i', strtotime($materi['created_at'])); ?></p>
                        <p class="materi-type">Tipe: <strong><?php echo $file_ext; ?></strong></p>

                        <div class="materi-actions">
                            <a href="<?php echo htmlspecialchars($materi['file_path']); ?>" class="btn btn-small btn-primary"
                                target="_blank">📥 Download</a>
                            <a href="delete_materi.php?id=<?php echo $materi['id']; ?>" class="btn btn-small btn-delete"
                                onclick="return confirm('Yakin hapus materi ini?')">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>📭 Belum ada materi yang diunggah. Mulai dengan menambahkan materi baru!</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>