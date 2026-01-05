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
    $judul_buku = $conn->real_escape_string($_POST['judul_buku']); // Perbarui nama
    $pengarang = $conn->real_escape_string($_POST['pengarang']);
    $tahun_terbit = $conn->real_escape_string($_POST['tahun_terbit']);
    $penerbit = $conn->real_escape_string($_POST['penerbit']);
    $jumlah_halaman = $conn->real_escape_string($_POST['jumlah_halaman']); // Ditambahkan

    // Validasi
    if (empty($judul_buku) || empty($pengarang) || empty($tahun_terbit) || empty($penerbit) || empty($jumlah_halaman)) {
        $message = '<div class="alert alert-danger" role="alert">Semua field harus diisi!</div>';
    } else {
        // Query untuk menambahkan buku baru. id_buku akan di-generate otomatis oleh database.
        $sql_insert_buku = "INSERT INTO buku (judul_buku, pengarang, penerbit, tahun_terbit, jumlah_halaman) VALUES (?, ?, ?, ?, ?)"; // Perbarui kolom

        // Menggunakan prepared statement untuk keamanan
        $stmt_buku = $conn->prepare($sql_insert_buku);
        $stmt_buku->bind_param("ssssi", $judul_buku, $pengarang, $penerbit, $tahun_terbit, $jumlah_halaman); // Perbarui bind

        if ($stmt_buku->execute()) {
            $last_id_buku = $conn->insert_id; // Ambil ID buku yang baru saja di-generate
            $formatted_id_buku = '#'.sprintf('%04d', $last_id_buku); // Format ID

            $message = '<div class="alert alert-success" role="alert">Buku berhasil ditambahkan dengan ID: ' . $formatted_id_buku . '!</div>';

            // --- LOG AKTIVITAS ---
            $sql_log = "INSERT INTO log_aktivitas (user_id, tipe_aktivitas, deskripsi_aktivitas, referensi_id) VALUES (?, ?, ?, ?)"; // Perbarui kolom user_id
            $stmt_log = $conn->prepare($sql_log);
            $tipe_aktivitas = "Tambah Buku";
            $deskripsi_aktivitas = "Buku baru ditambahkan: \"" . $judul_buku . "\" (ID: " . $formatted_id_buku . ")";
            $stmt_log->bind_param("issi", $_SESSION['id_petugas'], $tipe_aktivitas, $deskripsi_aktivitas, $last_id_buku); // Sesuaikan bind_param
            $stmt_log->execute();
            $stmt_log->close();
            // --- AKHIR LOG AKTIVITAS ---

            // Opsional: Clear form fields setelah submit sukses
            $_POST = array(); // Mengosongkan POST data agar form bersih setelah submit
        } else {
            $message = '<div class="alert alert-danger" role="alert">Error: ' . $stmt_buku->error . '</div>';
        }
        $stmt_buku->close();
    }
}
$conn->close(); // Tutup koneksi setelah semua operasi selesai
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku Baru - Perpustakaan</title>
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
                <a href="data_buku.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i class="fas fa-book me-2"></i>Data Buku</a>
                <a href="data_anggota.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-users me-2"></i>Data Anggota</a>
                <a href="peminjaman.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-receipt me-2"></i>Peminjaman</a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0 text-white">Tambah Buku Baru</h2>
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
                                    <label for="judul_buku" class="form-label">Judul Buku</label>
                                    <input type="text" class="form-control" id="judul_buku" name="judul_buku" required> </div>
                                <div class="mb-3">
                                    <label for="pengarang" class="form-label">Pengarang</label>
                                    <input type="text" class="form-control" id="pengarang" name="pengarang" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                                    <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" min="1000" max="<?php echo date('Y'); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="penerbit" class="form-label">Penerbit</label>
                                    <input type="text" class="form-control" id="penerbit" name="penerbit" required>
                                </div>
                                <div class="mb-3">
                                    <label for="jumlah_halaman" class="form-label">Jumlah Halaman</label>
                                    <input type="number" class="form-control" id="jumlah_halaman" name="jumlah_halaman" min="1" required> </div>
                                <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan Buku</button>
                                <a href="data_buku.php" class="btn btn-secondary ms-2"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
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