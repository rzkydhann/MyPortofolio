<?php
session_start();
if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php'; // Sertakan file koneksi database

// Cek apakah ada ID buku yang dikirim melalui GET request
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_buku = $conn->real_escape_string($_GET['id']); // Perbarui nama variabel

    // Ambil judul buku untuk log aktivitas sebelum dihapus
    $judul_buku_to_delete = '';
    $sql_get_judul = "SELECT judul_buku FROM buku WHERE id_buku = ?"; // Perbarui nama kolom
    $stmt_get_judul = $conn->prepare($sql_get_judul);
    $stmt_get_judul->bind_param("i", $id_buku);
    $stmt_get_judul->execute();
    $result_judul = $stmt_get_judul->get_result();
    if ($result_judul->num_rows > 0) {
        $row_judul = $result_judul->fetch_assoc();
        $judul_buku_to_delete = $row_judul['judul_buku'];
    }
    $stmt_get_judul->close();

    // Query untuk menghapus buku
    $sql_delete_buku = "DELETE FROM buku WHERE id_buku = ?"; // Perbarui nama kolom
    $stmt_delete_buku = $conn->prepare($sql_delete_buku);
    $stmt_delete_buku->bind_param("i", $id_buku);

    if ($stmt_delete_buku->execute()) {
        // --- LOG AKTIVITAS ---
        $sql_log = "INSERT INTO log_aktivitas (user_id, tipe_aktivitas, deskripsi_aktivitas, referensi_id) VALUES (?, ?, ?, ?)"; // Perbarui kolom user_id
        $stmt_log = $conn->prepare($sql_log);
        $tipe_aktivitas = "Hapus Buku";
        $deskripsi_aktivitas = "Buku \"" . $judul_buku_to_delete . "\" (ID: #" . sprintf('%04d', $id_buku) . ") telah dihapus.";
        $stmt_log->bind_param("issi", $_SESSION['id_petugas'], $tipe_aktivitas, $deskripsi_aktivitas, $id_buku); // Sesuaikan bind_param
        $stmt_log->execute();
        $stmt_log->close();
        // --- AKHIR LOG AKTIVITAS ---

        // Redirect kembali ke data_buku.php dengan pesan sukses
        header("Location: data_buku.php?status=success_delete");
    } else {
        // Redirect kembali ke data_buku.php dengan pesan error
        header("Location: data_buku.php?status=error_delete");
    }
    $stmt_delete_buku->close();
} else {
    // Jika tidak ada ID yang diberikan, redirect kembali ke data_buku.php dengan pesan warning
    header("Location: data_buku.php?status=no_id");
}

$conn->close(); // Tutup koneksi
exit(); // Pastikan script berhenti setelah redirect
?>