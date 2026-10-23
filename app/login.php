<?php
ob_start();
session_start();
$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
if ($conn->connect_error) { die("Lỗi kết nối CSDL"); }

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('teacher', 'student') NOT NULL DEFAULT 'student'
)");

$check = $conn->query("SELECT COUNT(*) as cnt FROM users");
$row = $check->fetch_assoc();
if ($row['cnt'] == 0) {
    $pass_hash = password_hash('123456', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, password, role) VALUES ('giaovien', '$pass_hash', 'teacher')");
    $conn->query("INSERT INTO users (username, password, role) VALUES ('hocsinh', '$pass_hash', 'student')");
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else { $error = "Mật khẩu không chính xác!"; }
    } else { $error = "Tên đăng nhập không tồn tại!"; }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập Hệ Thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
<div class="card shadow-sm p-4" style="width: 400px; border-radius: 12px;">
    <h3 class="text-center mb-4 fw-bold text-primary">🔐 Đăng Nhập Hệ Thống</h3>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?php echo $error; ?></div><?php endif; ?>
    <div class="alert alert-info py-2 small mb-3">
        <strong>Tài khoản mẫu:</strong><br>
        - Giáo viên: <code>giaovien</code> / <code>123456</code><br>
        - Học sinh: <code>hocsinh</code> / <code>123456</code>
    </div>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-bold">Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Đăng Nhập</button>
    </form>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
