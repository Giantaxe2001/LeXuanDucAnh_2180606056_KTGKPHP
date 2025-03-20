<?php
include 'config.php';
session_start();

// Kiểm tra nếu chưa đăng nhập, chuyển hướng đến login.php
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit();
}

$maSV = $_SESSION['MaSV'];

// Lấy thông tin sinh viên hiện tại
$sqlUser = "SELECT sv.MaSV, sv.HoTen, sv.Hinh, nh.TenNganh 
            FROM SinhVien sv 
            JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh
            WHERE sv.MaSV = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $maSV);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();

// Truy vấn danh sách sinh viên
$sql = "SELECT sv.MaSV, sv.HoTen, sv.GioiTinh, sv.NgaySinh, sv.Hinh, nh.TenNganh 
        FROM SinhVien sv 
        JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <!-- Menu điều hướng -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Trang Chủ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="hocphan.php">Học Phần</a></li>
                    <li class="nav-item"><a class="nav-link" href="dangkyhocphan.php">Đăng Ký Học Phần</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php">Sinh Viên</a></li>
                </ul>

                <!-- Hiển thị trạng thái đăng nhập -->
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['MaSV'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <?php 
                                    $userImage = !empty($user['Hinh']) ? "upload/".$user['Hinh'] : "upload/67dbd09055441.gif";
                                ?>
                                <img src="<?= $userImage ?>" width="30" height="30" class="rounded-circle">
                                <?= htmlspecialchars($user['HoTen']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="logout.php">🔴 Đăng Xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light px-3" href="login.php">🔑 Đăng Nhập</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-3 text-center">TRANG SINH VIÊN</h2>

       

        <a href="create.php" class="btn btn-primary mb-3">Thêm Sinh Viên</a>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã SV</th>
                        <th>Họ Tên</th>
                        <th>Giới Tính</th>
                        <th>Ngày Sinh</th>
                        <th>Hình</th>
                        <th>Ngành Học</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['MaSV']) ?></td>
                        <td><?= htmlspecialchars($row['HoTen']) ?></td>
                        <td><?= htmlspecialchars($row['GioiTinh']) ?></td>
                        <td><?= date("d/m/Y", strtotime($row['NgaySinh'])) ?></td>
                        <td>
                            <?php 
                                $imagePath = "upload/" . basename($row['Hinh']);
                                if (empty($row['Hinh']) || !file_exists($imagePath)) {
                                    $imagePath = "upload/dung.jpg"; 
                                }
                            ?>
                            <img src="<?= htmlspecialchars($imagePath) ?>" width="60" height="60" class="rounded">
                        </td>
                        <td><?= htmlspecialchars($row['TenNganh']) ?></td>
                        <td>
                            <a href="detail.php?id=<?= urlencode($row['MaSV']) ?>" class="btn btn-info btn-sm">Chi tiết</a>
                            <a href="edit.php?id=<?= urlencode($row['MaSV']) ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="delete.php?id=<?= urlencode($row['MaSV']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center">Không có dữ liệu sinh viên.</div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
