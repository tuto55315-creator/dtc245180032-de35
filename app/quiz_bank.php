<?php
$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
if ($conn->connect_error) { die("Lỗi kết nối CSDL"); }

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT e.*, COUNT(r.id) AS total_attempts 
        FROM exams e 
        LEFT JOIN results r ON e.id = r.exam_id";

if (!empty($search)) {
    $search_clean = $conn->real_escape_string($search);
    $sql .= " WHERE e.title LIKE '%$search_clean%' OR e.subject LIKE '%$search_clean%'";
}

$sql .= " GROUP BY e.id ORDER BY e.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kho Đề Thi & Thống Kê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📚 Kho Đề Thi & Thống Kê Lượt Làm Bài</h2>
        <div>
            <a href="index.php" class="btn btn-secondary">Trang Chủ</a>
            <a href="create_quiz_fast.php" class="btn btn-success">+ Soạn Đề Nhanh (Azota)</a>
        </div>
    </div>

    <!-- Thanh tìm kiếm đề thi -->
    <form method="GET" action="quiz_bank.php" class="row g-3 mb-4">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm đề thi theo tên hoặc môn học..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tên Đề Thi</th>
                        <th>Môn Học</th>
                        <th>Thời Gian</th>
                        <th>📊 Số Lượt Làm Bài</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td class="text-start fw-bold"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['subject']); ?></span></td>
                            <td><?php echo $row['duration_minutes']; ?> phút</td>
                            <td><span class="badge bg-success fs-6"><?php echo $row['total_attempts']; ?> lượt</span></td>
                            <td>
                                <a href="take_exam.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Làm Bài</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="py-4 text-muted">Không tìm thấy đề thi phù hợp!</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
