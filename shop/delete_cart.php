<?php
include "../memberboard/session.php";

// DB 연결 정보 설정
$config = require '../config.php';
$db_host = $config['DB_HOST'];
$db_user = $config['DB_USER'];
$db_password = $config['DB_PASSWORD'];
$db_name = $config['DB_NAME'];

// DB 연결
$con = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$con) {
    die("<script>alert('DB 연결 실패: " . mysqli_connect_error() . "'); window.history.back();</script>");
}

// 로그인 확인
if ($user_num == 0) {
    die("<script>alert('로그인이 필요합니다.'); window.history.back();</script>");
}

// POST 데이터 가져오기
$cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;

if ($cart_id <= 0) {
    die("<script>alert('잘못된 요청입니다.'); window.history.back();</script>");
}

// 장바구니 항목 삭제
$delete_sql = "DELETE FROM cart WHERE cart_id = ? AND member_id = ?";
$stmt = mysqli_prepare($con, $delete_sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_num);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('장바구니에서 삭제되었습니다.'); window.location.href = 'cart.php';</script>";
    } else {
        echo "<script>alert('삭제 중 오류가 발생했습니다.'); window.history.back();</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('쿼리 준비 중 오류가 발생했습니다.'); window.history.back();</script>";
}

// DB 연결 종료
mysqli_close($con);
?>
