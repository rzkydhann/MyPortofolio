<?php
session_start();
require '../function.php'; // Asumsi file ini berisi koneksi database $conn

// Cek apakah user sudah login
if (!isset($_SESSION['loginpelanggan'])) {
    header("Location: loginpelanggan.php");
    exit;
}

$old_username = $_SESSION['userpelanggan'] ?? '';

// Ambil data pelanggan dari database
if (!($stmt = $conn->prepare("SELECT * FROM pelanggan WHERE username = ?"))) {
    echo "<script>alert('Terjadi kesalahan pada persiapan query database untuk mengambil data!'); window.location='dashboard.php';</script>";
    exit;
}
$stmt->bind_param("s", $old_username);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Jika data pelanggan tidak ditemukan
if (!$data) {
    echo "<script>alert('Data pelanggan tidak ditemukan! Pastikan Anda login dengan benar.'); window.location='dashboard.php';</script>";
    exit;
}

// Jika form disubmit
if (isset($_POST['simpan'])) {
    $new_username = htmlspecialchars(trim($_POST['username'])); // Username baru
    $nama         = htmlspecialchars(trim($_POST['nama']));
    $email        = htmlspecialchars(trim($_POST['email']));
    $hp           = htmlspecialchars(trim($_POST['hp']));
    $alamat       = htmlspecialchars(trim($_POST['alamat']));

    // Validasi: Cek apakah username baru sudah digunakan oleh pengguna lain
    if ($new_username !== $old_username) { // Hanya cek jika username berubah
        $check_stmt = $conn->prepare("SELECT id FROM pelanggan WHERE username = ?");
        $check_stmt->bind_param("s", $new_username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        if ($check_result->num_rows > 0) {
            echo "<script>alert('Username " . $new_username . " sudah digunakan. Silakan pilih username lain.');</script>";
            // Langsung keluar dari proses update atau set error flag
            $error_update = true;
        }
        $check_stmt->close();
    }

    if (!isset($error_update)) { // Lanjutkan update jika tidak ada error validasi username
        if (!($stmt = $conn->prepare("UPDATE pelanggan SET username=?, nama=?, email=?, hp=?, alamat=? WHERE username=?"))) {
            echo "<script>alert('Terjadi kesalahan pada persiapan query update database!');</script>";
        } else {
            $stmt->bind_param("ssssss", $new_username, $nama, $email, $hp, $alamat, $old_username);
            if ($stmt->execute()) {
                // Perbarui session dengan username yang baru
                $_SESSION['userpelanggan'] = $new_username;
                echo "<script>alert('Profil berhasil diperbarui!'); window.location='dashboard.php';</script>";
                exit;
            } else {
                echo "<script>alert('Gagal memperbarui profil: " . $stmt->error . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white shadow-md rounded p-6 mt-20">
        <h2 class="text-2xl font-bold mb-6 text-center">Edit Profil</h2>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($data['username']); ?>" class="w-full px-4 py-2 border rounded bg-gray-100 cursor-not-allowed" readonly>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" class="w-full px-4 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" class="w-full px-4 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">No HP</label>
                <input type="text" name="hp" value="<?= htmlspecialchars($data['hp']); ?>" class="w-full px-4 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Alamat</label>
                <textarea name="alamat" class="w-full px-4 py-2 border rounded" required><?= htmlspecialchars($data['alamat']); ?></textarea>
            </div>
            <div class="flex justify-between">
                <a href="dashboard.php" class="text-gray-600 hover:text-blue-500">← Kembali</a>
                <button type="submit" name="simpan" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>