<?php
session_start();
if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';

$delete_message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success_delete') {
        $delete_message = '<div class="alert alert-success" role="alert">Data buku berhasil dihapus!</div>';
    } elseif ($_GET['status'] == 'error_delete') {
        $delete_message = '<div class="alert alert-danger" role="alert">Terjadi kesalahan saat menghapus data buku.</div>';
    } elseif ($_GET['status'] == 'no_id') {
        $delete_message = '<div class="alert alert-warning" role="alert">ID buku tidak valid untuk dihapus.</div>';
    }
}

// Logika Pencarian
$search_query = "";
if (isset($_GET['search_query_buku']) && !empty($_GET['search_query_buku'])) {
    $search_query = $conn->real_escape_string($_GET['search_query_buku']);
}

// --- Logika Paginasi ---
$limit = 10; // Jumlah item per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit; // Offset untuk query LIMIT

// Query untuk menghitung total data (dengan filter pencarian jika ada)
$sql_count = "SELECT COUNT(id_buku) AS total FROM buku";
$where_clause_count = [];
if (!empty($search_query)) {
    $where_clause_count[] = "judul_buku LIKE '%$search_query%'";
    $where_clause_count[] = "pengarang LIKE '%$search_query%'";
    $where_clause_count[] = "penerbit LIKE '%$search_query%'";
    $where_clause_count[] = "tahun_terbit LIKE '%$search_query%'";
    $where_clause_count[] = "CAST(id_buku AS CHAR) LIKE '%$search_query%'";
}
if (!empty($where_clause_count)) {
    $sql_count .= " WHERE " . implode(" OR ", $where_clause_count);
}
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_records = $row_count['total'];
$total_pages = ceil($total_records / $limit); // Total halaman

// Modifikasi Query SQL untuk mengambil data halaman saat ini
$sql = "SELECT id_buku, judul_buku, pengarang, penerbit, tahun_terbit, jumlah_halaman FROM buku";
$where_clause_data = [];
if (!empty($search_query)) {
    $where_clause_data[] = "judul_buku LIKE '%$search_query%'";
    $where_clause_data[] = "pengarang LIKE '%$search_query%'";
    $where_clause_data[] = "penerbit LIKE '%$search_query%'";
    $where_clause_data[] = "tahun_terbit LIKE '%$search_query%'";
    $where_clause_data[] = "CAST(id_buku AS CHAR) LIKE '%$search_query%'";
}
if (!empty($where_clause_data)) {
    $sql .= " WHERE " . implode(" OR ", $where_clause_data);
}

$sql .= " ORDER BY id_buku ASC LIMIT $start, $limit"; // Order ASC dan tambahkan LIMIT
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - Perpustakaan</title>
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
                    <h2 class="fs-2 m-0 text-white">Kelola Data Buku</h2>
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
                    <div class="col">
                        <?php echo $delete_message; // Tampilkan pesan di sini ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <form class="input-group w-25" method="GET" action="data_buku.php">
                                <input type="text" class="form-control" placeholder="Cari buku..." aria-label="Cari buku" name="search_query_buku" value="<?php echo isset($_GET['search_query_buku']) ? htmlspecialchars($_GET['search_query_buku']) : ''; ?>">
                                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                                <?php if (isset($_GET['search_query_buku']) && !empty($_GET['search_query_buku'])): ?>
                                    <a href="data_buku.php" class="btn btn-outline-secondary" title="Hapus Pencarian"><i class="fas fa-times"></i></a>
                                <?php endif; ?>
                            </form>
                            <a href="tambah_buku.php" class="btn btn-success"><i class="fas fa-plus me-2"></i>Tambah Buku</a>
                        </div>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">ID Buku</th>
                                    <th scope="col">Judul Buku</th>
                                    <th scope="col">Pengarang</th>
                                    <th scope="col">Penerbit</th>
                                    <th scope="col">Tahun Terbit</th>
                                    <th scope="col">Jumlah Halaman</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    $current_item_number = $start + 1; // Untuk nomor urut di kolom pertama
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row' class='text-center table-no-column'>" . $current_item_number++ . "</th>";
                                        echo "<td>#" . sprintf('%04d', $row["id_buku"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["judul_buku"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["pengarang"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["penerbit"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["tahun_terbit"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["jumlah_halaman"]) . "</td>";
                                        echo "<td>";
                                        echo "<a href='edit_buku.php?id=" . $row["id_buku"] . "' class='btn btn-sm btn-info me-1'><i class='fas fa-edit'></i></a>";
                                        echo "<a href='hapus_buku.php?id=" . $row["id_buku"] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'><i class='fas fa-trash-alt'></i></a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center'>Tidak ada data buku.</td></tr>";
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end">
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search_query) ? '&search_query_buku=' . urlencode($search_query) : ''; ?>" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search_query) ? '&search_query_buku=' . urlencode($search_query) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search_query) ? '&search_query_buku=' . urlencode($search_query) : ''; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
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