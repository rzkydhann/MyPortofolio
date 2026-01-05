<?php
session_start();
if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';

$message = '';
$form_mode = 'peminjaman_baru'; // 'peminjaman_baru' atau 'riwayat'

// Logika Tambah Peminjaman
if (isset($_POST['action']) && $_POST['action'] == 'tambah_peminjaman') {
    $id_buku = $conn->real_escape_string($_POST['id_buku']);
    $id_anggota = $conn->real_escape_string($_POST['id_anggota']);
    $tgl_pinjam = date('Y-m-d'); // Tanggal pinjam hari ini
    $tgl_kembali = date('Y-m-d', strtotime('+7 days')); // Jatuh tempo 7 hari dari sekarang
    $id_petugas = $_SESSION['id_petugas'];
    $status_peminjaman = 'Dipinjam';

    // Validasi: Pastikan buku dan anggota ada
    $check_buku = $conn->query("SELECT COUNT(*) FROM buku WHERE id_buku = '$id_buku'")->fetch_row()[0];
    $check_anggota = $conn->query("SELECT COUNT(*) FROM anggota WHERE id_anggota = '$id_anggota'")->fetch_row()[0];

    if ($check_buku == 0) {
        $message = '<div class="alert alert-danger" role="alert">ID Buku tidak ditemukan.</div>';
    } elseif ($check_anggota == 0) {
        $message = '<div class="alert alert-danger" role="alert">ID Anggota tidak ditemukan.</div>';
    } else {
        // Validasi: Pastikan buku tidak sedang dipinjam (tgl_dikembalikan IS NULL dan status = 'Dipinjam')
        $check_availability_sql = "SELECT COUNT(*) FROM peminjaman WHERE id_buku = ? AND tgl_dikembalikan IS NULL AND status = 'Dipinjam'";
        $stmt_check_avail = $conn->prepare($check_availability_sql);
        $stmt_check_avail->bind_param("i", $id_buku);
        $stmt_check_avail->execute();
        $is_borrowed = $stmt_check_avail->get_result()->fetch_row()[0];
        $stmt_check_avail->close();

        if ($is_borrowed > 0) {
            $message = '<div class="alert alert-warning" role="alert">Buku ini sedang dipinjam dan belum dikembalikan.</div>';
        } else {
            $sql_insert_peminjaman = "INSERT INTO peminjaman (id_buku, id_anggota, id_petugas, tgl_pinjam, tgl_kembali, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_peminjaman = $conn->prepare($sql_insert_peminjaman);
            $stmt_peminjaman->bind_param("iissss", $id_buku, $id_anggota, $id_petugas, $tgl_pinjam, $tgl_kembali, $status_peminjaman);

            if ($stmt_peminjaman->execute()) {
                $last_id_peminjaman = $conn->insert_id;
                $message = '<div class="alert alert-success" role="alert">Peminjaman berhasil dicatat! ID Peminjaman: #' . sprintf('%04d', $last_id_peminjaman) . '</div>';

                // --- LOG AKTIVITAS ---
                $deskripsi_log = "Peminjaman baru dicatat. Buku ID: " . $id_buku . ", Anggota ID: " . $id_anggota;
                $sql_log = "INSERT INTO log_aktivitas (user_id, tipe_aktivitas, deskripsi_aktivitas, referensi_id) VALUES (?, ?, ?, ?)";
                $stmt_log = $conn->prepare($sql_log);
                $tipe_aktivitas = "Peminjaman";
                $stmt_log->bind_param("issi", $_SESSION['id_petugas'], $tipe_aktivitas, $deskripsi_log, $last_id_peminjaman);
                $stmt_log->execute();
                $stmt_log->close();
                // --- AKHIR LOG AKTIVITAS ---

            } else {
                $message = '<div class="alert alert-danger" role="alert">Error mencatat peminjaman: ' . $stmt_peminjaman->error . '</div>';
            }
            $stmt_peminjaman->close();
        }
    }
}

// Logika Pengembalian Buku
if (isset($_GET['action']) && $_GET['action'] == 'kembalikan' && isset($_GET['id'])) {
    $id_peminjaman_kembali = $conn->real_escape_string($_GET['id']);
    $tgl_dikembalikan = date('Y-m-d');
    $status_dikembalikan = 'Dikembalikan';

    // Ambil info peminjaman sebelum update untuk log dan cek keterlambatan
    $peminjaman_info = $conn->query("SELECT id_buku, id_anggota, tgl_kembali, tgl_dikembalikan FROM peminjaman WHERE id_peminjaman = '$id_peminjaman_kembali'")->fetch_assoc();

    if ($peminjaman_info && is_null($peminjaman_info['tgl_dikembalikan'])) { // Hanya jika belum dikembalikan
        $sql_update_pengembalian = "UPDATE peminjaman SET tgl_dikembalikan = ?, status = ? WHERE id_peminjaman = ?";
        $stmt_pengembalian = $conn->prepare($sql_update_pengembalian);
        $stmt_pengembalian->bind_param("ssi", $tgl_dikembalikan, $status_dikembalikan, $id_peminjaman_kembali);

        if ($stmt_pengembalian->execute()) {
            $message = '<div class="alert alert-success" role="alert">Buku berhasil dikembalikan!</div>';

            // Cek keterlambatan untuk log
            $tgl_kembali_seharusnya = strtotime($peminjaman_info['tgl_kembali']);
            $tgl_dikembalikan_aktual = strtotime($tgl_dikembalikan);
            $is_late = ($tgl_dikembalikan_aktual > $tgl_kembali_seharusnya);
            $late_status = $is_late ? " (Terlambat)" : "";

            // --- LOG AKTIVITAS ---
            $deskripsi_log = "Buku dikembalikan. ID Peminjaman: " . $id_peminjaman_kembali . $late_status;
            $sql_log = "INSERT INTO log_aktivitas (user_id, tipe_aktivitas, deskripsi_aktivitas, referensi_id) VALUES (?, ?, ?, ?)";
            $stmt_log = $conn->prepare($sql_log);
            $tipe_aktivitas = "Pengembalian";
            $stmt_log->bind_param("issi", $_SESSION['id_petugas'], $tipe_aktivitas, $deskripsi_log, $id_peminjaman_kembali);
            $stmt_log->execute();
            $stmt_log->close();
            // --- AKHIR LOG AKTIVITAS ---

        } else {
            $message = '<div class="alert alert-danger" role="alert">Error mengembalikan buku: ' . $stmt_pengembalian->error . '</div>';
        }
        $stmt_pengembalian->close();
    } elseif ($peminjaman_info && !is_null($peminjaman_info['tgl_dikembalikan'])) {
        $message = '<div class="alert alert-warning" role="alert">Buku ini sudah dikembalikan sebelumnya.</div>';
    } else {
        $message = '<div class="alert alert-danger" role="alert">Peminjaman tidak ditemukan.</div>';
    }
    // Redirect untuk menghilangkan parameter GET dari URL dan mencegah pengembalian ganda saat refresh
    header("Location: peminjaman.php?message=" . urlencode(strip_tags($message)));
    exit();
}

// Mengambil pesan dari URL setelah redirect (misalnya dari aksi pengembalian)
if (isset($_GET['message'])) {
    $message = '<div class="alert alert-info" role="alert">' . htmlspecialchars($_GET['message']) . '</div>';
}


// Penentuan mode tampilan (Peminjaman Baru atau Riwayat)
if (isset($_GET['mode'])) {
    $form_mode = $_GET['mode'];
}

// Logika Paginasi untuk Peminjaman Aktif
$limit_aktif = 5; // Jumlah item per halaman untuk peminjaman aktif
$page_aktif = isset($_GET['page_aktif']) && is_numeric($_GET['page_aktif']) ? (int)$_GET['page_aktif'] : 1;
$start_aktif = ($page_aktif - 1) * $limit_aktif;

$sql_count_aktif = "SELECT COUNT(p.id_peminjaman) AS total FROM peminjaman p WHERE p.tgl_dikembalikan IS NULL AND p.status = 'Dipinjam'";
$total_records_aktif = $conn->query($sql_count_aktif)->fetch_assoc()['total'];
$total_pages_aktif = ceil($total_records_aktif / $limit_aktif);

$sql_aktif = "SELECT
                p.id_peminjaman,
                b.id_buku AS buku_id_display,
                b.judul_buku,
                a.nama AS nama_anggota,
                pt.nama_petugas,
                p.tgl_pinjam,
                p.tgl_kembali,
                p.status
              FROM peminjaman p
              JOIN buku b ON p.id_buku = b.id_buku
              JOIN anggota a ON p.id_anggota = a.id_anggota
              JOIN petugas pt ON p.id_petugas = pt.id_petugas
              WHERE p.tgl_dikembalikan IS NULL AND p.status = 'Dipinjam'
              ORDER BY p.tgl_pinjam DESC
              LIMIT $start_aktif, $limit_aktif";
$result_aktif = $conn->query($sql_aktif);

// Logika Paginasi untuk Riwayat Peminjaman
$limit_riwayat = 10; // Jumlah item per halaman untuk riwayat
$page_riwayat = isset($_GET['page_riwayat']) && is_numeric($_GET['page_riwayat']) ? (int)$_GET['page_riwayat'] : 1;
$start_riwayat = ($page_riwayat - 1) * $limit_riwayat;

$sql_count_riwayat = "SELECT COUNT(p.id_peminjaman) AS total FROM peminjaman p WHERE p.tgl_dikembalikan IS NOT NULL OR p.status = 'Dikembalikan' OR p.status = 'Terlambat'";
$total_records_riwayat = $conn->query($sql_count_riwayat)->fetch_assoc()['total'];
$total_pages_riwayat = ceil($total_records_riwayat / $limit_riwayat);

$sql_riwayat = "SELECT
                  p.id_peminjaman,
                  b.id_buku AS buku_id_display,
                  b.judul_buku,
                  a.nama AS nama_anggota,
                  pt.nama_petugas,
                  p.tgl_pinjam,
                  p.tgl_kembali,
                  p.tgl_dikembalikan,
                  p.status
                FROM peminjaman p
                JOIN buku b ON p.id_buku = b.id_buku
                JOIN anggota a ON p.id_anggota = a.id_anggota
                JOIN petugas pt ON p.id_petugas = pt.id_petugas
                WHERE p.tgl_dikembalikan IS NOT NULL OR p.status = 'Dikembalikan' OR p.status = 'Terlambat'
                ORDER BY p.tgl_dikembalikan DESC, p.tgl_pinjam DESC
                LIMIT $start_riwayat, $limit_riwayat";
$result_riwayat = $conn->query($sql_riwayat);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Status Badges untuk peminjaman (diulang dari style.css agar spesifik jika perlu) */
        .status-badge {
            padding: .35em .65em;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
        }
        .status-dipinjam { background-color: #ffc107; color: #333; } /* Kuning */
        .status-dikembalikan { background-color: #28a745; color: white; } /* Hijau */
        .status-terlambat { background-color: #dc3545; color: white; } /* Merah */
        /* Custom icon styling for sidebar (untuk ikon Font Awesome) */
        /* Tidak perlu sidebar-custom-icon jika menggunakan Font Awesome */
    </style>
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
                    <img src="img/dwi_annisa.jpeg" alt="Dwi Annisa" class="rounded-circle mb-2" width="80" height="80"> <h5><?php echo htmlspecialchars($_SESSION['nama_petugas']); ?></h5>
                    <small><?php echo htmlspecialchars($_SESSION['role']); ?></small>
                </div>
                <a href="index.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="data_buku.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-book me-2"></i>Data Buku</a>
                <a href="data_anggota.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-users me-2"></i>Data Anggota</a>
                <a href="peminjaman.php" class="list-group-item list-group-item-action bg-transparent second-text active">
                    <i class="fas fa-receipt me-2"></i>Peminjaman
                </a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0 text-white">Manajemen Peminjaman</h2>
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
                <?php echo $message; // Tampilkan pesan ?>

                <div class="row my-4">
                    <div class="col-12">
                        <ul class="nav nav-tabs mb-3" id="peminjamanTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo ($form_mode == 'peminjaman_baru' ? 'active' : ''); ?>" id="peminjaman-tab" data-bs-toggle="tab" data-bs-target="#peminjaman-baru" type="button" role="tab" aria-controls="peminjaman-baru" aria-selected="<?php echo ($form_mode == 'peminjaman_baru' ? 'true' : 'false'); ?>">Peminjaman Baru</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo ($form_mode == 'riwayat' ? 'active' : ''); ?>" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-peminjaman" type="button" role="tab" aria-controls="riwayat-peminjaman" aria-selected="<?php echo ($form_mode == 'riwayat' ? 'true' : 'false'); ?>">Riwayat Peminjaman</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="peminjamanTabContent">
                            <div class="tab-pane fade <?php echo ($form_mode == 'peminjaman_baru' ? 'show active' : ''); ?>" id="peminjaman-baru" role="tabpanel" aria-labelledby="peminjaman-tab">
                                <div class="card p-4 mb-4">
                                    <h4 class="mb-3">Catat Peminjaman Baru</h4>
                                    <form action="peminjaman.php" method="POST">
                                        <input type="hidden" name="action" value="tambah_peminjaman">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="id_buku" class="form-label">ID Buku</label>
                                                <input type="number" class="form-control" id="id_buku" name="id_buku" placeholder="Masukkan ID Buku (angka)" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="id_anggota" class="form-label">ID Anggota</label>
                                                <input type="number" class="form-control" id="id_anggota" name="id_anggota" placeholder="Masukkan ID Anggota (angka)" required>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-plus-circle me-2"></i>Catat Peminjaman</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="card p-4">
                                    <h4 class="mb-3">Daftar Peminjaman Aktif</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID Peminjaman</th>
                                                    <th>ID Buku</th>
                                                    <th>Judul Buku</th>
                                                    <th>Anggota</th>
                                                    <th>Petugas</th>
                                                    <th>Tgl Pinjam</th>
                                                    <th>Tgl Kembali</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result_aktif->num_rows > 0): ?>
                                                    <?php while($row = $result_aktif->fetch_assoc()):
                                                        $status_class = '';
                                                        $status_display = htmlspecialchars($row['status']);
                                                        if (strtotime($row['tgl_kembali']) < strtotime(date('Y-m-d')) && $row['status'] == 'Dipinjam') {
                                                            $status_class = 'status-terlambat';
                                                            $status_display = 'Terlambat';
                                                        } elseif ($row['status'] == 'Dipinjam') {
                                                            $status_class = 'status-dipinjam';
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td>#<?php echo sprintf('%04d', $row['id_peminjaman']); ?></td>
                                                            <td>#<?php echo sprintf('%04d', $row['buku_id_display']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['judul_buku']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_anggota']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_petugas']); ?></td>
                                                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['tgl_pinjam']))); ?></td>
                                                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['tgl_kembali']))); ?></td>
                                                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_display; ?></span></td>
                                                            <td>
                                                                <a href="peminjaman.php?action=kembalikan&id=<?php echo $row['id_peminjaman']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Yakin ingin mengembalikan buku ini?');"><i class="fas fa-undo-alt me-1"></i>Kembalikan</a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center">Tidak ada peminjaman aktif.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <nav aria-label="Page navigation for active loans">
                                        <ul class="pagination justify-content-end">
                                            <li class="page-item <?php echo ($page_aktif <= 1) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?mode=peminjaman_baru&page_aktif=<?php echo $page_aktif - 1; ?>" tabindex="-1" aria-disabled="true">Previous</a>
                                            </li>
                                            <?php for ($i = 1; $i <= $total_pages_aktif; $i++): ?>
                                                <li class="page-item <?php echo ($page_aktif == $i) ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?mode=peminjaman_baru&page_aktif=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            <li class="page-item <?php echo ($page_aktif >= $total_pages_aktif) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?mode=peminjaman_baru&page_aktif=<?php echo $page_aktif + 1; ?>">Next</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>

                            <div class="tab-pane fade <?php echo ($form_mode == 'riwayat' ? 'show active' : ''); ?>" id="riwayat-peminjaman" role="tabpanel" aria-labelledby="riwayat-tab">
                                <div class="card p-4">
                                    <h4 class="mb-3">Riwayat Peminjaman</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID Peminjaman</th>
                                                    <th>ID Buku</th>
                                                    <th>Judul Buku</th>
                                                    <th>Anggota</th>
                                                    <th>Petugas</th>
                                                    <th>Tgl Pinjam</th>
                                                    <th>Tgl Kembali (Jatuh Tempo)</th>
                                                    <th>Tgl Dikembalikan</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result_riwayat->num_rows > 0): ?>
                                                    <?php while($row = $result_riwayat->fetch_assoc()):
                                                        $status_class = '';
                                                        $status_display = htmlspecialchars($row['status']);
                                                        if ($row['status'] == 'Dikembalikan') {
                                                            $status_class = 'status-dikembalikan';
                                                        } elseif (strtotime($row['tgl_kembali']) < strtotime($row['tgl_dikembalikan'] ?? date('Y-m-d'))) { // Keterlambatan di riwayat
                                                            $status_class = 'status-terlambat';
                                                            $status_display = 'Terlambat';
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td>#<?php echo sprintf('%04d', $row['id_peminjaman']); ?></td>
                                                            <td>#<?php echo sprintf('%04d', $row['buku_id_display']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['judul_buku']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_anggota']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_petugas']); ?></td>
                                                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['tgl_pinjam']))); ?></td>
                                                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['tgl_kembali']))); ?></td>
                                                            <td><?php echo $row['tgl_dikembalikan'] ? htmlspecialchars(date('d/m/Y', strtotime($row['tgl_dikembalikan']))) : '-'; ?></td>
                                                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_display; ?></span></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center">Tidak ada riwayat peminjaman.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <nav aria-label="Page navigation for loan history">
                                        <ul class="pagination justify-content-end">
                                            <li class="page-item <?php echo ($page_riwayat <= 1) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?mode=riwayat&page_riwayat=<?php echo $page_riwayat - 1; ?>" tabindex="-1" aria-disabled="true">Previous</a>
                                            </li>
                                            <?php for ($i = 1; $i <= $total_pages_riwayat; $i++): ?>
                                                <li class="page-item <?php echo ($page_riwayat == $i) ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?mode=riwayat&page_riwayat=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            <li class="page-item <?php echo ($page_riwayat >= $total_pages_riwayat) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?mode=riwayat&page_riwayat=<?php echo $page_riwayat + 1; ?>">Next</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
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

        // Mengaktifkan tab berdasarkan parameter URL
        document.addEventListener('DOMContentLoaded', function() {
            var urlParams = new URLSearchParams(window.location.search);
            var mode = urlParams.get('mode');
            if (mode === 'riwayat') {
                var tab = new bootstrap.Tab(document.getElementById('riwayat-tab'));
                tab.show();
            } else {
                var tab = new bootstrap.Tab(document.getElementById('peminjaman-tab'));
                tab.show();
            }
        });
    </script>
</body>
</html>