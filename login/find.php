<?php
// PHPMailer 파일 포함
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB 연결
$config = require '../config.php';
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

$message = ""; // 메시지 저장 변수
$userId = ""; // 아이디
$displayStyle = "none"; // 아이디 출력 기본 숨김

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['send_email'])) {
        // 사용자가 입력한 이메일
        $email = trim($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "올바른 이메일 형식을 입력해 주세요.";
        } else {
            $stmt = $con->prepare("SELECT id FROM members WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $userId = $row['id'];

                // 인증번호 생성
                $verificationCode = (string)mt_rand(100000, 999999);
                $_SESSION['verification_code'] = $verificationCode;
                $_SESSION['verification_email'] = $email; // 이메일 저장

                // 이메일 전송
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'tlaals7241@gmail.com'; // Gmail 주소
                    $mail->Password = '';   // 요청된 비밀번호 유지
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('tlaals7241@gmail.com', 'Admin');
                    $mail->addAddress($email);
                    $mail->Subject = 'ID/password find authentication number';
                    $mail->Body = "Hello, the authentication number is as follows: $verificationCode";

                    $mail->send();
                    $message = "인증번호가 이메일로 전송되었습니다.";
                } catch (Exception $e) {
                    $message = "메일 전송 실패: {$mail->ErrorInfo}";
                }
            } else {
                $message = "해당 이메일로 등록된 사용자를 찾을 수 없습니다.";
            }
            $stmt->close();
        }
    }

    if (isset($_POST['verify_code'])) {
        // 인증번호 확인
        $inputCode = trim($_POST['input_code']);
        $sessionCode = $_SESSION['verification_code'] ?? null;
        $verificationEmail = $_SESSION['verification_email'] ?? null;

        if ($sessionCode && $sessionCode === $inputCode) {
            $stmt = $con->prepare("SELECT id FROM members WHERE email = ?");
            $stmt->bind_param("s", $verificationEmail);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $userId = $row['id'];
                $displayStyle = "block";
                $_SESSION['verification_code'] = null; // 인증번호 제거
                $_SESSION['verification_email'] = null;
            } else {
                $message = "사용자 정보를 찾을 수 없습니다.";
            }
            $stmt->close();
        } else {
            $message = "인증번호가 일치하지 않습니다.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>아이디 찾기</title>
    <link rel="stylesheet" href="find.css">
    <style>
        .user-info {
            display: <?= $displayStyle ?>; /* 기본값은 숨김 */
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="find-container">
        <h1>아이디 찾기</h1>
        <div class="message">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <!-- 이메일 입력 -->
        <form method="post" action="find.php" class="find-form">
            <label for="email">이메일 입력</label>
            <input type="email" id="email" name="email" required>
            <button type="submit" name="send_email" class="btn">이메일로 인증번호 보내기</button>
        </form>

        <!-- 인증번호 입력 -->
        <form method="post" action="find.php" class="find-form">
            <label for="input_code">인증번호 입력</label>
            <input type="text" id="input_code" name="input_code" required>
            <button type="submit" name="verify_code" class="btn">인증하기</button>
        </form>

        <!-- 인증 성공 시 출력 -->
        <div class="user-info">
            <p><strong>아이디:</strong> <?= htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>
</body>
</html>
