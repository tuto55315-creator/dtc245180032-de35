<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Đề Thi Trắc Nghiệm Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .card-custom { border-radius: 16px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    </style>
</head>
<body>
    <div class="container my-5" style="max-width: 850px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0 text-primary"><i class="bi bi-journal-plus me-2"></i>Tạo Đề Thi Trắc Nghiệm Mới</h4>
            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-3"><i class="bi bi-arrow-left me-1"></i>Trang chủ</a>
        </div>

        <div class="card card-custom p-4 bg-white">
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $servername = "mysql_db";
                $username = "quiz_user";
                $password = "SecureQuizPassword456!";
                $dbname = "quiz_db";
                $conn = new mysqli($servername, $username, $password, $dbname);

                $title = $conn->real_escape_string($_POST['title']);
                $subject = $conn->real_escape_string($_POST['subject']);
                $duration = intval($_POST['duration']);
                $shuffle_q = isset($_POST['shuffle_q']) ? 1 : 0;
                $shuffle_opt = isset($_POST['shuffle_opt']) ? 1 : 0;
                
                $questions = array();
                for ($i = 0; $i < count($_POST['q_text']); $i++) {
                    $questions[] = array(
                        "q" => $_POST['q_text'][$i],
                        "options" => array(
                            $_POST['opt0'][$i],
                            $_POST['opt1'][$i],
                            $_POST['opt2'][$i],
                            $_POST['opt3'][$i]
                        ),
                        "answer" => intval($_POST['ans'][$i])
                    );
                }
                $json_questions = json_encode($questions, JSON_UNESCAPED_UNICODE);
                $conn->query("INSERT INTO exams (title, subject, duration, shuffle_q, shuffle_opt, questions) VALUES ('$title', '$subject', $duration, $shuffle_q, $shuffle_opt, '$json_questions')");
                echo '<div class="alert alert-success rounded-3 mb-4"><i class="bi bi-check-circle-fill me-2"></i>Tạo đề thi thành công! <a href="quiz_bank.php" class="fw-bold text-success">Xem ngay trong Kho đề thi</a></div>';
                $conn->close();
            }
            ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu đề đề thi:</label>
                    <input type="text" name="title" class="form-control form-control-lg rounded-3" required placeholder="Ví dụ: Kiểm tra 1 tiết Triển khai và Quản trị Hệ thống">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên Môn học / Lớp:</label>
                        <input type="text" name="subject" class="form-control rounded-3" required placeholder="Ví dụ: Công nghệ thông tin">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Thời gian làm bài (Phút):</label>
                        <input type="number" name="duration" class="form-control rounded-3" value="15" min="1" max="180" required>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-4 border">
                    <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-sliders me-2 text-primary"></i>Cấu hình đảo đề thi:</label>
                    <div class="d-flex gap-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="shuffle_q" value="1" id="sq" checked>
                            <label class="form-check-label fw-semibold" for="sq">Xáo trộn câu hỏi</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="shuffle_opt" value="1" id="so" checked>
                            <label class="form-check-label fw-semibold" for="so">Xáo trộn các đáp án A,B,C,D</label>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-list-task me-2"></i>Danh sách câu hỏi</h5>
                <div id="question-container">
                    <div class="card p-3 mb-3 border rounded-3 bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nội dung câu hỏi 1:</label>
                            <input type="text" name="q_text[]" class="form-control bg-white" required placeholder="Nhập câu hỏi...">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6"><input type="text" name="opt0[]" class="form-control bg-white" placeholder="Đáp án A" required></div>
                            <div class="col-md-6"><input type="text" name="opt1[]" class="form-control bg-white" placeholder="Đáp án B" required></div>
                            <div class="col-md-6"><input type="text" name="opt2[]" class="form-control bg-white" placeholder="Đáp án C" required></div>
                            <div class="col-md-6"><input type="text" name="opt3[]" class="form-control bg-white" placeholder="Đáp án D" required></div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-bold text-success">Chọn đáp án đúng:</label>
                            <select name="ans[]" class="form-select bg-white">
                                <option value="0">Đáp án A</option>
                                <option value="1">Đáp án B</option>
                                <option value="2">Đáp án C</option>
                                <option value="3">Đáp án D</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-primary rounded-pill mb-4" onclick="addQuestion()"><i class="bi bi-plus-lg me-1"></i>Thêm câu hỏi tiếp theo</button>
                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm"><i class="bi bi-cloud-upload me-2"></i>Xuất bản Đề Thi Mới</button>
            </form>
        </div>
    </div>

    <script>
    let qCount = 1;
    function addQuestion() {
        qCount++;
        let container = document.getElementById('question-container');
        let html = `<div class="card p-3 mb-3 border rounded-3 bg-light">
            <div class="mb-3"><label class="form-label fw-semibold">Nội dung câu hỏi ${qCount}:</label><input type="text" name="q_text[]" class="form-control bg-white" required placeholder="Nhập câu hỏi..."></div>
            <div class="row g-2">
                <div class="col-md-6"><input type="text" name="opt0[]" class="form-control bg-white" placeholder="Đáp án A" required></div>
                <div class="col-md-6"><input type="text" name="opt1[]" class="form-control bg-white" placeholder="Đáp án B" required></div>
                <div class="col-md-6"><input type="text" name="opt2[]" class="form-control bg-white" placeholder="Đáp án C" required></div>
                <div class="col-md-6"><input type="text" name="opt3[]" class="form-control bg-white" placeholder="Đáp án D" required></div>
            </div>
            <div class="mt-3"><label class="form-label fw-bold text-success">Chọn đáp án đúng:</label>
                <select name="ans[]" class="form-select bg-white">
                    <option value="0">Đáp án A</option><option value="1">Đáp án B</option><option value="2">Đáp án C</option><option value="3">Đáp án D</option>
                </select>
            </div></div>`;
        container.insertAdjacentHTML('beforeend', html);
    }
    </script>
</body>
</html>
