<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Làm lại câu trả lời sai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>
</head>
<body class="bg-light">
    <div class="container my-5" style="max-width: 800px;">
        <?php
        session_start();
        $exam_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $failed_questions = isset($_SESSION['failed_questions_'.$exam_id]) ? $_SESSION['failed_questions_'.$exam_id] : array();
        $student_name = isset($_SESSION['student_name_'.$exam_id]) ? $_SESSION['student_name_'.$exam_id] : "Thí sinh";

        if (count($failed_questions) == 0) {
            echo "<div class='card p-4 text-center shadow rounded-4'><h4 class='text-success'>🎉 Bạn không có câu sai nào!</h4><a href='quiz_bank.php' class='btn btn-primary mt-2 rounded-pill'>Về Kho đề thi</a></div>";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $score = 0;
            $total = count($failed_questions);
            for ($i = 0; $i < $total; $i++) {
                $ans = isset($_POST['fq_' . $i]) ? intval($_POST['fq_' . $i]) : -1;
                if ($ans === intval($failed_questions[$i]['correct_index'])) {
                    $score++;
                }
            }
            ?>
            <div class="card shadow p-4 text-center bg-white rounded-4">
                <h3 class="text-success fw-bold"><i class="bi bi-award-fill me-2"></i>Hoàn Thành Chế Độ Ôn Luyện Câu Sai!</h3>
                <hr>
                <p class="fs-5">Số câu trả lời sửa đúng: <span class="badge bg-success fs-6"><?php echo $score; ?> / <?php echo $total; ?> câu</span></p>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <a href="quiz_bank.php" class="btn btn-dark rounded-pill px-4">Kho đề thi</a>
                    <a href="take_exam.php?id=<?php echo $exam_id; ?>" class="btn btn-primary rounded-pill px-4">Làm lại bài thi chuẩn</a>
                </div>
            </div>
            <?php
        } else {
        ?>
        <div class="card shadow rounded-4 border-0 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-danger m-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Ôn Luyện & Làm Lai Câu Sai</h4>
                <a href="quiz_bank.php" class="btn btn-outline-secondary btn-sm rounded-pill">Bỏ qua</a>
            </div>
            <p class="text-muted">Học viên: <b><?php echo htmlspecialchars($student_name); ?></b> | Danh sách <?php echo count($failed_questions); ?> câu đã làm sai lượt trước:</p>
            <hr>

            <form method="POST">
                <?php for ($idx = 0; $idx < count($failed_questions); $idx++): 
                    $item = $failed_questions[$idx];
                ?>
                    <div class="mb-4 p-3 border border-danger-subtle rounded-3 bg-light">
                        <p class="fw-bold text-dark mb-2">Câu <?php echo ($idx + 1); ?>: <?php echo htmlspecialchars($item['q']); ?></p>
                        <?php for ($o = 0; $o < count($item['options']); $o++): ?>
                            <div class="form-check my-2">
                                <input class="form-check-input" type="radio" name="fq_<?php echo $idx; ?>" id="fq_<?php echo $idx.'_'.$o; ?>" value="<?php echo $o; ?>" required>
                                <label class="form-check-label" for="fq_<?php echo $idx.'_'.$o; ?>">
                                    <b><?php echo chr(65 + $o); ?>.</b> <?php echo htmlspecialchars($item['options'][$o]); ?>
                                </label>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>
                <button type="submit" class="btn btn-danger btn-lg w-100 rounded-pill fw-bold shadow"><i class="bi bi-check-all me-2"></i>Kiểm Tra Đáp Án Ôn Luyện</button>
            </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>
