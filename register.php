<?php
include 'config.php';
session_start();

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maSV = trim($_POST['MaSV']);
    $hoTen = trim($_POST['HoTen']);
    $gioiTinh = $_POST['GioiTinh'];
    $ngaySinh = $_POST['NgaySinh'];
    $maNganh = $_POST['MaNganh'];
    $hinhPath = "dung.jpg"; // Ảnh mặc định nếu không tải lên

    // Kiểm tra xem Mã SV đã tồn tại chưa
    $checkSQL = "SELECT * FROM SinhVien WHERE MaSV = ?";
    $stmt = $conn->prepare($checkSQL);
    $stmt->bind_param("s", $maSV);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        $errorMessage = "❌ Mã sinh viên đã tồn tại!";
    } else {
        // Xử lý tải lên ảnh nếu có
        if (!empty($_FILES["Hinh"]["name"])) {
            $target_dir = "upload/";
            $imageFileType = strtolower(pathinfo($_FILES["Hinh"]["name"], PATHINFO_EXTENSION));
            $allowedTypes = array("jpg", "jpeg", "png", "gif");

            if (in_array($imageFileType, $allowedTypes)) {
                $newFileName = uniqid() . "." . $imageFileType;
                $target_file = $target_dir . $newFileName;

                if (move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
                    $hinhPath = $newFileName;
                } else {
                    $errorMessage = "❌ Lỗi khi tải ảnh lên!";
                }
            } else {
                $errorMessage = "❌ Chỉ cho phép ảnh JPG, JPEG, PNG, GIF!";
            }
        }

        // Nếu không có lỗi, tiến hành lưu dữ liệu
        if (empty($errorMessage)) {
            $insertSQL = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSQL);
            $stmt->bind_param("ssssss", $maSV, $hoTen, $gioiTinh, $ngaySinh, $hinhPath, $maNganh);

            if ($stmt->execute()) {
                $_SESSION['MaSV'] = $maSV; // Tự động đăng nhập
                header("Location: index.php");
                exit();
            } else {
                $errorMessage = "❌ Lỗi khi đăng ký: " . $conn->error;
            }
        }
    }
}

// Lấy danh sách ngành học
$nganhHoc = $conn->query("SELECT * FROM NganhHoc");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">ĐĂNG KÝ SINH VIÊN</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Mã Sinh Viên</label>
                <input type="text" name="MaSV" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Họ Tên</label>
                <input type="text" name="HoTen" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Giới Tính</label>
                <select name="GioiTinh" class="form-control">
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Ngày Sinh</label>
                <input type="date" name="NgaySinh" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Chọn Ảnh Đại Diện (Tùy chọn)</label>
                <input type="file" name="Hinh" class="form-control">
            </div>
            <div class="mb-3">
                <label>Ngành Học</label>
                <select name="MaNganh" class="form-control">
                    <?php while ($row = $nganhHoc->fetch_assoc()): ?>
                        <option value="<?= $row['MaNganh'] ?>"><?= htmlspecialchars($row['TenNganh']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger"><?= $errorMessage ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">Đăng Ký</button>
            <a href="login.php" class="btn btn-secondary">Đã có tài khoản? Đăng nhập</a>
        </form>
    </div>
</body>
</html>
