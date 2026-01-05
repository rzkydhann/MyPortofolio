<?php
session_start(); // Mulai session

// Cek apakah user sudah login
if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php"); // Arahkan ke halaman login jika belum login
    exit();
}
include 'includes/db_connect.php'; // Sertakan file koneksi database

// --- Ambil Data untuk Dashboard Summary ---

// Ambil Total Buku
$sql_total_buku = "SELECT COUNT(*) AS total_buku FROM buku";
$result_total_buku = $conn->query($sql_total_buku);
$total_buku = 0;
if ($result_total_buku && $result_total_buku->num_rows > 0) {
    $row_buku = $result_total_buku->fetch_assoc();
    $total_buku = $row_buku['total_buku'];
}

// Ambil Total Anggota
$sql_total_anggota = "SELECT COUNT(*) AS total_anggota FROM anggota";
$result_total_anggota = $conn->query($sql_total_anggota);
$total_anggota = 0;
if ($result_total_anggota && $result_total_anggota->num_rows > 0) {
    $row_anggota = $result_total_anggota->fetch_assoc();
    $total_anggota = $row_anggota['total_anggota'];
}

// Ambil Total Peminjaman (semua catatan peminjaman)
$sql_total_peminjaman = "SELECT COUNT(*) AS total_peminjaman FROM peminjaman";
$result_total_peminjaman = $conn->query($sql_total_peminjaman);
$total_peminjaman = 0;
if ($result_total_peminjaman && $result_total_peminjaman->num_rows > 0) {
    $row_peminjaman = $result_total_peminjaman->fetch_assoc();
    $total_peminjaman = $row_peminjaman['total_peminjaman'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Perpustakaan</title>
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
                <a href="index.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="data_buku.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-book me-2"></i>Data Buku</a>
                <a href="data_anggota.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-users me-2"></i>Data Anggota</a>
                <a href="peminjaman.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-receipt me-2"></i>Peminjaman</a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0 text-white">Dashboard</h2>
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
                <div class="row g-3 my-2">
                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                            <div>
                                <p class="fs-5 mb-0">Total Buku</p>
                                <h3 class="fs-2"><?php echo $total_buku; ?></h3>
                            </div>
                            <i class="fas fa-book"></i>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                            <div>
                                <p class="fs-5 mb-0">Total Anggota</p>
                                <h3 class="fs-2"><?php echo $total_anggota; ?></h3>
                            </div>
                            <i class="fas fa-users"></i>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                            <div>
                                <p class="fs-5 mb-0">Total Peminjaman</p>
                                <h3 class="fs-2"><?php echo $total_peminjaman; ?></h3>
                            </div>
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>

                <div class="row my-4">
                    <h3 class="fs-4 mb-3">Aktivitas Terbaru</h3>
                    <div class="col">
                        <?php
                        // Ambil 5 aktivitas terbaru dari tabel log_aktivitas
                        $sql_aktivitas = "SELECT * FROM log_aktivitas ORDER BY timestamp DESC LIMIT 5";
                        $result_aktivitas = $conn->query($sql_aktivitas);

                        if ($result_aktivitas->num_rows > 0) {
                            while($row_aktivitas = $result_aktivitas->fetch_assoc()) {
                                $icon_class = '';
                                if (strpos($row_aktivitas['tipe_aktivitas'], 'Buku') !== false) {
                                    $icon_class = 'fas fa-book';
                                } elseif (strpos($row_aktivitas['tipe_aktivitas'], 'Anggota') !== false) {
                                    $icon_class = 'fas fa-user-plus';
                                } elseif (strpos($row_aktivitas['tipe_aktivitas'], 'Peminjaman') !== false) {
                                    $icon_class = 'fas fa-hand-holding-usd';
                                } elseif (strpos($row_aktivitas['tipe_aktivitas'], 'Pengembalian') !== false) {
                                    $icon_class = 'fas fa-undo-alt';
                                } elseif (strpos($row_aktivitas['tipe_aktivitas'], 'Login') !== false) {
                                    $icon_class = 'fas fa-info-circle';
                                } elseif (strpos($row_aktivitas['tipe_aktivitas'], 'Logout') !== false) {
                                    $icon_class = 'fas fa-sign-out-alt';
                                } else {
                                    $icon_class = 'fas fa-info-circle'; // Default icon
                                }

                                // Format waktu (misal: "2 jam yang lalu", "1 hari yang lalu")
                                $time_ago = '';
                                $now = new DateTime();
                                $activity_time = new DateTime($row_aktivitas['timestamp']);
                                $interval = $now->diff($activity_time);

                                if ($interval->y > 0) {
                                    $time_ago = $interval->y . ' tahun yang lalu';
                                } elseif ($interval->m > 0) {
                                    $time_ago = $interval->m . ' bulan yang lalu';
                                } elseif ($interval->d > 0) {
                                    $time_ago = $interval->d . ' hari yang lalu';
                                } elseif ($interval->h > 0) {
                                    $time_ago = $interval->h . ' jam yang lalu';
                                } elseif ($interval->i > 0) {
                                    $time_ago = $interval->i . ' menit yang lalu';
                                } else {
                                    $time_ago = 'Baru saja';
                                }

                                echo '<div class="card mb-3 activity-card">';
                                echo '    <div class="card-body d-flex align-items-center">';
                                echo '        <i class="' . $icon_class . ' me-3 activity-icon"></i>';
                                echo '        <div>';
                                echo '            <h6 class="mb-0">' . htmlspecialchars($row_aktivitas['deskripsi_aktivitas']) . '</h6>';
                                echo '            <small class="text-muted">' . $time_ago . '</small>';
                                echo '        </div>';
                                echo '    </div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p class="text-center">Tidak ada aktivitas terbaru.</p>';
                        }
                        // Tutup koneksi database setelah semua query selesai
                        $conn->close();
                        ?>
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