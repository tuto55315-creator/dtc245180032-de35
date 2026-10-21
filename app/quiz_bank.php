<?php
$conn = new mysqli('mysql_db', 'quiz_user', 'SecureQuizPassword456!', 'quiz_db');
if ($conn->connect_error) { die("Lỗi kết nối CSDL"); }

$msg = "";

// Xử lý tự động thêm đề cương mẫu khi người dùng bấm nút
if (isset($_GET['import_sample'])) {
    $type = $_GET['import_sample'];
    
    if ($type == 'toan') {
        $title = "Đề cương ôn tập Toán cơ bản (15 câu)";
        $subject = "Toán học";
        $duration = 20;
        $questions = [
            ["Đạo hàm của hàm số f(x) = x^2 là gì?", "2x", "x", "x^2", "2", "A"],
            ["Nghiệm của phương trình 2x - 4 = 0 là?", "x = 1", "x = 2", "x = -2", "x = 4", "B"],
            ["Tập xác định của hàm số y = ln(x) là?", "(0; +infinity)", "[0; +infinity)", "R", "(-infinity; 0)", "A"],
            ["Hệ số góc của đường thẳng y = 3x + 5 là?", "5", "-3", "3", "-5", "C"],
            ["Thể tích khối chóp có diện tích đáy S và chiều cao h tính bằng công thức nào?", "V = S.h", "V = 1/3 S.h", "V = 1/2 S.h", "V = 1/6 S.h", "B"],
            ["Trong không gian Oxyz, vectơ pháp tuyến của mặt phẳng x + 2y - 3z + 1 = 0 là?", "(1; 2; -3)", "(1; 2; 3)", "(-1; -2; 3)", "(2; -3; 1)", "A"],
            ["Giá trị của sin(30 độ) bằng bao nhiêu?", "1/2", "can(3)/2", "can(2)/2", "1", "A"],
            ["Tích phân từ 0 đến 1 của 2x dx bằng bao nhiêu?", "1", "2", "0", "1/2", "A"],
            ["Số phức z = 3 + 4i có phần thực bằng bao nhiêu?", "3", "4", "5", "4i", "A"],
            ["Đường tiệm cận ngang của đồ thị hàm số y = (2x+1)/(x-1) là đường thẳng nào?", "x = 1", "y = 2", "y = 1", "x = 2", "B"],
            ["Cho cấp số cộng có u1 = 2 và công sai d = 3. Số hạng thứ 5 là?", "11", "14", "12", "15", "A"],
            ["Diện tích mặt cầu bán kính R được tính theo công thức nào?", "4 * pi * R^2", "pi * R^2", "4/3 * pi * R^3", "2 * pi * R", "A"],
            ["Phương trình nào dưới đây là phương trình mặt cầu?", "x^2 + y^2 + z^2 - 2x = 0", "x^2 + y^2 = 4", "x + y + z = 1", "x^2 - y^2 = 1", "A"],
            ["Số các chỉnh hợp chập k của n phần tử tính bằng công thức nào?", "n! / (n-k)!", "n! / (k!(n-k)!)", "n^k", "k!", "A"],
            ["Họ nguyên hàm của hàm số f(x) = e^x là?", "e^x + C", "e^x", "1/x + C", "x * e^x + C", "A"]
        ];
    } elseif ($type == 'hoa') {
        $title = "Đề cương ôn tập Hóa học cơ bản (15 câu)";
        $subject = "Hóa học";
        $duration = 20;
        $questions = [
            ["Kim loại nào dẫn điện tốt nhất trong tất cả các kim loại?", "Cu", "Ag", "Al", "Fe", "B"],
            ["Số hiệu nguyên tử của nguyên tố Carbon (C) là?", "6", "8", "12", "14", "A"],
            ["Dung dịch làm quỳ tím hóa xanh là?", "HCl", "NaOH", "NaCl", "H2SO4", "B"],
            ["Kim loại nào tác dụng mạnh với nước ở nhiệt độ thường tạo dung dịch kiềm?", "Fe", "Cu", "Na", "Ag", "C"],
            ["Chất nào sau đây thuộc loại hiđrocacbon no?", "Metan (CH4)", "Etilen (C2H4)", "Axetilen (C2H2)", "Benzen (C6H6)", "A"],
            ["Khí gây hiệu ứng nhà kính chủ yếu do hoạt động công nghiệp và đời sống là?", "O2", "CO2", "N2", "H2", "B"],
            ["Oxit nào sau đây là oxit axit?", "CaO", "Na2O", "SO2", "CuO", "C"],
            ["Để khử chua cho đất nông nghiệp, người ta thường dùng chất nào?", "CaSO4", "Ca(OH)2", "NaCl", "HCl", "B"],
            ["Kim loại nào sau đây không tác dụng với dung dịch HCl loãng?", "Al", "Mg", "Cu", "Zn", "C"],
            ["Công thức hóa học của axit sunfuric đặc là?", "HCl", "HNO3", "H2SO4", "H3PO4", "C"],
            ["Phản ứng giữa axit và bazơ gọi là phản ứng gì?", "Phản ứng thế", "Phản ứng trung hòa", "Phản ứng phân hủy", "Phản ứng hóa hợp", "B"],
            ["Chất nào sau đây phản ứng với dung dịch AgNO3 trong amonia tạo kết tủa vàng nhạt?", "Metan", "Etilen", "Axetilen", "Etan", "C"],
            ["Nhóm chức đặc trưng của ancol là gì?", "-COOH", "-OH", "-CHO", "-NH2", "B"],
            ["Đường mía (saccarozơ) thuộc loại disaccarit gồm các gốc monosaccarit nào?", "Glucose và Fructose", "Chỉ gồm Glucose", "Glucose và Galactose", "Fructose và Galactose", "A"],
            ["Quặng bôxít là nguyên liệu chính dùng để sản xuất kim loại nào?", "Sắt", "Nhôm", "Đồng", "Kẽm", "B"]
        ];
    }

    if (isset($questions)) {
        $stmt_e = $conn->prepare("INSERT INTO exams (title, subject, duration_minutes, shuffle_questions, shuffle_answers) VALUES (?, ?, ?, 1, 1)");
        $stmt_e->bind_param("ssi", $title, $subject, $duration);
        $stmt_e->execute();
        $new_exam_id = $stmt_e->insert_id;

        foreach ($questions as $q) {
            $stmt_q = $conn->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_q->bind_param("issssss", $new_exam_id, $q[0], $q[1], $q[2], $q[3], $q[4], $q[5]);
            $stmt_q->execute();
        }
        $msg = "Đã nhập thành công bộ đề cương ôn tập môn $subject vào hệ thống!";
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT e.*, COUNT(r.id) AS total_attempts 
        FROM exams e 
        LEFT JOIN results r ON e.id = r.exam_id";

if (!empty($search)) {
    $search_clean = $conn->real_escape_string($search);
    $sql .= " WHERE e.title LIKE '%$search_clean%' OR e.subject LIKE '%$search_clean%'";
}

$sql .= " GROUP BY e.id ORDER BY e.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kho Đề Thi & Thống Kê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📚 Kho Đề Thi & Thống Kê Lượt Làm Bài</h2>
        <div>
            <a href="index.php" class="btn btn-secondary">Trang Chủ</a>
            <a href="create_quiz_fast.php" class="btn btn-success">+ Soạn Đề Nhanh (Azota)</a>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Khối chọn nhanh đề cương ôn tập có sẵn -->
    <div class="card shadow-sm mb-4 border-info">
        <div class="card-header bg-info text-dark fw-bold">
            💡 Thêm nhanh Đề cương ôn tập có sẵn (15 câu cơ bản)
        </div>
        <div class="card-body bg-white">
            <p class="text-muted mb-3">Bấm vào các nút dưới đây để hệ thống tự động thêm đề cương ôn tập chuẩn vào cơ sở dữ liệu:</p>
            <a href="quiz_bank.php?import_sample=toan" class="btn btn-outline-primary me-2 mb-2">➕ Thêm Đề Cương Ôn Tập Toán (15 câu)</a>
            <a href="quiz_bank.php?import_sample=hoa" class="btn btn-outline-warning mb-2">➕ Thêm Đề Cương Ôn Tập Hóa (15 câu)</a>
        </div>
    </div>

    <!-- Thanh tìm kiếm đề thi -->
    <form method="GET" action="quiz_bank.php" class="row g-3 mb-4">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm đề thi theo tên hoặc môn học..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tên Đề Thi</th>
                        <th>Môn Học</th>
                        <th>Thời Gian</th>
                        <th>📊 Số Lượt Làm Bài</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td class="text-start fw-bold"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['subject']); ?></span></td>
                            <td><?php echo $row['duration_minutes']; ?> phút</td>
                            <td><span class="badge bg-success fs-6"><?php echo $row['total_attempts']; ?> lượt</span></td>
                            <td>
                                <a href="take_exam.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Làm Bài</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="py-4 text-muted">Không tìm thấy đề thi phù hợp!</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
