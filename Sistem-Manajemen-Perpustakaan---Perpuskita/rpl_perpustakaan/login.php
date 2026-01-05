<?php
session_start(); // Mulai session di awal setiap file PHP yang butuh session
include 'includes/db_connect.php';

$message = '';

// Jika user sudah login, arahkan ke dashboard
if (isset($_SESSION['id_petugas'])) {
    header("Location: index.php");
    exit();
}

// Proses login jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_petugas_input = $conn->real_escape_string($_POST['id_petugas']); // Ambil input ID petugas
    $password = $_POST['password']; // Password tidak perlu disanitasi dengan real_escape_string karena akan di-hash

    // Validasi bahwa id_petugas_input adalah angka
    if (!is_numeric($id_petugas_input)) {
        $message = '<div class="alert alert-danger">ID Petugas harus berupa angka.</div>';
    } else {
        // Ambil data user dari database berdasarkan id_petugas
        // Kita perlu nama_petugas untuk session dan log, jadi ambil juga.
        $sql = "SELECT id_petugas, nama_petugas, password, role FROM petugas WHERE id_petugas = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_petugas_input); // Bind sebagai integer (i)
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password yang dimasukkan dengan hash di database
            if (password_verify($password, $user['password'])) {
                // Login berhasil, set session
                $_SESSION['id_petugas'] = $user['id_petugas'];
                $_SESSION['nama_petugas'] = $user['nama_petugas']; // Tetap simpan nama_petugas untuk tampilan di sidebar
                $_SESSION['role'] = $user['role'];

                // --- LOG AKTIVITAS LOGIN ---
                $sql_log = "INSERT INTO log_aktivitas (user_id, tipe_aktivitas, deskripsi_aktivitas) VALUES (?, ?, ?)";
                $stmt_log = $conn->prepare($sql_log);
                $tipe_aktivitas = "Login";
                $deskripsi_aktivitas = "Petugas " . $user['nama_petugas'] . " (ID: " . $user['id_petugas'] . ") berhasil login.";
                $stmt_log->bind_param("iss", $user['id_petugas'], $tipe_aktivitas, $deskripsi_aktivitas);
                $stmt_log->execute();
                $stmt_log->close();
                // --- AKHIR LOG AKTIVITAS LOGIN ---

                header("Location: index.php"); // Arahkan ke dashboard
                exit();
            } else {
                $message = '<div class="alert alert-danger">ID Petugas atau password salah.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">ID Petugas atau password salah.</div>';
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpuskita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #28a745; /* Warna hijau tua seperti di gambar */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden; /* Mencegah scroll jika konten kecil */
        }
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px; /* Batasi lebar container */
            text-align: center;
        }
        .login-logo-section {
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .login-logo-section img {
            max-height: 50px; /* Ukuran logo */
        }
        .login-logo-section .brand-text {
            font-family: 'Lemon', cursive; /* Gunakan font Lemon yang sudah diimpor */
            font-size: 2.5rem; /* Ukuran font lebih besar untuk logo di halaman login */
            color: #28a745; /* Warna teks sesuai warna background */
        }
        .form-control-lg {
            height: calc(2.5em + 1rem + 2px); /* Sesuaikan tinggi input jika perlu */
        }
        .btn-masuk {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            padding: 10px 20px;
            font-size: 1.1rem;
            width: 100%;
        }
        .btn-masuk:hover {
            background-color: #218838; /* Slightly darker green on hover */
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo-section">
            <img src="img/logo_perpuskita.png" alt="Logo Perpuskita">
            <span class="brand-text">Perpuskita</span>
        </div>
        <?php echo $message; // Tampilkan pesan error/sukses ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control form-control-lg" id="id_petugas" name="id_petugas" placeholder="ID Petugas (NIP)" required>
            </div>
            <div class="mb-4">
                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-masuk">Masuk</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>