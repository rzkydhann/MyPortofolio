<?php 

$conn = mysqli_connect("localhost", "root", "", "teknisi");

if (isset($_POST['kirim'])) {

	$nama = $_POST['nama'];
	$password = $_POST['password'];

	mysqli_query($conn, "INSERT INTO contoh VALUES('$nama', '$password')");

}



// mysqli_query($conn, "INSERT INTO contoh VALUES")


 ?>


<!DOCTYPE html>
<html>
<head>
	<title>testpage</title>
</head>
<body>

<form action="" method="post">
<label for="nama">Nama</label>
<input type="text" name="nama" id="nama">
<br>
<label for="password">password</label>
<input type="password" name="password" id="password">
<br>
<button type="submit" name="kirim">kirim</button>
	
</form>

</body>
</html>