<?php
include 'includes/db_connect.php'; // Pastikan path koneksi database benar

// Password baru yang ingin Anda gunakan
$new_password = 'perpuskita123';

// Hash password baru
$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

// Username user yang ingin Anda reset passwordnya
$username_to_reset = 'dwi_annisa';

// Query UPDATE untuk memperbarui password di database
$sql_update_password = "UPDATE petugas SET password = ? WHERE nama_petugas = ?";
$stmt = $conn->prepare($sql_update_password);

// Cek apakah prepared statement berhasil
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameter dan jalankan query
$stmt->bind_param("ss", $hashed_new_password, $username_to_reset);

if ($stmt->execute()) {
    echo "Password untuk user '<b>" . htmlspecialchars($username_to_reset) . "</b>' berhasil direset menjadi '<b>" . htmlspecialchars($new_password) . "</b>'.<br>";
    echo "Sekarang silakan hapus file ini dari server Anda untuk alasan keamanan.";
} else {
    echo "Error saat mereset password: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>