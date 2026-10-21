<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Thi Trắc nghiệm - Đề 35</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --sidebar-bg: #1e3a8a; --main-bg: #f1f5f9; --card-radius: 16px; }
        body { background-color: var(--main-bg); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .sidebar { width: 90px; background-color: var(--sidebar-bg); min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding-top: 1.5rem; position: fixed; left: 0; top: 0; bottom: 0; z-index: 1000; }
        .sidebar-brand { width: 50px; height: 50px; background: #ffffff; color: var(--sidebar-bg); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-bottom: 2rem; text-decoration: none; }
        .sidebar-menu { display: flex; flex-direction: column; gap: 1.25rem; width: 100%; align-items: center; }
        .sidebar-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #93c5fd; font-size: 1.3rem; text-decoration: none; transition: all 0.2s ease; }
        .sidebar-icon:hover, .sidebar-icon.active { background-color: rgba(255, 255, 255, 0.2); color: #ffffff; }
        .main-content { margin-left: 90px; padding: 1.5rem 2.5rem; }
        .top-header { display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 0.8rem 1.5rem; border-radius: var(--card-radius); box-shadow: 0 2px 10px rgba(0,0,0,0.02); margin-bottom: 1.5rem; }
        .welcome-banner { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; border-radius: var(--card-radius); padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px rgba(30, 58, 138, 0.15); }
        .feature-card { background: white; border-radius: var(--card-radius); padding: 1.5rem; text-align: center; border: 1px solid #e2e8f0; transition: all 0.3s ease; text-decoration: none; color: #1e293b; display: block; height: 100%; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 20px -5px rgba(37, 99, 235, 0.1); border-color: #bfdbfe; color: #2563eb; }
        .feature-icon-wrapper { width: 60px; height: 60px; background: #eff6ff; color: #2563eb; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 1rem auto; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="index.php" class="sidebar-brand"><i class="bi bi-shield-check"></i></a>
        <div class="sidebar-menu">
            <a href="index.php" class="sidebar-icon active" title="Trang chủ"><i class="bi bi-house-door"></i></a>
            <a href="quiz_bank.php" class="sidebar-icon" title="Kho đề thi"><i class="bi bi-folder2-open"></i></a>
            <a href="create_quiz.php" class="sidebar-icon" title="Tạo đề thi mới"><i class="bi bi-journal-plus"></i></a>
            <a href="http://172.172.12.112:8080" target="_blank" class="sidebar-icon" title="phpMyAdmin"><i class="bi bi-database"></i></a>
            <a href="http://172.172.12.112:3000" target="_blank" class="sidebar-icon" title="Grafana Monitor"><i class="bi bi-bar-chart-line"></i></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <span class="fw-semibold text-secondary"><i class="bi bi-grid-fill me-2 text-primary"></i>Hệ thống Quản trị & Thi Trắc nghiệm (Đề 35)</span>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill"><i class="bi bi-globe me-1"></i>Nginx Proxy: 8088</span>
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:38px; height:38px;">TÚ</div>
                    <div>
                        <div class="fw-bold fs-7" style="line-height:1.2;">Tú Tô</div>
                        <small class="text-muted">Quản trị viên / Giáo viên</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="welcome-banner">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-2">Chào mừng trở lại, Tú Tô 👋</h3>
                    <p class="mb-0 opacity-75">Tùy chỉnh thời gian làm bài, xáo trộn câu hỏi/đáp án & làm lại câu sai dễ dàng.</p>
                </div>
                <div>
                    <a href="quiz_bank.php" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill shadow-sm me-2"><i class="bi bi-folder2-open me-2"></i>Kho Đề Thi</a>
                    <a href="create_quiz.php" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-pill shadow-sm"><i class="bi bi-plus-circle-fill me-2"></i>Tạo Đề Mới</a>
                </div>
            </div>
        </div>

        <?php
        $servername = "mysql_db";
        $username = "quiz_user";
        $password = "SecureQuizPassword456!";
        $dbname = "quiz_db";

        $conn = new mysqli($servername, $username,$password, $dbname);$db_status = false;
        $delete_msg = "";

        if (!$conn->connect_error) {$db_status = true;
            // Cập nhật cấu trúc bảng exams có thêm thời gian & đảo câu hỏi
            $conn->query("CREATE TABLE IF NOT EXISTS exams (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                subject VARCHAR(100) NOT NULL,
                duration INT DEFAULT 15,
                shuffle_q INT DEFAULT 1,
                shuffle_opt INT DEFAULT 1,
                questions TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $conn->query("CREATE TABLE IF NOT EXISTS results (
                id INT AUTO_INCREMENT PRIMARY KEY,
                exam_id INT NOT NULL,
                student_name VARCHAR(100) NOT NULL,
                score FLOAT NOT NULL,
                total FLOAT NOT NULL,
                submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            if (isset($_GET['delete_id'])) {$del_id = intval($_GET['delete_id']);$conn->query("DELETE FROM exams WHERE id = $del_id");
                $conn->query("DELETE FROM results WHERE exam_id = $del_id");
                $delete_msg = '<div class="alert alert-success alert-dismissible fade show rounded-3 mb-3"><i class="bi bi-trash-fill me-2"></i>Đã xóa đề thi khỏi hệ thống! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            }
        }
        ?>

        <?php if (!empty($delete_msg)) echo$delete_msg; ?>

        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-layers me-2 text-primary"></i>Quản lý học tập & Hạ tầng</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <a href="create_quiz.php" class="feature-card">
                    <div class="feature-icon-wrapper"><i class="bi bi-file-earmark-text"></i></div>
                    <div class="fw-bold">Tạo đề thi</div>
                    <small class="text-muted">Cấu hình câu hỏi & thời gian</small>
                </a>
            </div>
            <div class="col-md-3">
                <a href="quiz_bank.php" class="feature-card">
                    <div class="feature-icon-wrapper"><i class="bi bi-folder2-open"></i></div>
                    <div class="fw-bold">Kho đề thi</div>
                    <small class="text-muted">Xem tất cả đề thi đã tạo</small>
                </a>
            </div>
            <div class="col-md-3">
                <a href="http://172.172.12.112:8080" target="_blank" class="feature-card">
                    <div class="feature-icon-wrapper"><i class="bi bi-database-check"></i></div>
                    <div class="fw-bold">CSDL MySQL</div>
                    <small class="text-muted">Trạng thái: <?php echo $db_status ? '<span class="text-success">Hoạt động</span>' : '<span class="text-danger">Lỗi</span>'; ?></small>
                </a>
            </div>
            <div class="col-md-3">
                <a href="http://172.172.12.112:3000" target="_blank" class="feature-card">
                    <div class="feature-icon-wrapper"><i class="bi bi-activity"></i></div>
                    <div class="fw-bold">Giám sát Grafana</div>
                    <small class="text-muted">Metrics & Log tập trung</small>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-4 p-4 shadow-sm border mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0"><i class="bi bi-collection-play me-2 text-primary"></i>Đề Thi Nổi Bật Vừa Cập Nhật</h5>
                <a href="quiz_bank.php" class="btn btn-sm btn-outline-primary rounded-pill">Xem toàn bộ Kho đề thi <i class="bi bi-arrow-right me-1"></i></a>
            </div>
            <div class="list-group list-group-flush">
                <?php
                if ($db_status) {
                    $check =$conn->query("SELECT COUNT(*) as total FROM exams");
                    $row_count =$check->fetch_assoc();
                    if ($row_count['total'] == 0) {$sample_questions = array(
                            array("q" => "Docker Compose sử dụng file cấu hình mặc định có tên là gì?", "options" => array("docker-compose.json", "docker-compose.yml", "Dockerfile", "docker-config.xml"), "answer" => 1),
                            array("q" => "Trong cấu hình Nginx Reverse Proxy của đề 35, dịch vụ Nginx được mở ra cổng Public nào?", "options" => array("80", "8080", "8088", "9090"), "answer" => 2),
                            array("q" => "Công cụ nào được sử dụng để thu thập metrics từ các container thời gian thực?", "options" => array("Promtail", "cAdvisor", "phpMyAdmin", "MySQL"), "answer" => 1),
                            array("q" => "Loki nhận log từ agent thu thập log nào trong hệ thống?", "options" => array("Prometheus", "Grafana", "Promtail", "Nginx"), "answer" => 2),
                            array("q" => "Cổng mặc định để truy cập giao diện giám sát trực quan Grafana là gì?", "options" => array("3000", "3100", "8080", "9090"), "answer" => 0)
                        );
                        $q_json = json_encode($sample_questions, JSON_UNESCAPED_UNICODE);$conn->query("INSERT INTO exams (title, subject, duration, shuffle_q, shuffle_opt, questions) VALUES ('Đề thi tổng hợp: Triển khai & Quản trị Hệ thống Phần mềm (Đề 35)', 'Công nghệ Phần mềm', 15, 1, 1, '$q_json')");
                    }

                    $result =$conn->query("SELECT * FROM exams ORDER BY id DESC LIMIT 5");
                    while($row = $result->fetch_assoc()) {$q_array = json_decode($row['questions'], true);$q_num = is_array($q_array) ? count($q_array) : 0;
                        $duration = isset($row['duration']) ?$row['duration'] : 15;
                        echo '<div class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">'.$row['title'].'</h6>
                                    <span class="badge bg-primary-subtle text-primary px-2 py-1 me-1"><i class="bi bi-book me-1"></i>'.$row['subject'].'</span>
                                    <span class="badge bg-success-subtle text-success px-2 py-1 me-1"><i class="bi bi-check2-circle me-1"></i>'.$q_num.' câu</span>
                                    <span class="badge bg-warning-subtle text-dark px-2 py-1"><i class="bi bi-clock me-1"></i>'.$duration.' phút</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="take_exam.php?id='.$row['id'].'" class="btn btn-primary btn-sm px-3 rounded-pill"><i class="bi bi-pencil-square me-1"></i>Vào làm bài</a>
                                    <a href="index.php?delete_id='.$row['id'].'" onclick="return confirm(\'Bạn có chắc chắn muốn xóa đề thi này không?\')" class="btn btn-outline-danger btn-sm px-3 rounded-pill"><i class="bi bi-trash me-1"></i>Xóa đề</a>
                                </div>
                              </div>';
                    }
                }
                $conn->close();
                ?>
            </div>
        </div>
    </div>
</body>
</html>
