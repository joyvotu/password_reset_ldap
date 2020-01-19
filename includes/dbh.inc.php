<?php 

$servername = "localhost";
$dBUsername = "root";
$dBPassword = "";
$dBName = "password_reset";

$conn = mysqli_connect($servername,$dBUsername,$dBPassword,$dBName);

if (!$conn) {
	echo "database handler failed";
	die("connection failed: ".mysqli_connect_error());
}

?>