<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$role = $_SESSION['role'] ?? 'student';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Quản trị & Thi Trắc nghiệm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 260px; background: #1e293b; min-height: 100vh; position: fixed; top: 0; left: 0; color: #fff; padding: 20px; }
        .sidebar .brand { font-size: 1.2rem; font-weight: bold; margin-bottom: 30px; color: #38bdf8; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 8px; text-decoration: none; margin-bottom: 8px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #334155; color: #fff; }
        .main-content { margin-left: 260px; padding: 30px; }
        .top-bar { background: #fff; padding: 15px 25px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .welcome-card { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #fff; border-radius: 14px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(2,132,199,0.2); }
        .menu-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; text-align: center; text-decoration: none; color: inherit; display: block; transition: 0.2s; }
        .menu-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: #0ea5e9; color: #0284c7; }
        .menu-card i { font-size: 2.5rem; color: #0ea5e9; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-server"></i> SYS-QUIZ ADMIN</div>
    <a href="index.php" class="active"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <a href="quiz_bank.php"><i class="fa-solid fa-folder-open"></i> Kho đề thi</a>
    <?php if ($role == 'teacher'): ?>
        <a href="create_quiz_fast.php"><i class="fa-solid fa-file-pen"></i> Soạn đề nhanh</a>
    <?php endif; ?>
    <a href="logout.php" class="text-danger mt-auto"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
</div>

<div class="main-content">
    <div class="top-bar">
        <h5 class="mb-0 fw-bold text-dark">Hệ thống Quản trị & Thi Trắc nghiệm</h5>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-<?php echo ($role=='teacher')?'danger':'primary'; ?> px-3 py-2"><?php echo ($role=='teacher')?'Giáo viên':'Học sinh'; ?></span>
            <span class="fw-bold text-secondary"><i class="fa-solid fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
    </div>

    <div class="welcome-card">
        <h2>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p class="mb-0 opacity-75">Hệ thống quản lý ôn tập và kiểm tra trắc nghiệm chuyên ngành Công nghệ Phần mềm & Quản trị Hệ thống.</p>
    </div>

    <h5 class="fw-bold mb-3">Chức năng chính</h5>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="quiz_bank.php" class="menu-card">
                <i class="fa-solid fa-book"></i>
                <h5 class="fw-bold">Kho Đề Thi</h5>
                <p class="text-muted small mb-0">Làm bài và xem thống kê kết quả ôn tập</p>
            </a>
        </div>
        <?php if ($role == 'teacher'): ?>
        <div class="col-md-4">
            <a href="create_quiz_fast.php" class="menu-card">
                <i class="fa-solid fa-file-lines"></i>
                <h5 class="fw-bold">Soạn Đề Nhanh</h5>
                <p class="text-muted small mb-0">Nhập danh sách câu hỏi trắc nghiệm tự động</p>
            </a>
        </div>
        <?php endif; ?>
        <div class="col-md-4">
            <a href="logout.php" class="menu-card border-danger">
                <i class="fa-solid fa-power-off text-danger"></i>
                <h5 class="fw-bold text-danger">Đăng Xuất</h5>
                <p class="text-muted small mb-0">Kết thúc phiên làm việc an toàn</p>
            </a>
        </div>
    </div>
</div>

</body>
</html>
