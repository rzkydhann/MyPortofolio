<?php
session_start();

if (isset($_SESSION['loginpelanggan'])) {
  header("Location: dashboard.php");
  exit;
}

require '../function.php'; // Asumsi file ini berisi koneksi database $conn

if (isset($_POST["loginpelanggan"])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // **Perbaikan: Gunakan prepared statements untuk mencegah SQL Injection**
  // Periksa apakah prepare berhasil
  if (!($stmt = $conn->prepare("SELECT * FROM pelanggan WHERE username = ?"))) {
      $error = "Terjadi kesalahan pada persiapan query database.";
  } else {
      $stmt->bind_param("s", $username); // 's' menandakan tipe data string
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
          $_SESSION['loginpelanggan'] = true;
          $_SESSION['userpelanggan'] = $row['username']; // **Perbaikan: Simpan username, bukan nama**
          header("Location: dashboard.php");
          exit;
        } else {
            $error = "Username atau Password Salah!";
        }
      } else {
        $error = "Username atau Password Salah!";
      }
      $stmt->close(); // Tutup statement setelah selesai
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Halaman Login Pelanggan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <nav class="bg-white fixed w-full z-10 top-0 shadow">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between">
      <div class="flex space-x-4">
        <a href="../index.html" class="text-black font-bold text-xl py-4 px-2">Rockshoes.id</a>
      </div>
      <div class="hidden md:flex items-center space-x-1">
        <a href="registrasipelanggan.php" class="py-4 px-2 text-black hover:text-yellow-400">Registrasi</a>
      </div>
    </div>
  </div>
</nav>

  <section class="relative min-h-screen pt-24 bg-[url('../img/login.png')] bg-cover bg-center bg-no-repeat">
  <div class="absolute inset-0 bg-black opacity-50 z-0"></div>

  <div class="relative z-10 max-w-3xl mx-auto px-3 py-10">
    <div class="text-center mb-6">
      <h2 class="text-4xl font-bold text-white drop-shadow-lg">HALAMAN LOGIN</h2>
    </div>

    <div class="max-w-md mx-auto bg-white/80 backdrop-blur-md shadow-2xl rounded-lg p-6">
      <form action="" method="post" class="space-y-4">
        <?php if (isset($error)) : ?>
          <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded">
            <?= $error ?>
          </div>
        <?php endif; ?>

        <div>
          <label for="username" class="block mb-1 font-medium">Username</label>
          <input type="text" name="username" id="username"
                 class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
        </div>

        <div>
          <label for="password" class="block mb-1 font-medium">Password</label>
          <input type="password" name="password" id="password"
                 class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
        </div>

        <button type="submit" name="loginpelanggan"
                class="w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600 transition">
          Login
        </button>

        <p class="text-center">Belum punya akun? 
          <a href="registrasipelanggan.php" class="text-blue-600 hover:underline">Daftar disini</a>
        </p>
      </form>
    </div>
  </div>
</section>


</body>
</html>