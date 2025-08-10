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

$message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip = $_POST['nip'] ?? '';
    $nama_pegawai = $_POST['nama_pegawai'] ?? '';
    $tanggal = date('Y-m-d');
    $waktu_masuk = date('H:i:s');
    $keterangan = $_POST['keterangan'] ?? 'hadir';
    
    if(empty($nip) || empty($nama_pegawai)) {
        $message = "NIP dan nama pegawai harus diisi!";
    } else {
        $stmt = $conn->prepare("INSERT INTO absensi (nip, nama_pegawai, tanggal, waktu_masuk, keterangan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nip, $nama_pegawai, $tanggal, $waktu_masuk, $keterangan);
        
        if($stmt->execute()) {
            $message = "Absensi berhasil ditambahkan!";
        } else {
            $message = "Gagal menambahkan absensi: " . $conn->error;
        }
        
        $stmt->close();
    }
}

$absensi = $conn->query("SELECT * FROM absensi ORDER BY tanggal DESC, waktu_masuk DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - Sistem Absensi KPU Kota Cimahi</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
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
            <header class="page-header">
                <h1>Absensi Pegawai</h1>
                <p>Sistem Absensi KPU Kota Cimahi</p>
            </header>

            <?php if($message): ?>
                <div class="alert-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-section">
                <h2>Tambah Absensi</h2>
                <form method="POST" action="absensi.php" class="absensi-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" id="nip" name="nip" placeholder="Masukkan NIP" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_pegawai">Nama Pegawai</label>
                            <input type="text" id="nama_pegawai" name="nama_pegawai" placeholder="Masukkan Nama" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <select id="keterangan" name="keterangan">
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-plus"></i>
                        Tambah Absensi
                    </button>
                </form>
            </div>

            <div class="table-section">
                <h2>Daftar Absensi</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal</th>
                                <th>Waktu Masuk</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $absensi->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nip']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pegawai']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td><?php echo $row['waktu_masuk'] ? date('H:i', strtotime($row['waktu_masuk'])) : '-'; ?></td>
                                <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                <td>
                                    <a href="edit_absensi.php?id=<?php echo $row['no']; ?>" class="btn-edit">Edit</a>
                                    <a href="delete_absensi.php?id=<?php echo $row['no']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
