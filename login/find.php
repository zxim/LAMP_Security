<?php
include "../memberboard/session.php"; // 세션 처리

// PHPMailer 파일 포함
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 세션 시작 확인
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
$userPass = ""; // 비밀번호
$displayStyle = "none"; // 아이디/비밀번호 출력 기본 숨김

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['send_email'])) {
        // 이메일 전송 로직
        $userid = $_SESSION["userid"] ?? null;
        if (!$userid) {
            $message = "로그인이 필요합니다.";
        } else {
            $sql = "SELECT email FROM members WHERE id = '$userid'";
            $result = mysqli_query($con, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $userEmail = $row['email'];

                // 인증번호 생성
                $verificationCode = (string)mt_rand(100000, 999999);
                $_SESSION['verification_code'] = $verificationCode;

                // 이메일 전송
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = '';       // 구글 이메일
                    $mail->Password = '';       // 앱 비밀번호
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('', 'Admin');    //구글 이메일, 보내는 사람 이름
                    $mail->addAddress($userEmail, 'Client');    //받는 사람 이름
                    $mail->Subject = 'ID/password find authentication number';
                    $mail->Body = "Hello, the authentication number is as follows: $verificationCode";

                    $mail->send();
                    $message = "인증번호가 이메일로 전송되었습니다.";
                } catch (Exception $e) {
                    $message = "메일 전송 실패: {$mail->ErrorInfo}";
                }
            } else {
                $message = "사용자를 찾을 수 없습니다.";
            }
        }
    }

    if (isset($_POST['verify_code'])) {
        // 인증번호 확인
        $inputCode = trim($_POST['input_code']);
        $sessionCode = $_SESSION['verification_code'] ?? null;

        if ($sessionCode && $sessionCode === $inputCode) {
            // 아이디와 비밀번호 가져오기
            $userid = $_SESSION["userid"] ?? null;
            if ($userid) {
                $sql = "SELECT id, pass FROM members WHERE id = '$userid'";
                $result = mysqli_query($con, $sql);
                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $userId = $row['id'];
                    $userPass = $row['pass'];
                    $displayStyle = "block";
                    $_SESSION['verification_code'] = null; // 인증번호 제거
                } else {
                    $message = "사용자 정보를 찾을 수 없습니다.";
                }
            } else {
                $message = "세션 정보가 유효하지 않습니다. 다시 시도해주세요.";
            }
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
    <title>아이디/비밀번호 찾기</title>
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
        <h1>아이디/비밀번호 찾기</h1>
        <div class="message">
            <?= $message ?>
        </div>
        <!-- 이메일 전송 -->
        <form method="post" action="find.php" class="find-form">
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
            <p><strong>아이디:</strong> <?= htmlspecialchars($userId) ?></p>
            <p><strong>비밀번호:</strong> <?= htmlspecialchars($userPass) ?></p>
        </div>
    </div>
</body>
</html>
