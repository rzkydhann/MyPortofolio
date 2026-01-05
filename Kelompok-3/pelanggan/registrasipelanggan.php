<?php
require '../function.php';

$pesan = "";

if (isset($_POST['daftar'])) {
  if (registrasiP($_POST) > 0) {
    echo "
      <script>
        alert('User baru berhasil ditambahkan!');
        window.location.href = 'loginpelanggan.php';
      </script>
    ";
    exit;
  } else {
    // Simpan error ke variabel, bukan langsung echo
    $pesan = "Registrasi gagal: " . mysqli_error($conn);
  }
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Registrasi Pelanggan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-white fixed w-full z-10 top-0 shadow">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between">
      <div class="flex space-x-4">
        <a href="../index.html" class="text-black font-bold text-xl py-4 px-2">Rockshoes.id</a>
      </div>
      <div class="hidden md:flex items-center space-x-1">
        <a href="loginpelanggan.php" class="py-4 px-2 text-black hover:text-yellow-400">Login</a>
      </div>
    </div>
  </div>
</nav>

<!-- Form Registrasi -->
<section class="min-h-screen pt-24 bg-[url('../img/login.png')] bg-cover bg-center bg-no-repeat">
  <div class="absolute inset-0 bg-black opacity-50"></div>
  <div class="max-w-2xl mx-auto px-3 py-10">
    <h2 class="text-4xl font-bold mb-10 text-white text-center drop-shadow-2xl">REGISTRASI PELANGGAN</h2>
    
    <form action="" method="post" class="bg-white/80 p-6 rounded-lg shadow-lg space-y-4 backdrop-blur-sm">
      <div>
        <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
        <input type="text" name="nama" id="nama" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" id="username" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="hp" class="block text-sm font-medium text-gray-700">No HP</label>
        <input type="text" name="hp" id="hp" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
        <input type="text" name="alamat" id="alamat" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="exampleInputPassword1" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" id="exampleInputPassword1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <button type="submit" name="daftar" class="w-full bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-700">Daftar</button>
      </div>
      <p class="text-sm text-gray-700 text-center">
        Sudah punya akun? <a href="loginpelanggan.php" class="text-blue-600 hover:underline">Login disini</a>
      </p>
    </form>
  </div>
</section>


</body>
</html>
