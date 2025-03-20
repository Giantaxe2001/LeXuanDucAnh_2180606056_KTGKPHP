<?php
$servername = "localhost";
$username = "root";   // Tài khoản MySQL mặc định của XAMPP
$password = "";       // Để trống nếu dùng XAMPP
$database = "Test1";  // Tên cơ sở dữ liệu của bạn

// Kết nối MySQL
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
