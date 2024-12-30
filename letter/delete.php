<?php
include "../memberboard/session.php"; // 세션 처리
$config = require '../config.php'; // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

// 로그인 여부 확인
$user_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
if (!$user_id) {
    echo "<script>alert('로그인 후 이용 가능합니다.'); location.href='/project/login/login_form.php';</script>";
    exit;
}

// POST 요청으로 삭제 ID 확인
$message_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($message_id > 0) {
    // 삭제 쿼리: 받은 쪽지와 보낸 쪽지 모두 삭제 가능하도록 조건 추가
    $query = "
        DELETE FROM messages 
        WHERE id = $message_id 
        AND (receiver_id = '$user_id' OR sender_id = '$user_id')
    ";
    
    if (mysqli_query($con, $query)) {
        if (mysqli_affected_rows($con) > 0) {
            echo "<script>alert('쪽지가 삭제되었습니다.'); location.href='message.php';</script>";
        } else {
            echo "<script>alert('삭제할 권한이 없거나 이미 삭제된 쪽지입니다.'); history.back();</script>";
        }
    } else {
        echo "<script>alert('삭제 실패: " . mysqli_error($con) . "'); history.back();</script>";
    }
} else {
    echo "<script>alert('유효하지 않은 요청입니다.'); history.back();</script>";
}

mysqli_close($con);
?>
