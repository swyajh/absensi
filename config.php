<?php
date_default_timezone_set("Asia/Jakarta");

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_absensi';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
