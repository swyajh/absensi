<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$dbname = 'db_absensi';
$username_db = 'root';
$password_db = '';

$conn = new mysqli($host, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$today = date('Y-m-d');

$total_pegawai = $conn->query("SELECT COUNT(DISTINCT nip) as total FROM absensi")->fetch_assoc()['total'];
$hadir_today = $conn->query("SELECT COUNT(*) as total FROM absensi WHERE tanggal = '$today' AND keterangan = 'hadir'")->fetch_assoc()['total'];
$izin_today = $conn->query("SELECT COUNT(*) as total FROM absensi WHERE tanggal = '$today' AND keterangan = 'izin'")->fetch_assoc()['total'];
$sakit_today = $conn->query("SELECT COUNT(*) as total FROM absensi WHERE tanggal = '$today' AND keterangan = 'sakit'")->fetch_assoc()['total'];

$recent_absensi = $conn->query("SELECT * FROM absensi ORDER BY tanggal DESC, waktu_masuk DESC LIMIT 10");

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Absensi KPU Kota Cimahi</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3>KPU CIMAHI</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="absensi.php"><i class="fas fa-clock"></i> Absensi</a></li>
                <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Laporan</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
                <p>Dashboard Sistem Absensi KPU Kota Cimahi</p>
            </header>

            <div class="stats-container">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3>Total Pegawai</h3>
                    <p><?php echo $total_pegawai; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <h3>Hadir Hari Ini</h3>
                    <p><?php echo $hadir_today; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Izin Hari Ini</h3>
                    <p><?php echo $izin_today; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-medkit"></i>
                    <h3>Sakit Hari Ini</h3>
                    <p><?php echo $sakit_today; ?></p>
                </div>
            </div>

            <div class="recent-activity">
                <h2>Aktivitas Terbaru</h2>
                <div class="activity-table">
                    <table>
                        <thead>
                            <tr>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal</th>
                                <th>Waktu Masuk</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $recent_absensi->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nip']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pegawai']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td><?php echo $row['waktu_masuk'] ? date('H:i', strtotime($row['waktu_masuk'])) : '-'; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $row['keterangan']; ?>">
                                        <?php echo strtoupper($row['keterangan']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
