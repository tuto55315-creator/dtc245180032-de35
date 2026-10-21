<?php
ob_start();
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'><h2>⛔ Truy cập bị từ chối!</h2><p>Chỉ có tài khoản <b>Giáo viên</b> mới có quyền soạn đề thi.</p><a href='quiz_bank.php'>Quay lại Kho Đề Thi</a></div>");
}

$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $duration = (int)$_POST['duration'];
    $raw_text = $_POST['raw_text'];

    $stmt = $conn->prepare("INSERT INTO exams (title, subject, duration_minutes, shuffle_questions, shuffle_answers) VALUES (?, ?, ?, 1, 1)");
    $stmt->bind_param("ssi", $title, $subject, $duration);
    $stmt->execute();
    $exam_id = $stmt->insert_id;

    $questions = explode("Câu ", $raw_text);
    $count = 0;

    foreach ($questions as $q_text) {
        if (trim($q_text) == "") continue;
        
        preg_match('/^\d+[:.]?\s*(.*?)(?=A\.)/s', $q_text, $m_q);
        preg_match('/A\.\s*(.*?)(?=B\.)/s', $q_text, $m_a);
        preg_match('/B\.\s*(.*?)(?=C\.)/s', $q_text, $m_b);
        preg_match('/C\.\s*(.*?)(?=D\.|Đáp án:|$)/s', $q_text, $m_c);
        preg_match('/D\.\s*(.*?)(?=Đáp án:|$)/s', $q_text, $m_d);
        preg_match('/Đáp án:\s*([A-D])/i', $q_text, $m_ans);

        $q = isset($m_q[1]) ? trim($m_q[1]) : '';
        $opt_a = isset($m_a[1]) ? trim($m_a[1]) : '';
        $opt_b = isset($m_b[1]) ? trim($m_b[1]) : '';
        $opt_c = isset($m_c[1]) ? trim($m_c[1]) : '';
        $opt_d = isset($m_d[1]) ? trim($m_d[1]) : '';
        $correct = isset($m_ans[1]) ? strtoupper(trim($m_ans[1])) : 'A';

        if (!empty($q)) {
            $stmt_q = $conn->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_q->bind_param("issssss", $exam_id, $q, $opt_a, $opt_b, $opt_c, $opt_d, $correct);
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
    <title>Trình Soạn Đề Nhanh (Kiểu Azota)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>✏️ Trình Soạn Đề Thi Nhanh (Kiểu Azota) - Giáo viên</h2>
        <a href="quiz_bank.php" class="btn btn-secondary">Xem Kho Đề Thi</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="row g-3 mb-3">
            <div class="col-md-5">
                <label class="form-label font-weight-bold">Tên bài thi / Đề thi:</label>
                <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Kiểm tra giữa kỳ">
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Môn học:</label>
                <input type="text" name="subject" class="form-control" required placeholder="Ví dụ: Lập trình Web">
            </div>
            <div class="col-md-3">
                <label class="form-label font-weight-bold">Thời gian (phút):</label>
                <input type="number" name="duration" class="form-control" value="15" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold">Copy & Paste danh sách câu hỏi vào đây:</label>
            <textarea name="raw_text" class="form-control" rows="12" placeholder="Mẫu định dạng soạn thảo:
Câu 1: Câu hỏi số 1?
A. Đáp án A
B. Đáp án B
C. Đáp án C
D. Đáp án D
Đáp án: B"></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">🚀 Tự Động Trích Xuất & Lưu Đề Thi</button>
    </form>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
