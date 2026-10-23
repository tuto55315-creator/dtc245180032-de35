<?php
ob_start();
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    die("Truy cập bị từ chối!");
}
$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $duration = (int)$_POST['duration'];
    $raw_text = $_POST['raw_text'];

    $stmt = $conn->prepare("INSERT INTO exams (title, subject, duration_minutes) VALUES (?, ?, ?)");
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
    <title>Soạn Đề Nhanh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
<div class="container" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>✏️ Soạn Đề Thi Nhanh</h2>
        <a href="quiz_bank.php" class="btn btn-secondary">Quay lại Kho Đề</a>
    </div>
    <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <form method="POST" class="card p-4 shadow-sm border-0 rounded-4">
        <div class="row g-3 mb-3">
            <div class="col-md-6"><label class="form-label fw-bold">Tên đề thi:</label><input type="text" name="title" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label fw-bold">Môn học:</label><input type="text" name="subject" class="form-control" required></div>
            <div class="col-md-2"><label class="form-label fw-bold">Thời gian (phút):</label><input type="number" name="duration" class="form-control" value="15" required></div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Nội dung câu hỏi:</label>
            <textarea name="raw_text" class="form-control" rows="10" placeholder="Câu 1: ...&#10;A. ...&#10;B. ...&#10;C. ...&#10;D. ...&#10;Đáp án: A"></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">🚀 Trích Xuất & Lưu Đề Thi</button>
    </form>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
