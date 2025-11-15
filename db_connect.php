<?php
// db_connect.php
$servername = "localhost";
$username = "root"; // Username mặc định của XAMPP
$password = ""; // Password mặc định của XAMPP
$dbname = "db_quanly_user";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập charset
$conn->set_charset("utf8");
?>