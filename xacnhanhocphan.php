<?php
include 'config.php';
session_start();

if (!isset($_SESSION['MaSV']) || !isset($_SESSION['dangky_thanhcong'])) {
    header("Location: dangkyhocphan.php");
    exit();
}

$maSV = $_SESSION['MaSV'];

// Lấy thông tin sinh viên
$sqlSinhVien = "SELECT sv.MaSV, sv.HoTen, sv.NgaySinh, nh.TenNganh 
                FROM SinhVien sv 
                JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh 
                WHERE sv.MaSV = ?";
$stmtSV = $conn->prepare($sqlSinhVien);
$stmtSV->bind_param("s", $maSV);
$stmtSV->execute();
$sinhVien = $stmtSV->get_result()->fetch_assoc();

// Lấy thông tin đăng ký
$sqlDangKy = "SELECT hp.MaHP, hp.TenHP, hp.SoTinChi 
              FROM DangKy dk 
              JOIN ChiTietDangKy cdk ON dk.MaDK = cdk.MaDK 
              JOIN HocPhan hp ON cdk.MaHP = hp.MaHP 
              WHERE dk.MaSV = ?";
$stmtDK = $conn->prepare($sqlDangKy);
$stmtDK->bind_param("s", $maSV);
$stmtDK->execute();
$resultDK = $stmtDK->get_result();

// Xóa session xác nhận
unset($_SESSION['dangky_thanhcong']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác Nhận Đăng Ký</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-3 text-center">Thông Tin Đăng Ký</h2>
        <table class="table table-bordered">
            <tr><th>Mã số sinh viên:</th><td><?= $sinhVien['MaSV'] ?></td></tr>
            <tr><th>Họ Tên:</th><td><?= $sinhVien['HoTen'] ?></td></tr>
            <tr><th>Ngành Học:</th><td><?= $sinhVien['TenNganh'] ?></td></tr>
            <tr><th>Ngày Đăng Ký:</th><td><?= date("d/m/Y") ?></td></tr>
        </table>
        <a href="index.php" class="btn btn-primary">Trở Về Trang Chủ</a>
    </div>
</body>
</html>
