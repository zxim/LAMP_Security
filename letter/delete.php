<?php
include "../memberboard/session.php"; // 세션 처리
$config = require '../config.php'; // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_id = intval($_POST['id']);
    $user_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

    if (!$user_id) {
        echo "<script>alert('로그인 후 이용 가능합니다.'); location.href='/project/login/login_form.php';</script>";
        exit;
    }

    $query = "DELETE FROM messages WHERE id = $message_id AND receiver_id = '$user_id'";
    if (!mysqli_query($con, $query)) {
        echo "<script>alert('삭제 실패: " . mysqli_error($con) . "'); history.back();</script>";
        exit;
    }

    echo "<script>alert('쪽지가 삭제되었습니다.'); location.href='message.php';</script>";
    exit;
}
mysqli_close($con);
?>
