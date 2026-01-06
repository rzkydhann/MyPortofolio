<?php
include "config.php";

// 1. Ambil kata kunci pencarian
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where_clause = $search ? "WHERE t1.symbol LIKE '%$search%'" : "";

// 2. Query untuk mengambil hanya data TERBARU untuk setiap koin unik
$query = "SELECT t1.* FROM tickers t1 
          INNER JOIN (
              SELECT symbol, MAX(created_at) as latest 
              FROM tickers 
              GROUP BY symbol
          ) t2 ON t1.symbol = t2.symbol AND t1.created_at = t2.latest 
          $where_clause 
          ORDER BY t1.symbol ASC";

$result = $conn->query($query);
$tickers = $result->fetch_all(MYSQLI_ASSOC);
$total_koin = count($tickers);
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Indodax Market - Segmentasi Historis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table thead { background-color: #4f46e5; color: white; }
        .coin-name { font-weight: 700; color: #4f46e5; }
        .btn-history { font-size: 0.8rem; padding: 2px 8px; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
        <h2 class="m-0">📊 Market Indodax (Data Terkini)</h2>
        <button id="saveBtn" class="btn btn-primary" onclick="saveTicker()">🔄 Update Semua Harga</button>
    </div>

    <div class="row mb-3">
        <div class="col-md-5">
            <form action="" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Cari koin (contoh: btc)..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-indigo" style="background: #4f46e5; color: white;">Cari</button>
            </form>
        </div>
        <div class="col-md-7 text-end text-muted align-self-center">
            Menampilkan <strong><?= $total_koin ?></strong> koin unik (Data terbaru saja)
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center align-middle">
                <thead>
                    <tr>
                        <th>Pair</th>
                        <th>Harga (IDR)</th>
                        <th>High (24j)</th>
                        <th>Low (24j)</th>
                        <th>Change</th>
                        <th>Waktu Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total_koin > 0): ?>
                    <?php foreach ($tickers as $row): ?>
                        <tr>
                            <td class="coin-name"><?= strtoupper(str_replace('_', '/', $row["symbol"])) ?></td>
                            <td>Rp <?= number_format($row["last_price"], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row["high"], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row["low"], 0, ',', '.') ?></td>
                            <td>
                                <?php 
                                    $chg = number_format($row["change_24h"], 2);
                                    $color = ($chg >= 0) ? "text-success" : "text-danger";
                                    echo "<span class='$color fw-bold'>{$chg}%</span>";
                                ?>
                            </td>
                            <td class="text-muted small"><?= $row["created_at"] ?></td>
                            <td>
                                <a href="history.php?symbol=<?= $row['symbol'] ?>" class="btn btn-outline-info btn-history">
                                    📜 Lihat Historis
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="p-5 text-muted">Koin tidak ditemukan. Silakan klik "Update Semua Harga".</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function saveTicker() {
    let btn = document.getElementById("saveBtn");
    btn.disabled = true; btn.innerText = "Processing...";
    fetch("save_ticker.php").then(res => res.json()).then(data => {
        alert(data.message); location.reload();
    }).finally(() => btn.disabled = false);
}
</script>
</body>
</html>