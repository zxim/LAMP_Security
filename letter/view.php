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

// 쪽지 데이터 가져오기
$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['userid'] ?? null;

if (!$user_id) {
    echo "<script>alert('로그인 후 이용 가능합니다.'); location.href='/project/login/login_form.php';</script>";
    exit;
}

if ($message_id <= 0) {
    echo "<script>alert('유효하지 않은 메시지 ID입니다.'); history.back();</script>";
    exit;
}

// 메시지 및 보낸 사람 정보 조회
$query = "
    SELECT 
        m.*, 
        s.name AS sender_name 
    FROM messages AS m
    JOIN members AS s ON m.sender_id = s.id
    WHERE m.id = ? AND (m.receiver_id = ? OR m.sender_id = ?)
";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iss", $message_id, $user_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<script>alert('메시지를 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

$message = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <title>쪽지 보기</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include "../login/header.php"; ?>

<div class="message-container">
    <h1 class="message-title">쪽지 내용</h1>
    <p><strong>보낸 사람:</strong> <?= htmlspecialchars($message['sender_name']) ?> (<?= htmlspecialchars($message['sender_id']) ?>)</p>
    <p><strong>제목:</strong> <?= htmlspecialchars($message['subject']) ?></p>
    <div class="message-content">
        <?= nl2br(htmlspecialchars($message['content'])) ?>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <a class="message-btn" href="message.php">목록으로 돌아가기</a>
        <a class="message-btn" href="reply.php?receiver_id=<?= htmlspecialchars($message['sender_id']) ?>">답장하기</a>
    </div>
</div>
</body>
</html>
<?php
mysqli_close($con);
?>
