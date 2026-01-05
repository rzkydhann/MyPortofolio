<?php
session_start();
if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php'; // Sertakan file koneksi database

$message = ''; // Variabel untuk menyimpan pesan sukses/error

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan sanitasi
    $nama = $conn->real_escape_string($_POST['nama']); // Perbarui nama
    $alamat = $conn->real_escape_string($_POST['alamat']); // Ditambahkan
    $no_telp = $conn->real_escape_string($_POST['no_telp']); // Perbarui nama

    // Validasi sederhana
    if (empty($nama) || empty($alamat) || empty($no_telp)) {
        $message = '<div class="alert alert-danger" role="alert">Nama, Alamat, dan No Telp harus diisi!</div>';
    } else {
        // Query untuk menambahkan anggota baru
        $sql_insert_anggota = "INSERT INTO anggota (nama, alamat, no_telp) VALUES (?, ?, ?)"; // Perbarui kolom

        // Menggunakan prepared statement untuk keamanan
        $stmt_anggota = $conn->prepare($sql_insert_anggota);
        $stmt_anggota->bind_param("sss", $nama, $alamat, $no_telp); // Perbarui bind

        if ($stmt_anggota->execute()) {
            $last_id_anggota = $conn->insert_id; // Ambil ID anggota yang baru saja di-generate
            $formatted_id_anggota = '#'.sprintf('%04d', $last_id_anggota);

            $message = '<div class="alert alert-success" role="alert">Anggota berhasil ditambahkan dengan ID: ' . $formatted_id_anggota . '!</div>';

            // --- LOG AKTIVITAS ---
            $sql_log = "INSERT INTO log_aktivitas (user_id, tipe_aktivitas, deskripsi_aktivitas, referensi_id) VALUES (?, ?, ?, ?)"; // Perbarui kolom user_id
            $stmt_log = $conn->prepare($sql_log);
            $tipe_aktivitas = "Tambah Anggota";
            $deskripsi_aktivitas = "Anggota baru ditambahkan: \"" . $nama . "\" (ID: " . $formatted_id_anggota . ")";
            $stmt_log->bind_param("issi", $_SESSION['id_petugas'], $tipe_aktivitas, $deskripsi_aktivitas, $last_id_anggota); // Sesuaikan bind_param
            $stmt_log->execute();
            $stmt_log->close();
            // --- AKHIR LOG AKTIVITAS ---

            // Opsional: Clear form fields
            $_POST = array(); // Mengosongkan POST data agar form bersih setelah submit
        } else {
            $message = '<div class="alert alert-danger" role="alert">Error: ' . $stmt_anggota->error . '</div>';
        }
        $stmt_anggota->close();
    }
}
$conn->close(); // Tutup koneksi setelah semua operasi selesai
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota Baru - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <div class="bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading py-4 border-bottom">
                <div class="d-flex align-items-center justify-content-center w-100 gap-2">
                    <img src="img/logo_perpuskita.png" alt="Logo Perpuskita" class="img-fluid" style="max-height: 40px;">
                    <span class="brand-text">Perpuskita</span>
                </div>
            </div>
            <div class="list-group list-group-flush my-3">
                <div class="profile-info text-center mb-4">
                    <img src="img/dwi_annisa.jpeg" alt="Dwi Annisa" class="rounded-circle mb-2" width="80" height="80">
                    <h5><?php echo htmlspecialchars($_SESSION['nama_petugas']); ?></h5>
                    <small><?php echo htmlspecialchars($_SESSION['role']); ?></small>
                </div>
                <a href="index.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="data_buku.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-book me-2"></i>Data Buku</a>
                <a href="data_anggota.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i class="fas fa-users me-2"></i>Data Anggota</a>
                <a href="peminjaman.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-receipt me-2"></i>Peminjaman</a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0 text-white">Tambah Anggota Baru</h2>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="btn btn-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <div class="row my-4">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card p-4">
                            <?php echo $message; // Tampilkan pesan ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required> </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" required> </div>
                                <div class="mb-3">
                                    <label for="no_telp" class="form-label">Nomor Telp</label>
                                    <input type="text" class="form-control" id="no_telp" name="no_telp" required> </div>
                                <button type="submit" class="btn btn-success"><i class="fas fa-user-plus me-2"></i>Tambah Anggota</button>
                                <a href="data_anggota.php" class="btn btn-secondary ms-2"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>
</body>
</html>