<?php
include 'config.php';

session_start();
if (!isset($_SESSION['MaSV'])) {
    die("Vui lòng đăng nhập để đăng ký học phần!");
}

$maSV = $_SESSION['MaSV'];

// Lấy danh sách học phần
$sql = "SELECT * FROM HocPhan";
$result = $conn->query($sql);
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
        <h2 class="mb-3 text-center">DANH SÁCH HỌC PHẦN</h2>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Mã Học Phần</th>
                    <th>Tên Học Phần</th>
                    <th>Số Tín Chỉ</th>
                    <th>Đăng Ký</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['MaHP']) ?></td>
                    <td><?= htmlspecialchars($row['TenHP']) ?></td>
                    <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                    <td>
                        <form action="dangkyhocphan.php" method="post">
                            <input type="hidden" name="MaHP" value="<?= $row['MaHP'] ?>">
                            <button type="submit" class="btn btn-success">Đăng Ký</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </div>
</body>
</html>
