<?php
include "config.php";

header('Content-Type: application/json');

$url = "https://indodax.com/api/tickers";
$json = file_get_contents($url);
$data = json_decode($json, true);

if (!$data || !isset($data["tickers"])) {
    echo json_encode(["status" => "error", "message" => "❌ Gagal ambil data dari API"]);
    exit;
}

$tickers = $data["tickers"];
$count = 0;

foreach ($tickers as $pair => $val) {
    $symbol = $pair;
    $last_price = $val["last"];
    $high = $val["high"];
    $low = $val["low"];

    // Hitung Change 24h (%)
    $change_24h = 0;
    if ($low > 0) {
        $change_24h = (($last_price - $low) / $low) * 100;
    }

    // Menggunakan ON DUPLICATE KEY UPDATE agar data koin yang sama diperbarui, bukan ditambah baris baru
    // Pastikan kolom 'symbol' di database sudah diset sebagai UNIQUE atau PRIMARY KEY
    $stmt = $conn->prepare("INSERT INTO tickers (symbol, last_price, high, low, change_24h, created_at) 
                            VALUES (?, ?, ?, ?, ?, NOW()) 
                            ON DUPLICATE KEY UPDATE 
                            last_price = VALUES(last_price), 
                            high = VALUES(high), 
                            low = VALUES(low), 
                            change_24h = VALUES(change_24h), 
                            created_at = NOW()");
    
    $stmt->bind_param("sdddd", $symbol, $last_price, $high, $low, $change_24h);

    if ($stmt->execute()) {
        $count++;
    }
}

echo json_encode(["status" => "success", "message" => "✅ Berhasil sinkronisasi $count data koin Indodax"]);
?>