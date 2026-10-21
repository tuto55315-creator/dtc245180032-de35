<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Làm bài thi trắc nghiệm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .timer-box { position: sticky; top: 1rem; z-index: 100; background: #ffffff; border-radius: 12px; padding: 0.8rem 1.2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎓 Hệ thống Thi Trắc Nghiệm (Đề 35)</a>
            <a href="quiz_bank.php" class="btn btn-outline-light btn-sm rounded-pill">⬅️ Quay lại Kho đề</a>
        </div>
    </nav>

    <div class="container my-4" style="max-width: 850px;">
        <?php
        session_start();
        $servername = "mysql_db";
        $username = "quiz_user";
        $password = "SecureQuizPassword456!";
        $dbname = "quiz_db";
        $conn = new mysqli($servername, $username, $password, $dbname);

        $exam_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $exam_query = $conn->query("SELECT * FROM exams WHERE id = $exam_id");

        if (!$exam_query || $exam_query->num_rows == 0) {
            echo "<div class='alert alert-danger text-center'><h4>⚠️ Không tìm thấy đề thi!</h4><a href='quiz_bank.php' class='btn btn-primary mt-2 rounded-pill'>Quay lại Kho đề thi</a></div>";
            exit;
        }

        $exam = $exam_query->fetch_assoc();
        $duration = isset($exam['duration']) ? intval($exam['duration']) : 15;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $student_name = $conn->real_escape_string($_POST['student_name']);
            $score = 0;
            
            // Lấy lại danh sách câu hỏi đã xáo trộn từ Session
            $questions = $_SESSION['current_exam_questions_'.$exam_id];
            $total = count($questions);
            $failed_questions = array();

            for ($i = 0; $i < $total; $i++) {
                $user_ans = isset($_POST['q_' . $i]) ? intval($_POST['q_' . $i]) : -1;
                $correct_ans = intval($questions[$i]['correct_index']);
                
                if ($user_ans === $correct_ans) {
                    $score++;
                } else {
                    $failed_questions[] = array(
                        "q" => $questions[$i]['q'],
                        "options" => $questions[$i]['options'],
                        "correct_index" => $correct_ans,
                        "user_ans" => $user_ans
                    );
                }
            }

            $final_score = ($total > 0) ? ($score / $total) * 10 : 0;
            $conn->query("INSERT INTO results (exam_id, student_name, score, total) VALUES ($exam_id, '$student_name', $final_score, 10)");
            
            // Lưu câu sai vào session để làm lại
            $_SESSION['failed_questions_'.$exam_id] = $failed_questions;
            $_SESSION['student_name_'.$exam_id] = $student_name;
            ?>

            <div class="card shadow p-4 text-center bg-white rounded-4">
                <h2 class="text-success fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Nộp Bài & Chấm Điểm Thành Công!</h2>
                <hr>
                <h4>Thí sinh: <span class="text-primary fw-bold"><?php echo htmlspecialchars($student_name); ?></span></h4>
                <p class="fs-5">Trả lời đúng: <span class="badge bg-success fs-6"><?php echo $score; ?> / <?php echo $total; ?> câu</span></p>
                <h1 class="text-danger display-3 fw-bold my-3"><?php echo number_format($final_score, 1); ?> / 10</h1>
                
                <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
                    <a href="quiz_bank.php" class="btn btn-dark rounded-pill px-4"><i class="bi bi-folder2-open me-1"></i>Kho đề thi</a>
                    <a href="take_exam.php?id=<?php echo $exam_id; ?>" class="btn btn-outline-primary rounded-pill px-4"><i class="bi bi-arrow-repeat me-1"></i>Thi lại bài này</a>
                    <?php if (count($failed_questions) > 0): ?>
                        <a href="retake_failed.php?id=<?php echo $exam_id; ?>" class="btn btn-warning rounded-pill px-4 fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Làm lại các câu sai (<?php echo count($failed_questions); ?> câu)</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php
        } else {
            // Chuẩn bị danh sách câu hỏi có đảo đề & đảo đáp án
            $raw_questions = json_decode($exam['questions'], true);
            $processed_questions = array();

            foreach ($raw_questions as $item) {
                $opts = array();
                foreach ($item['options'] as $idx => $text) {
                    $opts[] = array("text" => $text, "is_correct" => ($idx == $item['answer']));
                }
                
                // Đảo đáp án nếu cấu hình shuffle_opt = 1
                if (isset($exam['shuffle_opt']) && $exam['shuffle_opt'] == 1) {
                    shuffle($opts);
                }

                $new_options = array();
                $correct_index = 0;
                foreach ($opts as $o_idx => $o_data) {
                    $new_options[] = $o_data['text'];
                    if ($o_data['is_correct']) {
                        $correct_index = $o_idx;
                    }
                }

                $processed_questions[] = array(
                    "q" => $item['q'],
                    "options" => $new_options,
                    "correct_index" => $correct_index
                );
            }

            // Đảo câu hỏi nếu cấu hình shuffle_q = 1
            if (isset($exam['shuffle_q']) && $exam['shuffle_q'] == 1) {
                shuffle($processed_questions);
            }

            $_SESSION['current_exam_questions_'.$exam_id] = $processed_questions;
        ?>

        <div class="d-flex justify-content-between align-items-center timer-box mb-4">
            <div>
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i><?php echo htmlspecialchars($exam['title']); ?></h5>
                <small class="text-muted">Môn thi: <?php echo htmlspecialchars($exam['subject']); ?></small>
            </div>
            <div class="text-end">
                <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill"><i class="bi bi-stopwatch me-1"></i><span id="timer">15:00</span></span>
            </div>
        </div>

        <div class="card shadow rounded-4 border-0 p-4 bg-white">
            <form id="quiz-form" method="POST">
                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <label class="form-label fw-bold text-danger"><i class="bi bi-person-fill me-1"></i>Họ và tên thí sinh (*):</label>
                    <input type="text" name="student_name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn A">
                </div>
                <hr>

                <?php
                for ($idx = 0; $idx < count($processed_questions); $idx++) {
                    $item = $processed_questions[$idx];
                    ?>
                    <div class="mb-4 p-3 border rounded-3 bg-white shadow-sm">
                        <p class="fw-bold text-primary mb-2">Câu <?php echo ($idx + 1); ?>: <?php echo htmlspecialchars($item['q']); ?></p>
                        <?php
                        $opts = $item['options'];
                        for ($o = 0; $o < count($opts); $o++) {
                            $char = chr(65 + $o);
                            ?>
                            <div class="form-check my-2">
                                <input class="form-check-input" type="radio" name="q_<?php echo $idx; ?>" id="q_<?php echo $idx . '_' . $o; ?>" value="<?php echo $o; ?>" required>
                                <label class="form-check-label" for="q_<?php echo $idx . '_' . $o; ?>">
                                    <b><?php echo $char; ?>.</b> <?php echo htmlspecialchars($opts[$o]); ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>

                <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow"><i class="bi bi-send-fill me-2"></i>Nộp Bài & Chấm Điểm Tự Động</button>
            </form>
        </div>

        <script>
        // JS Đếm ngược thời gian tự động nộp bài khi hết giờ
        let timeLeft = <?php echo $duration; ?> * 60;
        let timerDisplay = document.getElementById('timer');

        let countdown = setInterval(function() {
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            if (seconds < 10) seconds = '0' + seconds;
            if (minutes < 10) minutes = '0' + minutes;
            timerDisplay.textContent = minutes + ':' + seconds;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                alert('Đã hết thời gian làm bài! Hệ thống sẽ tự động nộp bài.');
                document.getElementById('quiz-form').submit();
            }
            timeLeft--;
        }, 1000);
        </script>

        <?php 
        } 
        $conn->close(); 
        ?>
    </div>
</body>
</html>
