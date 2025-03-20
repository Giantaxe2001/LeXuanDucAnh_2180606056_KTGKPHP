<?php
include 'config.php';

// Kiểm tra nếu ID không tồn tại
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Lỗi: Không có ID sinh viên được cung cấp.");
}

$maSV = $_GET['id'];

// Kiểm tra xem sinh viên có tồn tại không
$sql = "SELECT sv.*, nh.TenNganh FROM SinhVien sv 
        JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh 
        WHERE MaSV = '$maSV'";

$result = $conn->query($sql);

// Hiển thị lỗi nếu có vấn đề
if (!$result) {
    die("Lỗi truy vấn SQL: " . $conn->error);
}

if ($result->num_rows == 0) {
    die("Không tìm thấy sinh viên với ID: " . htmlspecialchars($maSV));
}

$sinhvien = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Thông tin chi tiết</h2>
        <p><strong>Họ Tên:</strong> <?= htmlspecialchars($sinhvien['HoTen']) ?></p>
        <p><strong>Giới Tính:</strong> <?= htmlspecialchars($sinhvien['GioiTinh']) ?></p>
        <p><strong>Ngày Sinh:</strong> <?= date("d/m/Y", strtotime($sinhvien['NgaySinh'])) ?></p>
        <p><strong>Ngành Học:</strong> <?= htmlspecialchars($sinhvien['TenNganh']) ?></p>
        
        <p><strong>Ảnh:</strong></p>
        <?php 
            $imagePath = "upload/" . basename($sinhvien['Hinh']);
            if (!file_exists($imagePath) || empty($sinhvien['Hinh'])) {
                $imagePath = "upload/dung.jpg"; // Ảnh mặc định
            }
        ?>
        <img src="<?= htmlspecialchars($imagePath) ?>" width="150">

        <br>
        <a href="index.php" class="btn btn-secondary mt-3">Quay lại</a>
    </div>
</body>
</html>
