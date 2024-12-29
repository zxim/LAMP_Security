<?php
// 세션 및 데이터베이스 설정
include "../memberboard/session.php"; // 세션 처리
$config = require '../config.php'; // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

// 세션에서 로그인 사용자 정보 가져오기
$user_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

if (!$user_id) {
    echo "<script>alert('로그인 후 이용 가능합니다.'); location.href='/project/login/login_form.php';</script>";
    exit;
}

// 받는 사람 ID 가져오기
$receiver_id = isset($_GET['receiver_id']) ? htmlspecialchars($_GET['receiver_id']) : '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>답장하기</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include "../login/header.php"; ?>

<div class="message-container">
    <h1 class="message-title">답장하기</h1>
    <form action="send.php" method="post" class="message-form">
        <label for="receiver_id">받는 사람 ID:</label>
        <input type="text" id="receiver_id" name="receiver_id" value="<?= $receiver_id ?>" readonly>

        <label for="subject">제목:</label>
        <input type="text" id="subject" name="subject" placeholder="제목을 입력하세요" required>

        <label for="content">내용:</label>
        <textarea id="content" name="content" rows="10" placeholder="내용을 입력하세요" required></textarea>

        <button type="submit" class="message-btn">보내기</button>
    </form>
    <div style="text-align: center; margin-top: 20px;">
        <a class="message-btn" href="message.php">목록으로 돌아가기</a>
    </div>
</div>
</body>
</html>
<?php mysqli_close($con); ?>
