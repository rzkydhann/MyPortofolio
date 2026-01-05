<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['loginpelanggan'])) {
    header("Location: loginpelanggan.php");
    exit;
}

// Ambil username dari session
$user = $_SESSION['userpelanggan'];

// Panggil file fungsi yang berisi koneksi database ($conn)
require '../function.php'; // Sesuaikan path jika file function.php berada di direktori yang berbeda

// Cek apakah ada pesanan yang dipilih untuk dibatalkan (dari form multiple)
if (isset($_POST['selected_orders']) && is_array($_POST['selected_orders'])) {
    $selected_ids = $_POST['selected_orders'];

    // Filter ID untuk memastikan hanya angka dan mencegah SQL Injection
    $filtered_ids = array_map('intval', $selected_ids);

    // Pastikan ada ID yang valid setelah filtering
    if (empty($filtered_ids)) {
        $_SESSION['operation_message'] = "Tidak ada pesanan yang valid untuk dibatalkan.";
        header("Location: history.php");
        exit;
    }

    // Buat string ID untuk query IN clause
    $ids_string = implode(',', $filtered_ids);

    // Query untuk mengubah status pesanan menjadi 'dibatalkan' berdasarkan ID dan nama pengguna
    // PENTING: Pastikan hanya pesanan milik pengguna yang login yang bisa dibatalkan
    // dan statusnya masih 'menunggu teknisi' atau 'diproses'
    $query = "UPDATE orderperbaikan SET status = 'dibatalkan' WHERE id IN ($ids_string) AND nama = ? AND (status = 'menunggu teknisi' OR status = 'diproses')";

    // Gunakan prepared statement untuk keamanan
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $user); // Bind nama pengguna

        if (mysqli_stmt_execute($stmt)) {
            $rows_affected = mysqli_stmt_affected_rows($stmt);
            if ($rows_affected > 0) {
                $_SESSION['operation_message'] = "Berhasil membatalkan " . $rows_affected . " pesanan.";
            } else {
                $_SESSION['operation_message'] = "Tidak ada pesanan yang dibatalkan. Mungkin pesanan tidak ditemukan, bukan milik Anda, atau statusnya sudah tidak bisa dibatalkan.";
            }
        } else {
            $_SESSION['operation_message'] = "Terjadi kesalahan saat membatalkan pesanan: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['operation_message'] = "Terjadi kesalahan pada prepared statement: " . mysqli_error($conn);
    }

    // Redirect kembali ke halaman history
    header("Location: history.php");
    exit;

} elseif (isset($_POST['id'])) { // Handle pembatalan satu item
    $id = intval($_POST['id']); // Sanitize input

    // Query untuk membatalkan satu pesanan
    $query = "UPDATE orderperbaikan SET status = 'dibatalkan' WHERE id = ? AND nama = ? AND (status = 'menunggu teknisi' OR status = 'diproses')";

    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "is", $id, $user);

        if (mysqli_stmt_execute($stmt)) {
            $rows_affected = mysqli_stmt_affected_rows($stmt);
            if ($rows_affected > 0) {
                $_SESSION['operation_message'] = "Berhasil membatalkan pesanan.";
            } else {
                $_SESSION['operation_message'] = "Tidak ada pesanan yang dibatalkan. Mungkin pesanan tidak ditemukan, bukan milik Anda, atau statusnya sudah tidak bisa dibatalkan.";
            }
        } else {
            $_SESSION['operation_message'] = "Terjadi kesalahan saat membatalkan pesanan: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['operation_message'] = "Terjadi kesalahan pada prepared statement: " . mysqli_error($conn);
    }

    header("Location: history.php");
    exit;

} else {
    // Jika tidak ada pesanan yang dipilih
    $_SESSION['operation_message'] = "Tidak ada pesanan yang dipilih untuk dibatalkan.";
    header("Location: history.php");
    exit;
}
?>