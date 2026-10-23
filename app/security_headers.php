<?php
// Các tiêu đề bảo mật HTTP (Security Headers Hardening)
header("X-Frame-Options: SAMEORIGIN");                 // Chống clickjacking
header("X-Content-Type-Options: nosniff");              // Chống MIME-type sniffing
header("X-XSS-Protection: 1; mode=block");              // Kích hoạt bộ lọc XSS của trình duyệt
header("Referrer-Policy: strict-origin-when-cross-origin"); // Kiểm soát thông tin referrer
?>
