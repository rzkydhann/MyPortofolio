<?php
// db_connect.php
$servername = "localhost";
$username = "root"; // Ganti jika database Anda di hosting lain
$password = "";     // Ganti dengan password database Anda
$dbname = "perpustakaan_db"; // Nama database

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>