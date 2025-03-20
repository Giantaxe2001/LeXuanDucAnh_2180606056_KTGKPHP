<?php
include 'config.php';
session_start();

if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit();
}

$maSV = $_SESSION['MaSV'];

// Nếu chưa có session đăng ký học phần, tạo mới
if (!isset($_SESSION['hocphan_dangky'])) {
    $_SESSION['hocphan_dangky'] = [];
}

// Thêm học phần vào danh sách đăng ký (nếu chưa có)
if (isset($_GET['register'])) {
    $maHP = $_GET['register'];

    // Kiểm tra học phần có tồn tại trong database không
    $sqlCheck = "SELECT * FROM HocPhan WHERE MaHP = ?";
    $stmt = $conn->prepare($sqlCheck);
    $stmt->bind_param("s", $maHP);
    $stmt->execute();
    $resultCheck = $stmt->get_result();

    if ($resultCheck->num_rows > 0) {
        if (!in_array($maHP, $_SESSION['hocphan_dangky'])) {
            $_SESSION['hocphan_dangky'][] = $maHP;
        }
    }

    header("Location: dangkyhocphan.php");
    exit();
}

// Xóa học phần khỏi danh sách
if (isset($_GET['xoaHP'])) {
    $maHP = $_GET['xoaHP'];
    $_SESSION['hocphan_dangky'] = array_diff($_SESSION['hocphan_dangky'], [$maHP]);
    header("Location: dangkyhocphan.php");
    exit();
}

// Xóa toàn bộ đăng ký học phần
if (isset($_GET['xoaTatCa'])) {
    $_SESSION['hocphan_dangky'] = [];
    header("Location: dangkyhocphan.php");
    exit();
}

// Lấy danh sách học phần đã chọn
$hocPhanDangKy = [];
$tongTinChi = 0;

if (!empty($_SESSION['hocphan_dangky'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['hocphan_dangky']), '?'));
    $sql = "SELECT * FROM HocPhan WHERE MaHP IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat("s", count($_SESSION['hocphan_dangky'])), ...$_SESSION['hocphan_dangky']);
    $stmt->execute();
    $result = $stmt->get_result();
    $hocPhanDangKy = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($hocPhanDangKy as $hp) {
        $tongTinChi += $hp['SoTinChi'];
    }
}

// Lưu đăng ký vào database
if (isset($_POST['xacNhan'])) {
    $sqlInsertDK = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (NOW(), ?)";
    $stmt = $conn->prepare($sqlInsertDK);
    $stmt->bind_param("s", $maSV);
    $stmt->execute();
    $maDK = $conn->insert_id;

    $sqlInsertCTDK = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)";
    $stmtCTDK = $conn->prepare($sqlInsertCTDK);

    foreach ($_SESSION['hocphan_dangky'] as $maHP) {
        $stmtCTDK->bind_param("is", $maDK, $maHP);
        $stmtCTDK->execute();
    }

    $_SESSION['hocphan_dangky'] = []; // Xóa session sau khi lưu
    $_SESSION['dangky_thanhcong'] = true;
    header("Location: xacnhanhocphan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký Học Phần</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-3 text-center">Đăng Ký Học Phần</h2>

        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>MaHP</th>
                    <th>Tên Học Phần</th>
                    <th>Số Tín Chỉ</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hocPhanDangKy as $row): ?>
                <tr>
                    <td><?= $row['MaHP'] ?></td>
                    <td><?= $row['TenHP'] ?></td>
                    <td><?= $row['SoTinChi'] ?></td>
                    <td>
                        <a href="dangkyhocphan.php?xoaHP=<?= $row['MaHP'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xoá học phần này?')">Xoá</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><strong>Số học phần:</strong> <?= count($hocPhanDangKy) ?></p>
        <p><strong>Tổng số tín chỉ:</strong> <?= $tongTinChi ?></p>

        <a href="dangkyhocphan.php?xoaTatCa=1" class="btn btn-warning" onclick="return confirm('Xác nhận xoá tất cả học phần?')">Xoá Đăng Ký</a>

        <form action="" method="post" class="d-inline">
            <button type="submit" name="xacNhan" class="btn btn-success">Lưu đăng ký</button>
        </form>
    </div>
</body>
</html>
