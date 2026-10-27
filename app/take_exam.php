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

$exam_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin đề thi (bao gồm thời gian làm bài)
$exam_stmt = $conn->prepare("SELECT title, subject, duration_minutes FROM exams WHERE id = ?");
$exam_stmt->bind_param("i", $exam_id);
$exam_stmt->execute();
$exam = $exam_stmt->get_result()->fetch_assoc();

if (!$exam) {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'><h2>❌ Không tìm thấy đề thi!</h2><a href='quiz_bank.php'>Quay lại Kho Đề Thi</a></div>");
}

$duration_minutes = max(1, (int)$exam['duration_minutes']); // Mặc định ít nhất 1 phút nếu lỗi

// Lấy danh sách câu hỏi
$q_stmt = $conn->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE exam_id = ?");
$q_stmt->bind_param("i", $exam_id);
$q_stmt->execute();
$questions = $q_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Làm bài thi - <?php echo htmlspecialchars($exam['title']); ?></title>
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
        .exam-header { background: #fff; padding: 20px 30px; border-radius: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 20px; z-index: 99; border-left: 6px solid var(--azota-primary); }
        .timer-box { font-size: 1.25rem; font-weight: bold; color: #dc3545; background: #fff5f5; padding: 8px 16px; border-radius: 10px; border: 1px solid #fed7d7; display: flex; align-items: center; gap: 8px; }
        .question-card { background: #fff; border-radius: 14px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); }
        .option-label { display: block; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s; font-weight: 500; }
        .option-label:hover { background: #f8fafc; border-color: var(--azota-primary); }
        .form-check-input:checked + .option-label { background: #eff6ff; border-color: var(--azota-primary); color: var(--azota-primary); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><i class="fa-solid fa-shapes"></i></div>
        <a href="index.php" title="Trang chủ"><i class="fa-solid fa-house"></i></a>
        <a href="quiz_bank.php" title="Kho đề thi"><i class="fa-solid fa-folder-open"></i></a>
        <a href="logout.php" title="Đăng xuất" class="mt-auto mb-4 text-danger"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>

    <div class="main-content">
        <!-- Header đề thi & Đồng hồ đếm ngược -->
        <div class="exam-header">
            <div>
                <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($exam['title']); ?></h4>
                <p class="text-secondary mb-0 small"><i class="fa-solid fa-book me-1"></i> Môn học: <strong><?php echo htmlspecialchars($exam['subject']); ?></strong></p>
            </div>
            <div class="timer-box shadow-sm">
                <i class="fa-solid fa-clock"></i>
                <span id="timer">--:--</span>
            </div>
        </div>

        <form action="submit_exam.php" method="POST" id="examForm">
            <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">

            <?php 
            $index = 1;
            if ($questions && $questions->num_rows > 0):
                while ($q = $questions->fetch_assoc()): 
                    $qid = $q['id'];
            ?>
                <div class="question-card">
                    <h5 class="fw-bold text-primary mb-3">Câu <?php echo $index++; ?>:</h5>
                    <p class="fs-6 fw-semibold text-dark mb-3"><?php echo nl2br(htmlspecialchars($q['question_text'])); ?></p>
                    
                    <div class="options-list">
                        <?php foreach (['A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']] as $key => $val): if(!empty(trim($val))): ?>
                            <div class="position-relative">
                                <input class="form-check-input position-absolute top-50 start-0 translate-middle-y ms-3 d-none" type="radio" name="q_<?php echo $qid; ?>" id="q<?php echo $qid; ?>_<?php echo $key; ?>" value="<?php echo $key; ?>">
                                <label class="option-label ps-5" for="q<?php echo $qid; ?>_<?php echo $key; ?>">
                                    <strong class="me-2 text-primary"><?php echo $key; ?>.</strong> <?php echo htmlspecialchars($val); ?>
                                </label>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            <?php 
                endwhile; 
            else:
            ?>
                <div class="alert alert-warning text-center py-4 rounded-4 shadow-sm">Đề thi này hiện chưa có câu hỏi nào.</div>
            <?php endif; ?>

            <div class="text-center mt-4 mb-5">
                <button type="submit" class="btn btn-success py-3 px-5 fw-bold fs-5 rounded-4 shadow">
                    <i class="fa-solid fa-paper-plane me-2"></i> Nộp Bài Thi Ngay
                </button>
            </div>
        </form>
    </div>

<script>
// Thiết lập thời gian làm bài từ PHP (đổi sang giây)
let timeLeft = <?php echo $duration_minutes * 60; ?>;
const timerDisplay = document.getElementById('timer');
const examForm = document.getElementById('examForm');

const countdown = setInterval(() => {
    let minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;

    // Định dạng hiển thị MM:SS
    timerDisplay.innerText = `${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

    if (timeLeft <= 0) {
        clearInterval(countdown);
        alert("⏰ Hết thời gian làm bài! Hệ thống sẽ tự động nộp bài của bạn.");
        examForm.submit(); // Tự động submit form khi hết giờ
    }
    timeLeft--;
}, 1000);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
$conn->close();
ob_end_flush(); 
?>
