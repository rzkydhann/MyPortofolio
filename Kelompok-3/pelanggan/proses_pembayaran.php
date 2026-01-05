<?php
session_start();
require '../function.php';

if (!isset($_SESSION['loginpelanggan'])) {
    header("Location: loginpelanggan.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    $_SESSION['operation_message'] = "Permintaan tidak valid.";
    header("Location: history.php");
    exit;
}

$order_id = intval($_GET['order_id']);
$username = $_SESSION['userpelanggan'];

// Prepared statement untuk cek order milik user
$stmt = mysqli_prepare($conn, "SELECT * FROM orderperbaikan WHERE id = ? AND username = ?");
mysqli_stmt_bind_param($stmt, "is", $order_id, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$order) {
    $_SESSION['operation_message'] = "Pesanan tidak ditemukan.";
    header("Location: history.php");
    exit;
}

// Cek metode pembayaran dan redirect sesuai jenisnya
switch ($order['pembayaran']) {
    case 'E-Wallet':
        $_SESSION['order_ewallet'] = $order_id;
        header("Location: e-wallet-pembayaran.php?order_id=$order_id");
        exit;

    case 'Transfer':
        $_SESSION['order_transfer'] = $order_id;
        header("Location: transfer-pembayaran.php?order_id=$order_id");
        exit;

    default:
        $_SESSION['operation_message'] = "Metode pembayaran bukan E-Wallet atau Transfer.";
        header("Location: history.php");
        exit;
}
