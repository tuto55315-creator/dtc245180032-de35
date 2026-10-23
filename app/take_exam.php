<?php
ob_start();
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }
$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
if ($conn->connect_error) { die("Lỗi kết nối CSDL"); }

$exam_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$exam_res = $conn->query("SELECT * FROM exams WHERE id = $exam_id");
$exam = $exam_res->fetch_assoc();
if (!$exam) { die("Không tìm thấy đề thi!"); }

$q_res = $conn->query("SELECT * FROM questions WHERE exam_id = $exam_id");
$questions = [];
while ($row = $q_res->fetch_assoc()) { $questions[] = $row; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Làm Bài Thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container" style="max-width: 800px;">
    <div class="card shadow-sm p-4 mb-4 border-0 rounded-4 bg-primary text-white">
        <h2>📝 <?php echo htmlspecialchars($exam['title']); ?></h2>
        <p class="mb-0">Môn học: <strong><?php echo htmlspecialchars($exam['subject']); ?></strong> | Thời gian: <strong><?php echo $exam['duration_minutes']; ?> phút</strong></p>
    </div>
    <form action="submit_exam.php" method="POST">
        <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
        <?php foreach ($questions as $i => $q): ?>
            <div class="card shadow-sm p-4 mb-3 border-0 rounded-4">
                <p class="fw-bold">Câu <?php echo ($i+1); ?>: <?php echo htmlspecialchars($q['question_text']); ?></p>
                <?php foreach(['A'=>$q['option_a'], 'B'=>$q['option_b'], 'C'=>$q['option_c'], 'D'=>$q['option_d']] as $k=>$v): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="q_<?php echo $q['id']; ?>" value="<?php echo $k; ?>" id="q<?php echo $q['id']; ?>_<?php echo $k; ?>" required>
                        <label class="form-check-label" for="q<?php echo $q['id']; ?>_<?php echo $k; ?>"><strong><?php echo $k; ?>.</strong> <?php echo htmlspecialchars($v); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold rounded-4 shadow">Nộp Bài Thi</button>
    </form>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
