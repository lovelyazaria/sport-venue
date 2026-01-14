<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sports_booking";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database GAGAL: " . mysqli_connect_error());
}
