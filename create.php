<?php
include 'config.php';

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maSV = $_POST['MaSV'];
    $hoTen = $_POST['HoTen'];
    $gioiTinh = $_POST['GioiTinh'];
    $ngaySinh = $_POST['NgaySinh'];
    $maNganh = $_POST['MaNganh'];
    $hinhPath = "";

    // Kiểm tra nếu có file ảnh được tải lên
    if (isset($_FILES["Hinh"]) && $_FILES["Hinh"]["error"] == 0) {
        $target_dir = "upload/";
        $imageFileType = strtolower(pathinfo($_FILES["Hinh"]["name"], PATHINFO_EXTENSION));
        $allowedTypes = array("jpg", "jpeg", "png", "gif");

        // Tạo thư mục upload nếu chưa có
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Kiểm tra định dạng file hợp lệ
        if (!in_array($imageFileType, $allowedTypes)) {
            $errorMessage = "Chỉ cho phép tải lên file ảnh (JPG, JPEG, PNG, GIF).";
        } else {
            // Tạo tên file mới để tránh trùng lặp
            $newFileName = uniqid() . "." . $imageFileType;
            $target_file = $target_dir . $newFileName;

            // Tiến hành upload file
            if (move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
                $hinhPath = $newFileName; // Chỉ lưu tên file vào CSDL
            } else {
                $errorMessage = "Có lỗi khi tải ảnh lên.";
            }
        }
    } else {
        $errorMessage = "Vui lòng chọn một file ảnh.";
    }

    // Nếu không có lỗi, thêm dữ liệu vào CSDL
    if (empty($errorMessage)) {
        $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
                VALUES ('$maSV', '$hoTen', '$gioiTinh', '$ngaySinh', '$hinhPath', '$maNganh')";

        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
            exit();
        } else {
            $errorMessage = "Lỗi: " . $conn->error;
        }
    }
}

$result = $conn->query("SELECT * FROM NganhHoc");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Thêm Sinh Viên</h2>

        <!-- Hiển thị lỗi nếu có -->
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
        <?php endif; ?>

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
                <label>Chọn Ảnh</label>
                <input type="file" name="Hinh" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Ngành Học</label>
                <select name="MaNganh" class="form-control">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['MaNganh'] ?>"><?= $row['TenNganh'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Thêm</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>
