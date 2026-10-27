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
    <title>Màn hình chính - Hệ thống Quản trị & Trắc nghiệm</title>
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
        .top-navbar { background: #fff; padding: 15px 30px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .welcome-banner { background: linear-gradient(135deg, #1850df 0%, #3b82f6 100%); color: #fff; border-radius: 16px; padding: 35px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(24,80,223,0.2); }
        .card-menu { border: none; border-radius: 16px; transition: transform 0.2s, box-shadow 0.2s; background: #fff; text-align: center; padding: 25px; height: 100%; text-decoration: none; color: inherit; display: block; box-shadow: 0 2px 6px rgba(0,0,0,0.04); }
        .card-menu:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); color: var(--azota-primary); }
        .card-menu i { font-size: 32px; color: var(--azota-primary); margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><i class="fa-solid fa-shapes"></i></div>
        <a href="index.php" class="active" title="Trang chủ"><i class="fa-solid fa-house"></i></a>
        <a href="quiz_bank.php" title="Kho đề thi"><i class="fa-solid fa-folder-open"></i></a>
        <?php if ($role == 'teacher'): ?>
            <a href="create_quiz_fast.php" title="Soạn đề nhanh"><i class="fa-solid fa-file-pen"></i></a>
        <?php endif; ?>
        <a href="logout.php" title="Đăng xuất" class="mt-auto mb-4 text-danger"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <h5 class="mb-0 text-secondary fw-bold">Màn hình chính</h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-primary me-3 px-3 py-2"><?php echo ($role == 'teacher') ? 'Giáo viên' : 'Học sinh'; ?></span>
                <div class="fw-bold text-dark"><i class="fa-solid fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></div>
            </div>
        </div>

        <div class="welcome-banner">
            <h2>Chào mừng trở lại, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
            <p class="mb-0 opacity-75">Quản lý hệ thống giáo dục, kho đề thi và luyện tập trực tuyến một cách hiệu quả.</p>
        </div>

        <h5 class="mb-3 fw-bold text-dark">Quản lý học tập & Đề thi</h5>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <a href="quiz_bank.php" class="card-menu">
                    <i class="fa-solid fa-book-open"></i>
                    <h6 class="fw-bold mb-1">Kho Đề Thi</h6>
                    <p class="text-muted small mb-0">Xem danh sách đề, làm bài & chia sẻ link</p>
                </a>
            </div>
            <?php if ($role == 'teacher'): ?>
            <div class="col-md-4">
                <a href="create_quiz_fast.php" class="card-menu">
                    <i class="fa-solid fa-file-circle-plus"></i>
                    <h6 class="fw-bold mb-1">Soạn Đề Từng Câu</h6>
                    <p class="text-muted small mb-0">Tạo đề thi tương tác trực quan</p>
                </a>
            </div>
            <?php endif; ?>
            <div class="col-md-4">
                <a href="logout.php" class="card-menu border-danger">
                    <i class="fa-solid fa-power-off text-danger"></i>
                    <h6 class="fw-bold text-danger">Đăng Xuất</h6>
                    <p class="text-muted small mb-0">Thoát khỏi phiên làm việc an toàn</p>
                </a>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
