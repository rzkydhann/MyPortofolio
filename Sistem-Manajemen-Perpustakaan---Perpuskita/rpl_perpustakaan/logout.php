<?php
session_start(); // Mulai session

// --- LOG AKTIVITAS LOGOUT ---
if (isset($_SESSION['id_petugas'])) {
    include 'includes/db_connect.php';

    $id_petugas_for_log = $_SESSION['id_petugas'];
    $nama_petugas_for_log = $_SESSION['nama_petugas']; // Untuk deskripsi

    // Tambahkan Cek Keberadaan Petugas
    $check_petugas_sql = "SELECT id_petugas FROM petugas WHERE id_petugas = ?";
    $stmt_check = $conn->prepare($check_petugas_sql);
    $stmt_check->bind_param("i", $id_petugas_for_log);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Petugas masih ada di database, lanjutkan logging
        $sql_log = "INSERT INTO log_aktivitas (user_id, tipe_aktivitas, deskripsi_aktivitas) VALUES (?, ?, ?)";
        $stmt_log = $conn->prepare($sql_log);
        $tipe_aktivitas = "Logout";
        $deskripsi_aktivitas = "Petugas " . $nama_petugas_for_log . " (ID: " . $id_petugas_for_log . ") berhasil logout.";
        $stmt_log->bind_param("iss", $id_petugas_for_log, $tipe_aktivitas, $deskripsi_aktivitas);
        
        // Periksa apakah eksekusi query log berhasil
        if (!$stmt_log->execute()) {
            // Opsional: Log error ini ke file system, bukan ditampilkan ke user
            error_log("Error logging out: " . $stmt_log->error);
        }
        $stmt_log->close();
    } else {
        // Petugas tidak ditemukan di database, tidak perlu logging ke log_aktivitas dengan user_id ini
        // Opsional: Anda bisa membuat log tanpa user_id jika Anda tetap ingin mencatat logout
        // $sql_log_no_user = "INSERT INTO log_aktivitas (tipe_aktivitas, deskripsi_aktivitas) VALUES (?, ?)";
        // $stmt_log_no_user = $conn->prepare($sql_log_no_user);
        // $tipe_aktivitas = "Logout";
        // $deskripsi_aktivitas = "Petugas dengan ID sesi " . $id_petugas_for_log . " (tidak ditemukan) berhasil logout.";
        // $stmt_log_no_user->bind_param("ss", $tipe_aktivitas, $deskripsi_aktivitas);
        // $stmt_log_no_user->execute();
        // $stmt_log_no_user->close();
    }
    $stmt_check->close();
    $conn->close();
}
// --- AKHIR LOG AKTIVITAS LOGOUT ---

// Hapus semua variabel session
$_SESSION = array();

// Hapus cookie session (jika ada)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
?>