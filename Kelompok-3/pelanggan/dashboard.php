<?php

// CEK SESSION APAKAH SUDAH LOGIN ATAU BELUM
session_start();
if (!isset($_SESSION['loginpelanggan'])) {
	header("Location: loginpelanggan.php");
	exit;

}

// MENGAMBIL NAMA DARI USER
$user = $_SESSION['userpelanggan'];


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pelanggan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

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
        <a href='../index.html' class="hover:text-yellow-400">Beranda</a>
        <a href="history.php" class="hover:text-yellow-400">Riwayat Pemesanan</a>
      </div>

      <!-- User Info and Logout -->
      <div class="flex items-center space-x-4">
        <span class="font-medium"><?= htmlspecialchars($user); ?></span>
        <a href="editprofil.php" class="hover:text-red-400">Edit Profil</a>
        <a href="logout.php" class="hover:text-red-400">Logout</a>
      </div>

    </div>
  </div>
</nav>

<!-- Menu Pelanggan -->
<section class="pt-28 pb-16 bg-gray-50">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl font-bold text-center mb-4 text-gray-800">Pilih Layanan Cuci Sepatu</h2>
    <p class="text-center text-gray-600 mb-10">Kami menyediakan berbagai layanan pembersihan dan perbaikan sepatu terbaik untuk Anda.</p>
    <hr class="mb-8 border-gray-300">

    <!-- Layanan Cuci Sepatu -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <?php
      $services = [
        [
          "name" => "Deep Cleaning",
          "image" => "deepclean.jpg",
          "desc" => "Pembersihan menyeluruh hingga ke bagian dalam sepatu.",
          "price" => "Rp 50.000 (Free Ongkir)"
        ],
        [
          "name" => "Hard Cleaning",
          "image" => "hardclean.webp",
          "desc" => "Cocok untuk sepatu dengan noda membandel atau bahan keras.",
          "price" => "Rp 60.000 (Free Ongkir)"
        ],
        [
          "name" => "Reglue",
          "image" => "reglue.webp",
          "desc" => "Perbaikan lem sol sepatu yang terlepas atau rusak.",
          "price" => "Rp 45.000 (Free Ongkir)"
        ],
        [
          "name" => "Repaint",
          "image" => "repaint.webp",
          "desc" => "Pengecatan ulang sepatu agar terlihat seperti baru.",
          "price" => "Rp 65.000 (Free Ongkir)"
        ]
      ];

      foreach ($services as $service) {
        echo "
        <div class='bg-white rounded-xl shadow-lg p-5 flex flex-col items-center text-center hover:shadow-2xl transition duration-300'>
          <a href='order.php?layanan=" . urlencode($service["name"]) . "'>
            <img src='../img/{$service["image"]}' alt='{$service["name"]}' class='h-32 object-contain mb-4 rounded-md'>
          </a>
          <h4 class='text-lg font-semibold text-gray-800 mb-2'>{$service["name"]}</h4>
          <p class='text-sm text-gray-600 mb-2'>{$service["desc"]}</p>
          <span class='text-yellow-600 font-bold'>{$service["price"]}</span>
        </div>";
      }
      ?>
    </div>
  </div>
</section>

</body>
</html>
