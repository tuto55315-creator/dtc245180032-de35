<?php
ob_start();
session_start();
$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
if ($conn->connect_error) { die("Lỗi kết nối CSDL"); }

$exam_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Lấy thông tin đề thi
$exam_result = $conn->query("SELECT * FROM exams WHERE id = $exam_id");
$exam = $exam_result->fetch_assoc();

if (!$exam) {
    die("Không tìm thấy đề thi!");
}

// Lấy danh sách câu hỏi
$questions_result = $conn->query("SELECT * FROM questions WHERE exam_id = $exam_id");
$questions = [];
while ($row = $questions_result->fetch_assoc()) {
    $questions[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Làm Bài Thi - <?php echo htmlspecialchars($exam['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card shadow-sm p-4 mb-4">
        <h2>📝 <?php echo htmlspecialchars($exam['title']); ?></h2>
        <p class="text-muted mb-1">Môn học: <strong><?php echo htmlspecialchars($exam['subject']); ?></strong> | Thời gian: <strong><?php echo $exam['duration_minutes']; ?> phút</strong></p>
    </div>

    <form action="submit_exam.php" method="POST">
        <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
        
        <?php foreach ($questions as $index => $q): ?>
            <div class="card shadow-sm mb-3 p-3">
                <p class="fw-bold">Câu <?php echo ($index + 1); ?>: <?php echo htmlspecialchars($q['question_text']); ?></p>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="q_<?php echo $q['id']; ?>" value="A" id="q<?php echo $q['id']; ?>_a">
                    <label class="form-check-label" for="q<?php echo $q['id']; ?>_a">A. <?php echo htmlspecialchars($q['option_a']); ?></label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="q_<?php echo $q['id']; ?>" value="B" id="q<?php echo $q['id']; ?>_b">
                    <label class="form-check-label" for="q<?php echo $q['id']; ?>_b">B. <?php echo htmlspecialchars($q['option_b']); ?></label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="q_<?php echo $q['id']; ?>" value="C" id="q<?php echo $q['id']; ?>_c">
                    <label class="form-check-label" for="q<?php echo $q['id']; ?>_c">C. <?php echo htmlspecialchars($q['option_c']); ?></label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="q_<?php echo $q['id']; ?>" value="D" id="q<?php echo $q['id']; ?>_d">
                    <label class="form-check-label" for="q<?php echo $q['id']; ?>_d">D. <?php echo htmlspecialchars($q['option_d']); ?></label>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-success btn-lg w-100">Nộp Bài Thi</button>
    </form>
</div>
</body>
</html>
<?php 
// Đẩy nội dung bộ đệm ra trình duyệt kết thúc phiên
ob_end_flush(); 
?>
