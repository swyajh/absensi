<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$host = 'localhost';
$username_db = 'root';
$password_db = '';
$database = 'db_absensi';

$conn = new mysqli($host, $username_db, $password_db, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {

        $stmt = $conn->prepare("SELECT * FROM user WHERE nama = ? AND status = 'admin'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (md5($password) === $user['password']) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['nama'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Username atau password salah!';
            }
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi KPU Kota Cimahi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login-style.css">
    <link rel="icon" type="image/png" href="gambar/KPU_Logo.png">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="login-header">
                <img src="gambar/KPU_Logo.png" alt="KPU Logo" class="logo">
                <h1>Sistem Absensi</h1>
                <h2>KPU Kota Cimahi</h2>
                <p>Selamat datang di sistem absensi digital karyawan</p>
            </div>
            
            <div class="login-features">
                <div class="feature">
                    <i class="fas fa-fingerprint"></i>
                    <span>Absensi Digital</span>
                </div>
                <div class="feature">
                    <i class="fas fa-clock"></i>
                    <span>Real-time Monitoring</span>
                </div>
                <div class="feature">
                    <i class="fas fa-chart-line"></i>
                    <span>Laporan Otomatis</span>
                </div>
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <span>Keamanan Terjamin</span>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-form-container">
                <h3>Masuk ke Akun Anda</h3>
                <p class="subtitle">Silakan login dengan username dan password Anda</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form action="login.php" method="POST" class="login-form">
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i>
                            Username
                        </label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               placeholder="Masukkan username" 
                               required 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="password-input">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan password" 
                                   required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk
                    </button>
                </form>
                
                <div class="login-footer">
                    <p>&copy; 2024 KPU Kota Cimahi. All rights reserved.</p>
                    <p>Developed by IT Support KPU</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        document.querySelector('.login-form').addEventListener('submit', function() {
            const btn = document.querySelector('.login-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
