<?php
include "../memberboard/session.php"; // 세션 처리
$config = require '../config.php'; // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

// 쪽지 작성 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
    $receiver_id = $_POST['receiver_id'];
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $content = mysqli_real_escape_string($con, $_POST['content']);

    if (!$sender_id) {
        echo "<script>alert('로그인 후 이용 가능합니다.'); location.href='/project/login/login_form.php';</script>";
        exit;
    }

    // 메시지 삽입
    $query = "INSERT INTO messages (sender_id, receiver_id, subject, content) VALUES ('$sender_id', '$receiver_id', '$subject', '$content')";
    if (!mysqli_query($con, $query)) {
        echo "<script>alert('쪽지 보내기 실패: " . mysqli_error($con) . "'); history.back();</script>";
        exit;
    }

    echo "<script>alert('쪽지가 성공적으로 전송되었습니다.'); location.href='message.php';</script>";
    exit;
}
mysqli_close($con);
?>

<!DOCTYPE html>
<html>
<head>
    <title>쪽지 보내기</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include "../login/header.php"; ?>
<div class="message-container">
    <h1 class="message-title">쪽지 보내기</h1>
    <form class="message-form" method="post" action="send.php">
        <label for="receiver_id">받는 사람 ID:</label>
        <input type="text" id="receiver_id" name="receiver_id" required>

        <label for="subject">제목:</label>
        <input type="text" id="subject" name="subject" maxlength="100" required>

        <label for="content">내용:</label>
        <textarea id="content" name="content" rows="5" required></textarea>

        <button class="message-button" type="submit" style="margin-bottom: 20px;">쪽지 보내기</button>
        <button class="message-button" type="button" onclick="location.href='message.php'">목록으로 돌아가기</button>
    </form>
</div>
</body>
</html>
