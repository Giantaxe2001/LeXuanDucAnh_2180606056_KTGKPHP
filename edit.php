<?php
include 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Lỗi: Không có ID sinh viên được cung cấp.");
}

$maSV = $_GET['id'];

// Lấy thông tin sinh viên
$sql = "SELECT * FROM SinhVien WHERE MaSV = '$maSV'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Không tìm thấy sinh viên!");
}

$sinhvien = $result->fetch_assoc();

// Xử lý cập nhật thông tin sinh viên
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoTen = $_POST['HoTen'];
    $gioiTinh = $_POST['GioiTinh'];
    $ngaySinh = $_POST['NgaySinh'];
    $maNganh = $_POST['MaNganh'];
    $hinhPath = $sinhvien['Hinh']; // Giữ nguyên hình cũ nếu không có hình mới

    // Xử lý upload ảnh mới (nếu có)
    if (!empty($_FILES["Hinh"]["name"])) {
        $target_dir = "upload/";
        $imageFileType = strtolower(pathinfo($_FILES["Hinh"]["name"], PATHINFO_EXTENSION));
        $allowedTypes = array("jpg", "jpeg", "png", "gif");

        // Kiểm tra file ảnh hợp lệ
        if (in_array($imageFileType, $allowedTypes)) {
            // Tạo tên file mới để tránh trùng lặp
            $newFileName = uniqid() . "." . $imageFileType;
            $target_file = $target_dir . $newFileName;

            // Tiến hành upload file
            if (move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
                $hinhPath = $newFileName; // Lưu tên file mới vào CSDL

                // Xóa ảnh cũ nếu có
                if (!empty($sinhvien['Hinh']) && file_exists("upload/" . $sinhvien['Hinh'])) {
                    unlink("upload/" . $sinhvien['Hinh']);
                }
            } else {
                die("Lỗi khi tải ảnh lên.");
            }
        } else {
            die("Chỉ cho phép tải ảnh định dạng JPG, JPEG, PNG, GIF.");
        }
    }

    // Cập nhật dữ liệu vào CSDL
    $sql = "UPDATE SinhVien 
            SET HoTen='$hoTen', GioiTinh='$gioiTinh', NgaySinh='$ngaySinh', Hinh='$hinhPath', MaNganh='$maNganh' 
            WHERE MaSV='$maSV'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('✅ Cập nhật thành công!'); window.location.href='index.php';</script>";
    } else {
        die("❌ Lỗi khi cập nhật: " . $conn->error);
    }
}

// Lấy danh sách ngành học
$nganhHoc = $conn->query("SELECT * FROM NganhHoc");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Hiệu chỉnh thông tin sinh viên</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Họ Tên</label>
                <input type="text" name="HoTen" class="form-control" value="<?= htmlspecialchars($sinhvien['HoTen']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Giới Tính</label>
                <select name="GioiTinh" class="form-control">
                    <option value="Nam" <?= $sinhvien['GioiTinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                    <option value="Nữ" <?= $sinhvien['GioiTinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Ngày Sinh</label>
                <input type="date" name="NgaySinh" class="form-control" value="<?= $sinhvien['NgaySinh'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Hình Ảnh Hiện Tại</label><br>
                <?php 
                    $imagePath = !empty($sinhvien['Hinh']) ? "upload/" . htmlspecialchars($sinhvien['Hinh']) : "upload/dung.jpg";
                ?>
                <img src="<?= htmlspecialchars($imagePath) ?>" width="100">
            </div>
            <div class="mb-3">
                <label>Chọn Ảnh Mới (nếu cần thay đổi)</label>
                <input type="file" name="Hinh" class="form-control">
            </div>
            <div class="mb-3">
                <label>Ngành Học</label>
                <select name="MaNganh" class="form-control">
                    <?php while ($row = $nganhHoc->fetch_assoc()): ?>
                        <option value="<?= $row['MaNganh'] ?>" <?= $row['MaNganh'] == $sinhvien['MaNganh'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['TenNganh']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Cập nhật</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>
