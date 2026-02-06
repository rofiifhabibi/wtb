<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "phpuser"; 
$pass = "12345";
$db   = "lapak_kita"; // Pastikan ini sesuai dengan nama DB projectmu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>