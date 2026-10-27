<?php
ob_start();
session_start();
if (!isset($_SESSION['username'])) { 
    header("Location: login.php"); 
    exit(); 
}

$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
if ($conn->connect_error) { 
    die("Lỗi kết nối CSDL: " . $conn->connect_error); 
}

$exam_id = isset($_POST['exam_id']) ? (int)$_POST['exam_id'] : 0;
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;

// Lấy danh sách câu hỏi và đáp án đúng của đề thi
$q_res = $conn->query("SELECT id, correct_option FROM questions WHERE exam_id = $exam_id");
$total = $q_res ? $q_res->num_rows : 0;
$correct_count = 0;

if ($q_res) {
    while ($row = $q_res->fetch_assoc()) {
        $qid = $row['id'];
        // Kiểm tra xem học sinh chọn đáp án nào cho câu hỏi này
        if (isset($_POST["q_$qid"]) && $_POST["q_$qid"] === $row['correct_option']) {
            $correct_count++;
        }
    }
}

// Tính điểm hệ 10
$score = ($total > 0) ? ($correct_count / $total) * 10 : 0;

// Lưu kết quả vào cơ sở dữ liệu
$stmt = $conn->prepare("INSERT INTO results (exam_id, user_id, score) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("iid", $exam_id, $user_id, $score);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết Quả Thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
<div class="card p-5 text-center shadow-sm border-0 rounded-4" style="max-width: 500px; width: 100%;">
    <div class="mb-3 text-success">
        <i class="fa-solid fa-circle-check fa-4x"></i>
    </div>
    <h2 class="text-success mb-3 fw-bold">Nộp Bài Thành Công!</h2>
    <p class="fs-5 text-secondary mb-1">Số câu trả lời đúng:</p>
    <h3 class="fw-bold text-dark mb-3"><?php echo $correct_count; ?> / <?php echo $total; ?> câu</h3>
    
    <p class="fs-5 text-secondary mb-1">Điểm số của bạn:</p>
    <h1 class="display-4 fw-bold text-primary mb-4"><?php echo round($score, 2); ?> <span class="fs-4 text-muted">điểm</span></h1>
    
    <a href="quiz_bank.php" class="btn btn-primary py-3 fw-bold rounded-4 shadow-sm"><i class="fa-solid fa-arrow-left me-2"></i> Quay lại Kho Đề Thi</a>
</div>
</body>
</html>
<?php 
$conn->close();
ob_end_flush(); 
?>
