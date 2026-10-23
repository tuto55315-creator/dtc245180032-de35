<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }
$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');

$exam_id = (int)$_POST['exam_id'];
$user_id = $_SESSION['user_id'];
$q_res = $conn->query("SELECT id, correct_option FROM questions WHERE exam_id = $exam_id");
$total = $q_res->num_rows;
$correct_count = 0;

while ($row = $q_res->fetch_assoc()) {
    $qid = $row['id'];
    if (isset($_POST["q_$qid"]) && $_POST["q_$qid"] === $row['correct_option']) {
        $correct_count++;
    }
}
$score = ($total > 0) ? ($correct_count / $total) * 10 : 0;
$stmt = $conn->prepare("INSERT INTO results (exam_id, user_id, score) VALUES (?, ?, ?)");
$stmt->bind_param("iid", $exam_id, $user_id, $score);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Kết Quả Thi</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
<div class="card p-5 text-center shadow-sm border-0 rounded-4" style="max-width: 500px; width: 100%;">
    <h2 class="text-success mb-3">🎉 Nộp Bài Thành Công!</h2>
    <p class="fs-5">Số câu đúng: <strong><?php echo $correct_count; ?> / <?php echo $total; ?></strong></p>
    <h1 class="display-4 fw-bold text-primary mb-4"><?php echo round($score, 2); ?> điểm</h1>
    <a href="quiz_bank.php" class="btn btn-primary py-2 fw-bold">Quay lại Kho Đề Thi</a>
</div>
</body>
</html>
