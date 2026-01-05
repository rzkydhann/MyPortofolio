<?php 

session_start();

if (!isset($_SESSION['loginpelanggan'])) {
  header("Location: loginpelanggan.php");
  exit;
}

require '../function.php';  // <-- Pastikan path ini sesuai dengan struktur folder Anda

$user = $_SESSION['userpelanggan'];
$dataUser = mysqli_query($conn, "SELECT * FROM pelanggan WHERE username = '$user'"); 
$userInfo = mysqli_fetch_assoc($dataUser);

if (!$userInfo) {
  die("Data user tidak ditemukan.");
}

// MENGAMBIL LAYANAN
$layananPerbaikan = $_GET['layanan'];

// CEK APAKAH TOMBOL KIRIM SUDAH DI TEKAN ATAU BELUM
if ( isset($_POST['kirim'])) {

  // cek apakah data berhasil di tambahkan atau tidak
  if( order($_POST) > 0  ) {
    echo "
      <script>
      alert('Permintan berhasil dikirim');
      document.location.href = 'history.php';

      </script>

    ";
  } else {
    echo "

    <script>
      alert('Permintan gagal dikirim');
      document.location.href = 'history.php';

    </script>

    ";
  }
}

$layananPerbaikan = $_GET['layanan'] ?? '';

$hargaLayanan = [
  "Deep Cleaning" => 50000,
  "Hard Cleaning" => 60000,
  "Reglue" => 45000,
  "Repaint" => 65000
];

$biaya = isset($hargaLayanan[$layananPerbaikan]) ? $hargaLayanan[$layananPerbaikan] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Halaman Order</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">

  <!-- Navbar -->
  <nav class="bg-white fixed w-full z-10 top-0 shadow">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex justify-between items-center h-16">
      
      <!-- Logo -->
        <div>
          <a href="../index.html" class="text-black font-bold text-xl">Rockshoes.id</a>
        </div>

        <!-- Navigation Menu -->
        <div class="flex items-center space-x-6">
          <a href='dashboard.php' class="hover:text-yellow-400">Beranda</a>
          <a href="history.php" class="hover:text-yellow-400">Riwayat Pemesanan</a>
        </div>

        <!-- User Info and Logout -->
        <div class="flex items-center space-x-4">
          <span class="font-medium"><?= htmlspecialchars($user); ?></span>
          <a href="logout.php" class="hover:text-red-400">Logout</a>
        </div>

      </div>
    </div>
  </nav>

  <!-- Order Form -->
  <section class="pt-28 pb-12">
    <div class="max-w-3xl mx-auto bg-white p-8 shadow rounded-lg">
      <h2 class="text-2xl font-semibold mb-4">Form Permintaan Perbaikan</h2>
      <hr class="mb-6">

      <form action="" method="post" class="space-y-4">
        <div>
          <label for="nama" class="block text-sm font-medium">Username</label>
          <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($userInfo['username']); ?>" 
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-yellow-100" />
        </div>

        <div>
          <label for="nama" class="block text-sm font-medium">Nama</label>
          <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($userInfo['nama']); ?>"
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-yellow-100" />
        </div>

        <div>
          <label for="hp" class="block text-sm font-medium">No. HP</label>
          <input type="text" name="hp" id="hp" value="<?= htmlspecialchars($userInfo['hp']); ?>" required
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-white-100" />
        </div>

        <div>
          <label for="layananPerbaikan" class="block text-sm font-medium">Layanan Perbaikan</label>
          <input type="text" name="layananPerbaikan" value="<?= $layananPerbaikan; ?>" readonly
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-white-100" />
        </div>

        <div>
          <label for="merk" class="block text-sm font-medium">Merk</label>
          <input type="text" name="merk" id="merk" required
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" />
        </div>

        <div>
          <label for="jenisperbaikan" class="block text-sm font-medium">Jenis Perbaikan</label>
          <input type="text" name="jenisPerbaikan" id="jenisperbaikan" required
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" />
        </div>

        <div>
          <label for="tanggal" class="block text-sm font-medium">Tanggal penjemputan</label>
          <input type="date" name="tanggal" id="tanggal"
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" />
        </div>

        <div>
          <label for="waktu" class="block text-sm font-medium">Jam Penjemputan (08:00 - 12:00)</label>
          <input type="time" name="waktu" id="waktu" required min="08:00" max="12:00"
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" />
        </div>

        <div>
          <label for="alamat" class="block text-sm font-medium">Lokasi / Alamat</label>
          <textarea name="alamat" id="alamat" rows="3"
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md"><?= htmlspecialchars($userInfo['alamat']); ?></textarea>
        </div>

        <div>
          <label for="biaya" class="block text-sm font-medium">Perkiraan Biaya (Rp)</label>
          <input type="text" name="biaya" id="biaya" value="<?= $biaya; ?>" readonly
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" />
        </div>

        <div>
          <label for="pembayaran" class="block text-sm font-medium">Metode Pembayaran</label>
          <select name="pembayaran" id="pembayaran" required
            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md">
            <option value="" disabled selected>Pilih Metode Pembayaran</option>
            <option value="COD">Bayar di Tempat (COD)</option>
            <option value="Transfer">Transfer Bank</option>
            <option value="E-Wallet">E-Wallet (OVO, DANA, GoPay)</option>
          </select>
        </div>

        <input type="hidden" name="status" value="Menunggu Teknisi" />
        <input type="hidden" name="teknisi" value="" />

        <button type="submit" name="kirim"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
          Kirim
        </button>
      </form>
    </div>
  </section>
</body>
</html>
