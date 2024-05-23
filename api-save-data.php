<?php
// تنظیم هدرهای پاسخ
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // اجازه دسترسی به همه منابع (برای توسعه محلی مفید است)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// تنظیمات دیتابیس
$servername = "localhost";
$username = "root"; // نام کاربری دیتابیس شما
$password = ""; // رمز عبور دیتابیس شما
$dbname = "profile-user";

// اتصال به دیتابیس
$conn = new mysqli($servername, $username, $password, $dbname);

// بررسی اتصال به دیتابیس
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// بررسی نوع درخواست
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    // دریافت ورودی JSON از بدنه درخواست
    $input = json_decode(file_get_contents('php://input'), true);

    // بررسی اینکه ورودی‌ها خالی نباشند
    if (isset($input['name']) && isset($input['email']) && isset($input['subject']) && isset($input['message'])) {
        $name = $input['name'];
        $email = $input['email'];
        $subject = $input['subject'];
        $message = $input['message'];

        // آماده‌سازی و اجرای کوئری
        $stmt = $conn->prepare("INSERT INTO comments (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            // پاسخ موفقیت
            $response = array('status' => 'success', 'message' => 'نظر شما ثبت شد با تشکر!');
        } else {
            // پاسخ خطا
            $response = array('status' => 'error', 'message' => 'اطلاعات مورد نظر ثبت نشد');
        }

        $stmt->close();
    } else {
        // پاسخ خطا
        $response = array('status' => 'error', 'message' => 'ورودی نامعتبر می باشد');
    }

    // ارسال پاسخ به صورت JSON
    echo json_encode($response);
} else {
    // اگر درخواست POST نبود، خطا برگردانید
    $response = array('status' => 'error', 'message' => 'Only POST method is allowed');
    echo json_encode($response);
}

$conn->close();
?>