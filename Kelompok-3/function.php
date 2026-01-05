<?php

// Koneksi ke database
$conn = mysqli_connect("localhost", "rockshoe_rockshoes", "12345678", "rockshoe_rockshoes");

// Fungsi untuk memeriksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi login admin (sudah ada, hanya dipertahankan)
function loginAdmin($data) {
    global $conn;
    $username = mysqli_real_escape_string($conn, $data['username']);
    $password = $data['password'];
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row["password"])) {
            session_start();
            $_SESSION['loginadmin'] = true;
            $_SESSION['useradmin'] = $row['nama'];
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            if (isset($row['level'])) {
                $_SESSION['admin_level'] = $row['level'];
            }
            return true;
        }
    }
    return false;
}

// Fungsi registrasi admin (sudah ada, dipertahankan)
function registrasiAdmin($data) {
    global $conn;
    $nama = htmlspecialchars($data['nama']);
    $username = strtolower(stripcslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $level = isset($data['level']) ? htmlspecialchars($data['level']) : 'admin';
    
    $result = mysqli_query($conn, "SELECT username FROM admin WHERE username = '$username'");
    if (mysqli_fetch_assoc($result)) {
        echo "<script>alert('Username admin sudah terdaftar!');</script>";
        return false;
    }
    
    $password = password_hash($password, PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO admin VALUES('','$nama','$username','$password','$level')");
    return mysqli_affected_rows($conn);
}

// Fungsi registrasi pelanggan (sudah ada, dipertahankan)
function registrasiP($data) {
    global $conn;
    $nama = htmlspecialchars($data['nama']);
    $username = strtolower(stripcslashes($data["username"]));
    $hp = htmlspecialchars($data['hp']);
    $email = htmlspecialchars($data['email']);
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $alamat = strtolower(stripcslashes($data["alamat"]));

    $result = mysqli_query($conn, "SELECT username FROM pelanggan WHERE username = '$username'");
    if (mysqli_fetch_assoc($result)) {
        echo "<script>alert('Username sudah terdaftar!');</script>";
        return false;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO pelanggan (nama, username, hp, email, password, alamat) 
    VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $username, $hp, $email, $password, $alamat);
    $stmt->execute();

    return mysqli_affected_rows($conn);
}


// Fungsi order (diperbarui dengan prepared statement dan kolom eksplisit)
function order($data) {
    global $conn;
    session_start();

    $username = $_SESSION['userpelanggan'] ?? '';
    $nama = htmlspecialchars($data["nama"]);
    $hp = htmlspecialchars($data["hp"]);
    $layanan = htmlspecialchars($data["layananPerbaikan"]);
    $merk = htmlspecialchars($data["merk"]);
    $jenis = htmlspecialchars($data["jenisPerbaikan"]);
    $tanggal = htmlspecialchars($data["tanggal"]);
    $waktu = htmlspecialchars($data["waktu"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $status = htmlspecialchars($data["status"]);
    $teknisi = htmlspecialchars($data["teknisi"]);
    $biaya = htmlspecialchars($data["biaya"]);
    $pembayaran = htmlspecialchars($data["pembayaran"]); // Tambahan pembayaran

    $stmt = mysqli_prepare($conn, "INSERT INTO orderperbaikan 
        (username, nama, hp, layananperbaikan, merk, jenisperbaikan, tanggal, waktu, alamat, status, teknisi, biaya, pembayaran) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "sssssssssssis", 
        $username, $nama, $hp, $layanan, $merk, $jenis, 
        $tanggal, $waktu, $alamat, $status, $teknisi, $biaya, $pembayaran);

    mysqli_stmt_execute($stmt);
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected_rows;
}

// Fungsi query (dipertahankan)
function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Fungsi completed order (diperbarui dengan prepared statement)
function completed($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE orderperbaikan SET status = 'Complete' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected_rows;
}

// Fungsi cancel order (diperbarui dengan prepared statement)
function canceled($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE orderperbaikan SET status = 'Cancel' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected_rows;
}

// Fungsi ambil order (diperbarui dengan prepared statement)
function ambil($id) {
    session_start();
    $user = $_SESSION['userteknisi'];
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE orderperbaikan SET status = 'Dalam Penanganan', teknisi = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $user, $id);
    mysqli_stmt_execute($stmt);
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected_rows;
}

// Fungsi baru: update status order (untuk dashboard_admin.php)
function updateStatus($id, $new_status) {
    global $conn;
    $allowed_statuses = ['Sepatu Akan Segera Dijemput','Sepatu Diantar Kembali ke Pelanggan', 'Dalam Penanganan', 'Complete', 'Cancel', 'Pembayaranmu Terkonfirmasi', 'Diproses'];
    if (in_array($new_status, $allowed_statuses)) {
        $stmt = mysqli_prepare($conn, "UPDATE orderperbaikan SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_affected_rows($stmt);
    }
    return 0;
}

// Fungsi baru: update catatan admin (untuk dashboard_admin.php)
function updateCatatan($id, $catatan_admin) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE orderperbaikan SET catatan_admin = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $catatan_admin, $id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_affected_rows($stmt);
}

// Fungsi baru: hapus order (untuk dashboard_admin.php)
function deleteOrder($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "DELETE FROM orderperbaikan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_affected_rows($stmt);
}

?>
