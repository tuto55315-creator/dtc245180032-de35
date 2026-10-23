<?php
ob_start();
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$role = $_SESSION['role'] ?? 'student';

$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
if ($conn->connect_error) { die("Lỗi kết nối CSDL"); }

// Đảm bảo bảng exams và questions tồn tại và có Đề 35 chuyên ngành
$conn->query("CREATE TABLE IF NOT EXISTS exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    duration_minutes INT DEFAULT 15,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT,
    question_text TEXT NOT NULL,
    option_a TEXT NOT NULL,
    option_b TEXT NOT NULL,
    option_c TEXT NOT NULL,
    option_d TEXT NOT NULL,
    correct_option VARCHAR(10) NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT,
    user_id INT,
    score FLOAT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Kiểm tra xem Đề 35 đã có chưa, nếu chưa có thì tự động seed đầy đủ 10 câu hỏi
$check_de35 = $conn->query("SELECT id FROM exams WHERE id = 35 OR title LIKE '%Đề 35%'");
if ($check_de35->num_rows == 0) {
    $conn->query("INSERT INTO exams (id, title, subject, duration_minutes) VALUES (35, 'Đề thi tổng hợp: Triển khai & Quản trị Hệ thống Phần mềm (Đề 35)', 'Công nghệ Phần mềm', 15)");
    
    $q_list = [
        ["Docker container khác gì so với Virtual Machine truyền thống?", "Container chia sẻ kernel của hệ điều hành host, nhẹ hơn VM", "VM không cần phần cứng ảo hóa", "Container chạy độc lập hoàn toàn phần cứng", "Không có điểm khác biệt", "A"],
        ["Lệnh nào dùng để khởi chạy các service trong file docker-compose.yml ở chế độ nền?", "docker compose up -d", "docker run -d", "docker start compose", "docker-compose init", "A"],
        ["Mục đích chính của Nginx Reverse Proxy là gì?", "Điều phối request từ client đến các backend server an toàn và cân bằng tải", "Lưu trữ cơ sở dữ liệu quan hệ", "Biên dịch mã nguồn PHP", "Tăng dung lượng RAM ảo", "A"],
        ["Trong Docker Network, lệnh internal: true có ý nghĩa gì?", "Cô lập hoàn toàn mạng nội bộ, không cho phép truy cập ra ngoài internet", "Cho phép mọi IP public truy cập", "Tăng tốc độ truyền tải gói tin", "Tự động tạo chứng chỉ SSL", "A"],
        ["Metric nào sau đây được Prometheus thu thập để giám sát tài nguyên?", "CPU usage, Memory utilization, Network I/O", "Màu sắc giao diện web", "Số lượng dòng code PHP", "Tên người dùng đăng nhập", "A"],
        ["Công cụ nào trong hệ thống giám sát chịu trách nhiệm đẩy log từ file hệ thống về Loki?", "Promtail", "Grafana", "Nginx", "MySQL", "A"],
        ["Cú pháp LogQL nào dùng để tìm kiếm các log chứa từ khóa 'error' trong container web?", "{container_name=\"quiz_web_hardened\"} |= \"error\"", "SELECT * FROM logs", "grep error /var/log", "SHOW LOGS", "A"],
        ["Hành động nào là biện pháp Hardening bảo mật cơ bản cho cơ sở dữ liệu MySQL trên Docker?", "Không mở port CSDL ra ngoài host, sử dụng mật khẩu mạnh và phân quyền chặt chẽ", "Đặt mật khẩu là root/root", "Cho phép mọi host truy cập port 3306", "Tắt tính năng mã hóa", "A"],
        ["Giao thức nào được sử dụng để mã hóa dữ liệu truyền tải web qua Nginx Reverse Proxy?", "HTTPS / TLS", "HTTP", "FTP", "Telnet", "A"],
        ["Mục đích của việc giới hạn tài nguyên (cpus và memory) trong docker-compose là gì?", "Ngăn chặn tiến trình chiếm dụng quá mức tài nguyên gây sập máy chủ (DoS)", "Tăng tốc độ CPU vật lý", "Giảm dung lượng ổ cứng", "Mở rộng băng tần mạng", "A"]
    ];

    foreach ($q_list as $q) {
        $stmt = $conn->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (35, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $q[0], $q[1], $q[2], $q[3], $q[4], $q[5]);
        $stmt->execute();
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT e.*, COUNT(r.id) AS total_attempts FROM exams e LEFT JOIN results r ON e.id = r.exam_id";
if (!empty($search)) {
    $s_clean = $conn->real_escape_string($search);
    $sql .= " WHERE e.title LIKE '%$s_clean%' OR e.subject LIKE '%$s_clean%'";
}
$sql .= " GROUP BY e.id ORDER BY e.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kho Đề Thi & Ôn Tập</title>
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
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-server"></i> SYS-QUIZ ADMIN</div>
    <a href="index.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <a href="quiz_bank.php" class="active"><i class="fa-solid fa-folder-open"></i> Kho đề thi</a>
    <?php if ($role == 'teacher'): ?>
        <a href="create_quiz_fast.php"><i class="fa-solid fa-file-pen"></i> Soạn đề nhanh</a>
    <?php endif; ?>
    <a href="logout.php" class="text-danger mt-auto"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
</div>

<div class="main-content">
    <div class="top-bar">
        <h4 class="mb-0 fw-bold text-dark">📚 Kho Đề Thi Trắc Nghiệm</h4>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-<?php echo ($role=='teacher')?'danger':'primary'; ?> px-3 py-2"><?php echo ($role=='teacher')?'Giáo viên':'Học sinh'; ?></span>
            <span class="fw-bold text-secondary"><i class="fa-solid fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control py-2" placeholder="Tìm kiếm đề thi..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Tìm kiếm</button>
        </div>
    </form>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th class="py-3">ID</th>
                        <th class="text-start py-3">Tên Đề Thi</th>
                        <th class="py-3">Môn Học</th>
                        <th class="py-3">Thời Gian</th>
                        <th class="py-3">Lượt Làm Bài</th>
                        <th class="py-3">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?php echo $row['id']; ?></td>
                            <td class="text-start fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><span class="badge bg-info text-dark px-3 py-2"><?php echo htmlspecialchars($row['subject']); ?></span></td>
                            <td><?php echo $row['duration_minutes']; ?> phút</td>
                            <td><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fw-bold"><?php echo $row['total_attempts']; ?> lượt</span></td>
                            <td>
                                <a href="take_exam.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary px-3 fw-bold"><i class="fa-solid fa-pen-to-square me-1"></i> Làm Bài</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="py-5 text-muted">Không tìm thấy đề thi!</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
<?php ob_end_flush(); ?>
