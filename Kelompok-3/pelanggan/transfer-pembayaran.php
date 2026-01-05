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

$stmt = mysqli_prepare($conn, "SELECT * FROM orderperbaikan WHERE id = ? AND username = ?");
mysqli_stmt_bind_param($stmt, "is", $order_id, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$order) {
    $_SESSION['operation_message'] = "Data tidak ditemukan.";
    header("Location: history.php");
    exit;
}

if ($order['pembayaran'] !== 'Transfer') {
    $_SESSION['operation_message'] = "Metode pembayaran bukan Transfer.";
    header("Location: history.php");
    exit;
}

// Informasi rekening tujuan transfer (ganti sesuai data kamu)
$nama_rekening = "Rockshoes Service";
$bank = "Bank Mandiri";
$nomor_rekening = "123-456-7890";

$biaya_rp = number_format($order['biaya'], 0, ',', '.');

$pesan = urlencode("Halo Admin Rockshoes, saya sudah melakukan pembayaran melalui Transfer Bank untuk pesanan berikut:\n\n".
    "🔢 ID Order: {$order['id']}\n".
    "👤 Nama: {$order['nama']}\n".
    "📱 HP: {$order['hp']}\n".
    "🛠️ Layanan: {$order['layananperbaikan']}\n".
    "🔧 Jenis: {$order['jenisperbaikan']}\n".
    "💰 Biaya: Rp {$biaya_rp}\n".
    "📅 Jadwal: {$order['tanggal']} | {$order['waktu']}\n\n".
    "Saya akan kirim bukti transfernya sekarang. Terima kasih!");

$wa_admin = "6281234241634"; // Ganti nomor admin di sini
$link_wa = "https://wa.me/{$wa_admin}?text={$pesan}";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pembayaran via Transfer Bank</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center px-4">

  <div class="bg-white p-6 rounded shadow max-w-md w-full">
    <h2 class="text-xl font-bold mb-4 text-center">Pembayaran Transfer Bank</h2>

    <div class="text-gray-700 mb-2"><strong>ID Order:</strong> <?= htmlspecialchars($order['id']); ?></div>
    <div class="text-gray-700 mb-2"><strong>Nama:</strong> <?= htmlspecialchars($order['nama']); ?></div>
    <div class="text-gray-700 mb-2"><strong>Layanan:</strong> <?= htmlspecialchars($order['layananperbaikan']); ?> - <?= htmlspecialchars($order['jenisperbaikan']); ?></div>
    <div class="text-gray-700 mb-2"><strong>Biaya:</strong> Rp <?= $biaya_rp; ?></div>
    <div class="text-gray-700 mb-4"><strong>Jadwal:</strong> <?= htmlspecialchars($order['tanggal']); ?> - <?= htmlspecialchars($order['waktu']); ?></div>

    <div class="bg-yellow-100 text-yellow-800 p-3 rounded text-sm mb-4">
      Silakan transfer jumlah pembayaran ke rekening berikut:
    </div>

    <div class="text-gray-900 mb-4 text-center">
      <p><strong>Bank:</strong> <?= $bank; ?></p>
      <p><strong>Nama Rekening:</strong> <?= $nama_rekening; ?></p>
      <p><strong>No. Rekening:</strong> <?= $nomor_rekening; ?></p>
    </div>

    <div class="text-sm text-gray-500 text-center mb-6">
      Setelah melakukan transfer, klik tombol di bawah ini untuk mengirim bukti transfer ke WhatsApp Admin.
    </div>

    <div class="text-center">
      <a href="<?= $link_wa; ?>" target="_blank" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
        Kirim Bukti TF ke WhatsApp
      </a>
    </div>

    <div class="text-center mt-4">
      <a href="history.php" class="text-blue-500 text-sm hover:underline">Kembali ke Riwayat</a>
    </div>
  </div>

</body>
</html>
