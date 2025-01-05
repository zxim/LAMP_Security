<?php
include "../memberboard/session.php";

header('Content-Type: application/json');

// 로그인 확인
if (!isset($user_num) || empty($user_num)) {
    echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
    exit();
}

// DB 연결
$config = require '../config.php';
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);

if (!$con) {
    echo json_encode(['success' => false, 'message' => 'DB 연결 실패: ' . mysqli_connect_error()]);
    exit();
}

// 사용자 포인트 가져오기
$pointsQuery = "SELECT points FROM members WHERE num = ?";
$stmt = mysqli_prepare($con, $pointsQuery);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => '쿼리 준비 실패: ' . mysqli_error($con)]);
    exit();
}
mysqli_stmt_bind_param($stmt, 'i', $user_num);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pointsRow = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$pointsRow) {
    echo json_encode(['success' => false, 'message' => '회원 정보를 찾을 수 없습니다.']);
    exit();
}

$currentPoints = $pointsRow['points'];

// 장바구니 항목 가져오기
$cartQuery = "
    SELECT c.cart_id, c.quantity, p.product_id, p.price 
    FROM cart c
    INNER JOIN products p ON c.product_id = p.product_id
    WHERE c.member_id = ?";
$stmt = mysqli_prepare($con, $cartQuery);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => '쿼리 준비 실패: ' . mysqli_error($con)]);
    exit();
}
mysqli_stmt_bind_param($stmt, 'i', $user_num);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => '장바구니가 비어 있습니다.']);
    exit();
}

$total_price = 0; // 총 결제 금액 계산
$orderValues = []; // 주문 삽입을 위한 값 저장

while ($row = mysqli_fetch_assoc($result)) {
    $product_id = $row['product_id'];
    $quantity = $row['quantity'];
    $price = $row['price'];

    $item_total = $quantity * $price; // 항목별 총 금액 계산
    $total_price += $item_total;

    // 주문 내역 삽입을 위한 값 저장
    $orderValues[] = "($user_num, $product_id, $quantity, $item_total)";
}

mysqli_stmt_close($stmt);

// 포인트 부족 확인
if ($currentPoints < $total_price) {
    echo json_encode(['success' => false, 'message' => '포인트가 부족합니다.']);
    exit();
}

// 트랜잭션 시작
mysqli_begin_transaction($con);

try {
    // 포인트 차감
    $updatePointsQuery = "UPDATE members SET points = ? WHERE num = ?";
    $stmt = mysqli_prepare($con, $updatePointsQuery);
    if (!$stmt) {
        throw new Exception('쿼리 준비 실패: ' . mysqli_error($con));
    }
    $newPoints = $currentPoints - $total_price;
    mysqli_stmt_bind_param($stmt, 'ii', $newPoints, $user_num);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('포인트 차감 중 오류 발생');
    }
    mysqli_stmt_close($stmt);

    // 주문 내역 저장
    $orderQuery = "INSERT INTO orders (member_id, product_id, quantity, total_price) VALUES " . implode(", ", $orderValues);
    if (!mysqli_query($con, $orderQuery)) {
        throw new Exception('주문 내역 저장 중 오류 발생: ' . mysqli_error($con));
    }

    // 장바구니 비우기
    $deleteCartQuery = "DELETE FROM cart WHERE member_id = ?";
    $stmt = mysqli_prepare($con, $deleteCartQuery);
    if (!$stmt) {
        throw new Exception('쿼리 준비 실패: ' . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'i', $user_num);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('장바구니 비우기 중 오류 발생');
    }
    mysqli_stmt_close($stmt);

    // 트랜잭션 커밋
    mysqli_commit($con);

    // 성공 응답
    echo json_encode(['success' => true, 'message' => '구매가 성공적으로 완료되었습니다. 총 결제 금액: ' . number_format($total_price) . '원']);
} catch (Exception $e) {
    // 트랜잭션 롤백
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($con);
?>
