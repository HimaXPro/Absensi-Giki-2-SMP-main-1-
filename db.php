<?php
$host = "localhost"; // Ganti dengan host database Anda
$dbname = "absensi"; // Ganti dengan nama database Anda
$username = "root"; // Ganti dengan username MySQL Anda
$password = "root"; // Ganti dengan password MySQL Anda

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
}
?>
