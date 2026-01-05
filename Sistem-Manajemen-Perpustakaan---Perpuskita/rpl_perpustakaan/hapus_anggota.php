<?php
session_start();
if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php'; // Sertakan file koneksi database

// Cek apakah ada ID anggota yang dikirim melalui GET request
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_anggota = $conn->real_escape_string($_GET['id']); // Perbarui nama variabel

    // Ambil nama anggota untuk log aktivitas sebelum dihapus
    $nama_anggota_to_delete = '';
    $sql_get_nama = "SELECT nama FROM anggota WHERE id_anggota = ?"; // Perbarui nama kolom
    $stmt_get_nama = $conn->prepare($sql_get_nama);
    $stmt_get_nama->bind_param("i", $id_anggota);
    $stmt_get_nama->execute();
    $result_nama = $stmt_get_nama->get_result();
    if ($result_nama->num_rows > 0) {
        $row_nama = $result_nama->fetch_assoc();
        $nama_anggota_to_delete = $row_nama['nama'];
    }
    $stmt_get_nama->close();

    // Query untuk menghapus anggota
    $sql_delete_anggota = "DELETE FROM anggota WHERE id_anggota = ?"; // Perbarui nama kolom
    $stmt_delete_anggota = $conn->prepare($sql_delete_anggota);
    $stmt_delete_anggota->bind_param("i", $id_anggota);

    if ($stmt_delete_anggota->execute()) {
        // --- LOG AKTIVITAS ---
        $sql_log = "INSERT INTO log_aktivitas (user_id, tipe_aktivitas, deskripsi_aktivitas, referensi_id) VALUES (?, ?, ?, ?)"; // Perbarui kolom user_id
        $stmt_log = $conn->prepare($sql_log);
        $tipe_aktivitas = "Hapus Anggota";
        $deskripsi_aktivitas = "Anggota \"" . $nama_anggota_to_delete . "\" (ID: #" . sprintf('%04d', $id_anggota) . ") telah dihapus.";
        $stmt_log->bind_param("issi", $_SESSION['id_petugas'], $tipe_aktivitas, $deskripsi_aktivitas, $id_anggota); // Sesuaikan bind_param
        $stmt_log->execute();
        $stmt_log->close();
        // --- AKHIR LOG AKTIVITAS ---

        // Redirect kembali ke data_anggota.php dengan pesan sukses
        header("Location: data_anggota.php?status=success_delete");
    } else {
        // Redirect kembali ke data_anggota.php dengan pesan error
        header("Location: data_anggota.php?status=error_delete");
    }
    $stmt_delete_anggota->close();
} else {
    // Jika tidak ada ID yang diberikan, redirect kembali ke data_anggota.php dengan pesan warning
    header("Location: data_anggota.php?status=no_id");
}

$conn->close(); // Tutup koneksi
exit(); // Pastikan script berhenti setelah redirect
?>