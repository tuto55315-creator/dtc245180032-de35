<?php
ob_start();
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'><h2>⛔ Truy cập bị từ chối!</h2><p>Chỉ có tài khoản <b>Giáo viên</b> mới có quyền soạn đề thi.</p><a href='quiz_bank.php'>Quay lại Kho Đề Thi</a></div>");
}

$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
if ($conn->connect_error) { die("Lỗi kết nối CSDL"); }

// Đảm bảo bảng exams và questions chuẩn xác
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

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $duration = (int)$_POST['duration'];
    $questions = $_POST['questions'] ?? [];

    $stmt = $conn->prepare("INSERT INTO exams (title, subject, duration_minutes) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $subject, $duration);
    $stmt->execute();
    $exam_id = $stmt->insert_id;

    $count = 0;
    foreach ($questions as $q) {
        $q_text = trim($q['text'] ?? '');
        $opt_a = trim($q['a'] ?? '');
        $opt_b = trim($q['b'] ?? '');
        $opt_c = trim($q['c'] ?? '');
        $opt_d = trim($q['d'] ?? '');
        $correct = $q['correct'] ?? 'A';

        if (!empty($q_text)) {
            $stmt_q = $conn->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_q->bind_param("issssss", $exam_id, $q_text, $opt_a, $opt_b, $opt_c, $opt_d, $correct);
            $stmt_q->execute();
            $count++;
        }
    }
    $msg = "Đã tạo thành công đề thi với $count câu hỏi!";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Soạn Đề Thi Từng Câu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --azota-primary: #1850df; --azota-bg: #f4f6f8; }
        body { background-color: var(--azota-bg); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 80px; background: #0f172a; min-height: 100vh; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; align-items: center; padding-top: 20px; z-index: 100; }
        .sidebar .brand { color: #fff; font-size: 24px; margin-bottom: 30px; }
        .sidebar a { color: #94a3b8; font-size: 20px; padding: 12px; margin-bottom: 10px; border-radius: 12px; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: var(--azota-primary); color: #fff; }
        .main-content { margin-left: 80px; padding: 30px; max-width: 950px; margin-right: auto; }
        .top-navbar { background: #fff; padding: 15px 30px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .question-box { background: #fff; border-radius: 14px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); border-left: 5px solid var(--azota-primary); position: relative; }
        .btn-delete-q { position: absolute; top: 20px; right: 20px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><i class="fa-solid fa-shapes"></i></div>
        <a href="index.php" title="Trang chủ"><i class="fa-solid fa-house"></i></a>
        <a href="quiz_bank.php" title="Kho đề thi"><i class="fa-solid fa-folder-open"></i></a>
        <a href="create_quiz_fast.php" class="active" title="Soạn đề nhanh"><i class="fa-solid fa-file-pen"></i></a>
        <a href="logout.php" title="Đăng xuất" class="mt-auto mb-4 text-danger"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <h4 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-file-pen text-primary me-2"></i> Soạn Đề Thi Trắc Nghiệm Từng Câu</h4>
            <a href="quiz_bank.php" class="btn btn-secondary btn-sm shadow-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kho Đề Thi</a>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-success shadow-sm"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="POST" id="quizForm">
            <!-- Thông tin chung đề thi -->
            <div class="card p-4 shadow-sm border-0 rounded-4 mb-4">
                <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-info-circle me-2"></i> Thông tin chung bài thi</h5>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Tên đề thi:</label>
                        <input type="text" name="title" class="form-control py-2" required placeholder="Ví dụ: Kiểm tra giữa kỳ môn Công nghệ PM">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Môn học:</label>
                        <input type="text" name="subject" class="form-control py-2" required placeholder="Ví dụ: Công nghệ Phần mềm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Thời gian (phút):</label>
                        <input type="number" name="duration" class="form-control py-2" value="15" required>
                    </div>
                </div>
            </div>

            <!-- Danh sách các câu hỏi -->
            <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-list-check me-2"></i> Danh sách câu hỏi</h5>
            <div id="questions-container"></div>

            <!-- Nút thêm câu hỏi & Submit -->
            <div class="d-flex gap-3 mb-5">
                <button type="button" class="btn btn-outline-primary py-3 px-4 fw-bold rounded-4 shadow-sm flex-grow-1" onclick="addQuestionBox()">
                    <i class="fa-solid fa-plus-circle me-2"></i> + Thêm Câu Hỏi Mới
                </button>
                <button type="submit" class="btn btn-success py-3 px-5 fw-bold rounded-4 shadow flex-grow-1">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Lưu Đề Thi Vào Hệ Thống
                </button>
            </div>
        </form>
    </div>

<script>
let questionIndex = 0;

function addQuestionBox() {
    questionIndex++;
    const container = document.getElementById('questions-container');
    
    const box = document.createElement('div');
    box.className = 'question-box';
    box.id = `q-box-${questionIndex}`;
    
    box.innerHTML = `
        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-q" onclick="removeQuestionBox(${questionIndex})" title="Xóa câu này">
            <i class="fa-solid fa-trash"></i>
        </button>
        <h5 class="fw-bold text-primary mb-3">Câu hỏi số <span class="q-num">${questionIndex}</span></h5>
        
        <div class="mb-3">
            <label class="form-label fw-bold small text-secondary">Nội dung câu hỏi:</label>
            <textarea name="questions[${questionIndex}][text]" class="form-control" rows="2" required placeholder="Nhập nội dung câu hỏi..."></textarea>
        </div>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text fw-bold bg-light">A</span>
                    <input type="text" name="questions[${questionIndex}][a]" class="form-control" required placeholder="Đáp án A">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text fw-bold bg-light">B</span>
                    <input type="text" name="questions[${questionIndex}][b]" class="form-control" required placeholder="Đáp án B">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text fw-bold bg-light">C</span>
                    <input type="text" name="questions[${questionIndex}][c]" class="form-control" required placeholder="Đáp án C">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text fw-bold bg-light">D</span>
                    <input type="text" name="questions[${questionIndex}][d]" class="form-control" required placeholder="Đáp án D">
                </div>
            </div>
        </div>

        <div class="mt-3 w-25">
            <label class="form-label fw-bold small text-success">Đáp án đúng:</label>
            <select name="questions[${questionIndex}][correct]" class="form-select border-success text-success fw-bold">
                <option value="A">Đáp án A</option>
                <option value="B">Đáp án B</option>
                <option value="C">Đáp án C</option>
                <option value="D">Đáp án D</option>
            </select>
        </div>
    `;
    
    container.appendChild(box);
    updateQuestionNumbers();
}

function removeQuestionBox(id) {
    const box = document.getElementById(`q-box-${id}`);
    if (box) {
        box.remove();
        updateQuestionNumbers();
    }
}

function updateQuestionNumbers() {
    const boxes = document.querySelectorAll('.question-box');
    boxes.forEach((box, idx) => {
        const numSpan = box.querySelector('.q-num');
        if (numSpan) {
            numSpan.innerText = idx + 1;
        }
    });
}

window.onload = function() {
    addQuestionBox();
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>
