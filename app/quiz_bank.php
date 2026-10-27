<?php
session_start();
if (!isset($_SESSION['username'])) { 
    header("Location: login.php"); 
    exit(); 
}

$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
if ($conn->connect_error) { 
    die("Lỗi kết nối CSDL: " . $conn->connect_error); 
}

// Lấy danh sách đề thi kèm đếm số lượt làm bài từ bảng results
$query = "SELECT e.id, e.title, e.subject, e.duration_minutes, 
          (SELECT COUNT(*) FROM results r WHERE r.exam_id = e.id) AS attempt_count 
          FROM exams e ORDER BY e.id DESC";
$exams = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kho Đề Thi & Quản Lý</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --azota-primary: #1850df; --azota-bg: #f4f6f8; }
        body { background-color: var(--azota-bg); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 80px; background: #0f172a; min-height: 100vh; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; align-items: center; padding-top: 20px; z-index: 100; }
        .sidebar .brand { color: #fff; font-size: 24px; margin-bottom: 30px; }
        .sidebar a { color: #94a3b8; font-size: 20px; padding: 12px; margin-bottom: 10px; border-radius: 12px; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: var(--azota-primary); color: #fff; }
        .main-content { margin-left: 80px; padding: 30px; }
        .content-card { background: #fff; border-radius: 14px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); }
        .table th { background: #1e293b !important; color: #fff !important; font-weight: 600; text-align: center; vertical-align: middle; }
        .table td { vertical-align: middle; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><i class="fa-solid fa-shapes"></i></div>
        <a href="index.php" title="Trang chủ"><i class="fa-solid fa-house"></i></a>
        <a href="quiz_bank.php" title="Kho đề thi" class="active"><i class="fa-solid fa-folder-open"></i></a>
        <a href="logout.php" title="Đăng xuất" class="mt-auto mb-4 text-danger"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>

    <div class="main-content">
        <div class="content-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-folder-open text-primary me-2"></i> Kho Đề Thi & Quản Lý</h3>
                <span class="badge bg-primary px-3 py-2 fs-6"><i class="fa-solid fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Tên Đề Thi</th>
                            <th style="width: 180px;">Môn Học</th>
                            <th style="width: 130px;">Thời Gian</th>
                            <th style="width: 140px;">Lượt Làm Bài</th>
                            <th style="width: 200px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($exams && $exams->num_rows > 0): ?>
                            <?php while ($row = $exams->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center fw-bold">#<?php echo $row['id']; ?></td>
                                    <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark px-3 py-2"><?php echo htmlspecialchars($row['subject']); ?></span>
                                    </td>
                                    <td class="text-center text-secondary">
                                        <i class="fa-regular fa-clock me-1"></i> <?php echo (int)$row['duration_minutes']; ?> phút
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success px-3 py-2 fs-6 text-white fw-bold">
                                            <i class="fa-solid fa-users me-1"></i> <?php echo (int)$row['attempt_count']; ?> lượt
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="take_exam.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary fw-semibold px-3">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Làm Bài
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chưa có đề thi nào trong hệ thống.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
$conn->close();
?>
