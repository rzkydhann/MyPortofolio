<?php
session_start();
if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php'; // Sertakan file koneksi database

$message = '';
$anggota_data = []; // Untuk menyimpan data anggota yang akan diedit

// Bagian 1: Mengambil Data Anggota yang Akan Diedit (saat halaman pertama kali dimuat)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_anggota = $conn->real_escape_string($_GET['id']); // Perbarui nama variabel

    $sql_get_anggota = "SELECT * FROM anggota WHERE id_anggota = ?"; // Perbarui nama kolom
    $stmt_get_anggota = $conn->prepare($sql_get_anggota);
    $stmt_get_anggota->bind_param("i", $id_anggota);
    $stmt_get_anggota->execute();
    $result_get_anggota = $stmt_get_anggota->get_result();

    if ($result_get_anggota->num_rows > 0) {
        $anggota_data = $result_get_anggota->fetch_assoc();
    } else {
        // Jika ID anggota tidak ditemukan, redirect atau tampilkan pesan error
        $message = '<div class="alert alert-danger" role="alert">Anggota tidak ditemukan!</div>';
        // header("Location: data_anggota.php?status=not_found");
        // exit();
    }
    $stmt_get_anggota->close();
} else if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // Jika tidak ada ID di URL dan bukan POST request
    $message = '<div class="alert alert-warning" role="alert">ID Anggota tidak valid.</div>';
    // header("Location: data_anggota.php");
    // exit();
}


// Bagian 2: Memproses Pembaruan Data Anggota (saat form disubmit)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_anggota = $conn->real_escape_string($_POST['id_anggota']); // ID anggota dari hidden input
    $nama = $conn->real_escape_string($_POST['nama']); // Perbarui nama
    $alamat = $conn->real_escape_string($_POST['alamat']); // Ditambahkan
    $no_telp = $conn->real_escape_string($_POST['no_telp']); // Perbarui nama

    // Validasi
    if (empty($nama) || empty($alamat) || empty($no_telp)) {
        $message = '<div class="alert alert-danger" role="alert">Semua field harus diisi!</div>';
    } else {
        // Query untuk update anggota
        $sql_update_anggota = "UPDATE anggota SET nama = ?, alamat = ?, no_telp = ? WHERE id_anggota = ?"; // Perbarui kolom

        $stmt_update_anggota = $conn->prepare($sql_update_anggota);
        $stmt_update_anggota->bind_param("sssi", $nama, $alamat, $no_telp, $id_anggota); // Perbarui bind

        if ($stmt_update_anggota->execute()) {
            $message = '<div class="alert alert-success" role="alert">Data anggota berhasil diperbarui!</div>';

            // --- LOG AKTIVITAS ---
            $sql_log = "INSERT INTO log_aktivitas (user_id, tipe_aktivitas, deskripsi_aktivitas, referensi_id) VALUES (?, ?, ?, ?)"; // Perbarui kolom user_id
            $stmt_log = $conn->prepare($sql_log);
            $tipe_aktivitas = "Edit Anggota";
            $deskripsi_aktivitas = "Data anggota \"" . $nama . "\" (ID: #" . sprintf('%04d', $id_anggota) . ") diperbarui.";
            $stmt_log->bind_param("issi", $_SESSION['id_petugas'], $tipe_aktivitas, $deskripsi_aktivitas, $id_anggota); // Sesuaikan bind_param
            $stmt_log->execute();
            $stmt_log->close();
            // --- AKHIR LOG AKTIVITAS ---

            // Setelah update, ambil kembali data terbaru untuk ditampilkan di form
            $sql_get_anggota_after_update = "SELECT * FROM anggota WHERE id_anggota = ?"; // Perbarui kolom
            $stmt_get_anggota_after_update = $conn->prepare($sql_get_anggota_after_update);
            $stmt_get_anggota_after_update->bind_param("i", $id_anggota);
            $stmt_get_anggota_after_update->execute();
            $result_get_anggota_after_update = $stmt_get_anggota_after_update->get_result();
            $anggota_data = $result_get_anggota_after_update->fetch_assoc();
            $stmt_get_anggota_after_update->close();

        } else {
            $message = '<div class="alert alert-danger" role="alert">Error memperbarui data: ' . $stmt_update_anggota->error . '</div>';
        }
        $stmt_update_anggota->close();
    }
}
$conn->close(); // Tutup koneksi setelah semua operasi selesai
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - Perpustakaan</title>
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
                    <h2 class="fs-2 m-0 text-white">Edit Data Anggota</h2>
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
                            <?php if (!empty($anggota_data)): // Tampilkan form hanya jika data anggota ditemukan ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <input type="hidden" name="id_anggota" value="<?php echo htmlspecialchars($anggota_data['id_anggota']); ?>"> <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($anggota_data['nama']); ?>" required> </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo htmlspecialchars($anggota_data['alamat']); ?>" required> </div>
                                <div class="mb-3">
                                    <label for="no_telp" class="form-label">Nomor Telp</label>
                                    <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?php echo htmlspecialchars($anggota_data['no_telp']); ?>" required> </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                                <a href="data_anggota.php" class="btn btn-secondary ms-2"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                            </form>
                            <?php else: ?>
                                <p class="text-center">Data anggota tidak dapat dimuat.</p>
                            <?php endif; ?>
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