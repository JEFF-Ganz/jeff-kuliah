<?php
// Start session
session_start();

// Include database connection
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Student Academic Planner' : 'Student Academic Planner'; ?>
        </title>
        <link rel="stylesheet"
            href="<?php echo isset($css_path) ? htmlspecialchars($css_path) : 'css/style.min.css'; ?>">
    </head>

    <body>
        <!-- Navigation Bar -->
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <h1>📚 JEFF Kuliah</h1>
                </div>
                <ul class="nav-menu">
                    <?php if (isLoggedIn()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="tambah_jadwal.php">Jadwal</a></li>
                        <li><a href="tambah_tugas.php">Tugas</a></li>
                        <li><a href="tambah_materi.php">Materi</a></li>
                        <li><span class="username">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                        <li><a href="logout.php" class="btn-logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </body>

</html>