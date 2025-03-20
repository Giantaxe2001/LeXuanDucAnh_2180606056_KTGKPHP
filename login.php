<?php
include 'config.php';
session_start();

$errorMessage = "";

// Xử lý khi sinh viên đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maSV = trim($_POST['MaSV']);

    // Kiểm tra sinh viên có tồn tại trong CSDL không
    $sql = "SELECT * FROM SinhVien WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maSV);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['MaSV'] = $maSV; // Lưu session cho sinh viên
        header("Location: index.php"); // Chuyển hướng về trang chính
        exit();
    } else {
        $errorMessage = "❌ Mã sinh viên không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">ĐĂNG NHẬP</h2>
        <form action="" method="post">
            <div class="mb-3">
                <label for="MaSV" class="form-label">Mã Sinh Viên</label>
                <input type="text" name="MaSV" class="form-control" required>
            </div>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger"><?= $errorMessage ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">Đăng Nhập</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>
