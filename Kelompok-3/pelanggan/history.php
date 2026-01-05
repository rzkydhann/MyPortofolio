<?php
session_start();

// Redirect jika belum login
if (!isset($_SESSION['loginpelanggan'])) {
  header("Location: loginpelanggan.php");
  exit;
}

$user = $_SESSION['userpelanggan'];

date_default_timezone_set('Asia/Jakarta');
$waktu = date('d-m-Y H:i:s');

// Memanggil fungsi dari file function.php
require '../function.php'; // Pastikan path ini benar

// Mengambil data perbaikan berdasarkan nama pengguna yang login
// Menggunakan prepared statement untuk keamanan
// Asumsi $conn sudah didefinisikan di function.php
$stmt = mysqli_prepare($conn, "SELECT * FROM orderperbaikan WHERE username = ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "s", $user);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$perbaikan = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Pesan sukses atau error setelah operasi (misal dari batalkan.php atau delete_multiple_history.php)
$message = '';
if (isset($_SESSION['operation_message'])) { // Menggunakan nama session yang lebih umum
    $message = $_SESSION['operation_message'];
    unset($_SESSION['operation_message']); // Hapus pesan setelah ditampilkan
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>History Order</title>
    <style>
      /* Custom styles if needed */
      .checkbox-container {
        display: flex;
        align-items: center;
        gap: 8px; /* Space between checkbox and text */
      }
    </style>
  </head>
  <body class="bg-yellow-100">
  
    <nav class="bg-white fixed w-full z-10 top-0 shadow">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
          <div>
            <a href="../index.html" class="text-black font-bold text-xl">Rockshoes.id</a>
          </div>
          <div class="flex items-center space-x-6">
            <a href='dashboard.php' class="hover:text-yellow-400">Beranda</a>
            <a href="history.php" class="hover:text-yellow-400">Riwayat Pemesanan</a>
          </div>
          <div class="flex items-center space-x-4">
            <span class="font-medium"><?= htmlspecialchars($user); ?></span>
            <a href="logout.php" class="hover:text-red-400">Logout</a>
          </div>
        </div>
      </div>
    </nav>

    <section class="pt-24 px-4 max-w-5xl mx-auto">
      <div class="flex justify-between items-center mb-4">
        <p class="text-gray-700">Terakhir Update : <?= $waktu; ?></p>
        <a href="history.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Refresh</a>
      </div>

      <?php if (!empty($message)) : ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
          <span class="block sm:inline"><?= htmlspecialchars($message); ?></span>
        </div>
      <?php endif; ?>

      <?php if (empty($perbaikan)) : ?>
        <p class="text-center text-gray-600 mt-8">Tidak ada riwayat pesanan.</p>
      <?php else : ?>
          <?php $i = 1; ?>
          <?php foreach($perbaikan as $data) : ?>
          <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-2">
              <div class="checkbox-container">
                <div>
                  <h5 class="text-lg font-semibold"><?= $i; ?>. Layanan Perbaikan <?= htmlspecialchars($data["layananperbaikan"]); ?></h5>
                  <p class="text-sm text-gray-600">Merk: <?= htmlspecialchars($data["merk"]); ?></p>
                </div>
              </div>
              <p class="text-sm text-gray-600"><?= htmlspecialchars($data["tanggal"]); ?> <?= htmlspecialchars($data["waktu"]); ?></p>
            </div>
            <div class="flex justify-between items-center">
              <div>
                <h5 class="font-medium">Jenis Perbaikan: <?= htmlspecialchars($data["jenisperbaikan"]); ?></h5>
              </div>
              <div class="text-right">
                <?php
                  $status = $data['status'];
                  $bgColor = match($status) {
                    'Sepatu akan Segera Dijemput' => 'bg-yellow-500',
                    'Pembayaranmu Terkonfirmasi' => 'bg-green-500',
                    'Dalam Penanganan' => 'bg-blue-500',
                    'Sepatu Diantar Kembali ke Pelanggan' => 'bg-blue-500',
                    'Complete' => 'bg-green-500',
                    'Cancel' => 'bg-red-500',
                    default => 'bg-gray-500',
                  };
                ?>
                <span class="<?= $bgColor; ?> text-white px-3 py-1 rounded text-sm">
                  Status: <?= htmlspecialchars($status); ?>
                </span>
                <p class="text-sm text-gray-600 mt-1">Catatan untukmu: <?= !empty($data["catatan_admin"]) ? htmlspecialchars($data["catatan_admin"]) : '-'; ?></p>
              </div>
            </div>
            <hr class="my-4">
            <table class="text-sm text-gray-700">
              <tr><td class="pr-4">Atas Nama</td><td>: <?= htmlspecialchars($data["nama"]); ?></td></tr>
              <tr><td class="pr-4">No. HP</td><td>: <?= htmlspecialchars($data["hp"]); ?></td></tr>
              <tr><td class="pr-4">Alamat</td><td>: <?= htmlspecialchars($data["alamat"]); ?></td></tr>
              <tr><td class="pr-4">Biaya</td><td>: <?= htmlspecialchars($data["biaya"]); ?></td></tr>
              <tr><td class="pr-4">Biaya</td><td>: <?= htmlspecialchars($data["biaya"]); ?></td></tr>
              <tr><td class="pr-4">Metode Pembayaran</td><td>: <?= htmlspecialchars($data["pembayaran"]); ?></td></tr>
            </table>
            <div class="mt-4 flex justify-end">
              <a href="proses_pembayaran.php?order_id=<?= $data['id']; ?>"
                class="bg-red-600 text-sm text-white px-4 py-2 rounded hover:bg-red-700 inline-block">
                Bayar Sekarang
              </a>
            </div>
          </div>
          <?php $i++; ?>
          <?php endforeach; ?>
      <?php endif; ?>
    </section>

  </body>
</html>