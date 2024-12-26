<?php
include "session.php"; // 세션 처리
$config = require '../config.php'; // DB 설정

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('잘못된 요청입니다. POST 요청만 허용됩니다.'); history.back();</script>";
    exit;
}

// 입력값 가져오기
$product_id = intval($_POST['product_id']);
$member_id = $_SESSION['user_num'];

// 상품 정보 가져오기
$product_query = "SELECT * FROM products WHERE product_id = $product_id";
$product_result = mysqli_query($con, $product_query);
$product = mysqli_fetch_assoc($product_result);

if (!$product) {
    echo "<script>alert('상품 정보를 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

// 사용자 포인트 확인
$member_query = "SELECT points FROM members WHERE num = $member_id";
$member_result = mysqli_query($con, $member_query);
$member = mysqli_fetch_assoc($member_result);

if ($member['points'] < $product['price']) {
    echo "<script>alert('포인트가 부족합니다.'); history.back();</script>";
    exit;
}

// 트랜잭션 시작
mysqli_begin_transaction($con);

try {
    // 포인트 차감
    $update_points = "UPDATE members SET points = points - {$product['price']} WHERE num = $member_id";
    if (!mysqli_query($con, $update_points)) {
        throw new Exception("포인트 차감 실패: " . mysqli_error($con));
    }

    // 주문 기록 추가
    $insert_order = "INSERT INTO orders (member_id, product_id) VALUES ($member_id, $product_id)";
    if (!mysqli_query($con, $insert_order)) {
        throw new Exception("주문 기록 추가 실패: " . mysqli_error($con));
    }

    // 트랜잭션 커밋
    mysqli_commit($con);
    echo "<script>alert('구매가 완료되었습니다!'); location.href = 'products.php';</script>";
} catch (Exception $e) {
    // 트랜잭션 롤백
    mysqli_rollback($con);
    echo "<script>alert('" . $e->getMessage() . "'); history.back();</script>";
} finally {
    mysqli_close($con);
}
?>
