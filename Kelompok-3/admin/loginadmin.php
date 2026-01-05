<?php
session_start();

if (isset($_SESSION['loginadmin'])) {
  header("Location: loginadmin.php");
  exit;
}

require '../function.php';

if (isset($_POST["loginadmin"])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Gunakan cara yang sama seperti login pelanggan
  $result = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");

  if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    // Cek apakah password di database menggunakan hash atau plain text
    if (password_verify($password, $row["password"]) || $password === $row["password"]) {
      $_SESSION['loginadmin'] = true;
      $_SESSION['useradmin'] = $row['nama']; // atau sesuai kolom nama di tabel admin
      $_SESSION['admin_id'] = $row['id']; // simpan ID admin untuk keperluan lain
      
      // Tambahkan alert sukses dan redirect dengan JavaScript
      echo "<script>
              alert('Login berhasil! Selamat datang di Dashboard Admin.');
              window.location.href = 'dashboard_admin.php';
            </script>";
      exit;
    }
  }

  $error = true;
  $error_msg = "Username atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Halaman Login Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Navbar -->
<nav class="bg-white fixed w-full z-10 top-0 shadow">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between">
      <div class="flex space-x-4">
        <a href="../index.html" class="text-black font-bold text-xl py-4 px-2">Rockshoes.id - Admin</a>
      </div>
      <div class="hidden md:flex items-center space-x-1">
        <a href="../index.html" class="py-4 px-2 text-black hover:text-yellow-400">Kembali ke Website</a>
      </div>
    </div>
  </div>
</nav>

  <!-- Login Form -->
<section class="relative min-h-screen pt-24 bg-[url('../img/admin-bg.png')] bg-cover bg-center bg-no-repeat">
  <!-- Overlay hitam -->
  <div class="absolute inset-0 bg-black opacity-60 z-0"></div>

  <!-- Konten -->
  <div class="relative z-10 max-w-3xl mx-auto px-3 py-10">
    <div class="text-center mb-6">
      <h2 class="text-4xl font-bold text-white drop-shadow-lg">ADMIN LOGIN</h2>
      <p class="text-white text-lg mt-2 drop-shadow">Panel Administrasi Rockshoes.id</p>
    </div>

    <div class="max-w-md mx-auto bg-white/85 backdrop-blur-md shadow-2xl rounded-lg p-6">
      <div class="text-center mb-4">
        <div class="w-16 h-16 bg-yellow-500 rounded-full mx-auto flex items-center justify-center mb-3">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Administrator Access</h3>
      </div>

      <form action="" method="post" class="space-y-4">
        <?php if (isset($error)) : ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <strong>Error!</strong> 
            <?php echo isset($error_msg) ? $error_msg : "Username atau Password salah!"; ?>
          </div>
        <?php endif; ?>

        <div>
          <label for="username" class="block mb-2 font-medium text-gray-700">
            <span class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              Username Admin
            </span>
          </label>
          <input type="text" name="username" id="username"
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent" 
                 placeholder="Masukkan username admin" required>
        </div>

        <div>
          <label for="password" class="block mb-2 font-medium text-gray-700">
            <span class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
              Password
            </span>
          </label>
          <input type="password" name="password" id="password"
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent" 
                 placeholder="Masukkan password" required>
        </div>

        <button type="submit" name="loginadmin"
                class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-white py-3 rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition duration-300 font-semibold shadow-lg transform hover:scale-105">
          <span class="flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1"></path>
            </svg>
            Login sebagai Admin
          </span>
        </button>

        <div class="text-center pt-4 border-t">
          <p class="text-sm text-gray-600 mb-2">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            Akses khusus untuk Administrator
          </p>
        </div>
      </form>
    </div>
  </div>
</section>

</body>
</html>