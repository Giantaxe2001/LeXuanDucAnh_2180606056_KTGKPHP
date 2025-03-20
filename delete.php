<?php
include 'config.php';

if (isset($_GET['id'])) {
    $maSV = $_GET['id'];
    $sql = "SELECT * FROM SinhVien WHERE MaSV = '$maSV'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $sinhvien = $result->fetch_assoc();
    } else {
        die("Không tìm thấy sinh viên!");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "DELETE FROM SinhVien WHERE MaSV='$maSV'";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xóa Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>XÓA THÔNG TIN</h2>
        <p>Bạn có chắc muốn xóa sinh viên này?</p>
        <p><strong><?= $sinhvien['HoTen'] ?></strong></p>
        <img src="upload/<?= $sinhvien['Hinh'] ?>" width="100">
        <form method="post">
            <button type="submit" class="btn btn-danger">Xóa</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>
