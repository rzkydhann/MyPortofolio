<?php
include "config.php";
$symbol = isset($_GET['symbol']) ? $conn->real_escape_string($_GET['symbol']) : '';

// Mengambil SEMUA riwayat harga khusus koin ini
$result = $conn->query("SELECT * FROM tickers WHERE symbol = '$symbol' ORDER BY created_at DESC");
$history = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Histori <?= strtoupper($symbol) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 shadow-sm rounded">
        <div class="d-flex justify-content-between mb-4">
            <h3>Riwayat Harga: <?= strtoupper(str_replace('_', '/', $symbol)) ?></h3>
            <a href="ticker_crypto.php" class="btn btn-secondary">Kembali</a>
        </div>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Waktu</th>
                    <th>Harga (IDR)</th>
                    <th>High</th>
                    <th>Low</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $h): ?>
                <tr>
                    <td><?= $h['created_at'] ?></td>
                    <td>Rp <?= number_format($h['last_price'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($h['high'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($h['low'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>