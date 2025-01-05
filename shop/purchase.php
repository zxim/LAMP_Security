<?php
// 세션 정보 포함
include "../memberboard/session.php";

// DB 연결 정보 설정
$config = require '../config.php';
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    die("<script>alert('DB 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>");
}

// 로그인 확인
if (empty($user_num)) {
    die("<script>alert('로그인이 필요합니다.'); history.back();</script>");
}

// POST 데이터 가져오기 및 검증
$productName = $_POST['productName'] ?? '';
$selectedColor = $_POST['selectedColor'] ?? '';
$storage = $_POST['storage'] ?? '';
$additionalPrice = (int)($_POST['additionalPrice'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);

if (empty($productName) || $quantity <= 0) {
    die("<script>alert('유효한 상품 정보가 필요합니다.'); history.back();</script>");
}

// 트랜잭션 시작
mysqli_begin_transaction($con);

try {
    // 상품 기본 가격 가져오기
    $productQuery = "SELECT product_id, price FROM products WHERE name = ?";
    $stmt = mysqli_prepare($con, $productQuery);
    if (!$stmt) {
        throw new Exception("쿼리 준비 실패: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 's', $productName);
    mysqli_stmt_execute($stmt);
    $productResult = mysqli_stmt_get_result($stmt);
    $productRow = mysqli_fetch_assoc($productResult);
    mysqli_stmt_close($stmt);

    if (!$productRow) {
        throw new Exception("상품 정보를 찾을 수 없습니다.");
    }

    $product_id = $productRow['product_id'];
    $base_price = $productRow['price'];

    // 총 결제 금액 계산
    $total_price = ($base_price + $additionalPrice) * $quantity;

    // 사용자 포인트 가져오기
    $pointsQuery = "SELECT points FROM members WHERE num = ?";
    $stmt = mysqli_prepare($con, $pointsQuery);
    if (!$stmt) {
        throw new Exception("쿼리 준비 실패: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'i', $user_num);
    mysqli_stmt_execute($stmt);
    $pointsResult = mysqli_stmt_get_result($stmt);
    $pointsRow = mysqli_fetch_assoc($pointsResult);
    mysqli_stmt_close($stmt);

    if (!$pointsRow) {
        throw new Exception("회원 정보를 찾을 수 없습니다.");
    }

    $currentPoints = $pointsRow['points'];

    // 포인트 부족 확인
    if ($currentPoints < $total_price) {
        throw new Exception("포인트가 부족합니다.");
    }

    // 포인트 차감
    $newPoints = $currentPoints - $total_price;
    $updatePointsQuery = "UPDATE members SET points = ? WHERE num = ?";
    $stmt = mysqli_prepare($con, $updatePointsQuery);
    if (!$stmt) {
        throw new Exception("쿼리 준비 실패: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'ii', $newPoints, $user_num);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("포인트 차감 중 오류 발생");
    }
    mysqli_stmt_close($stmt);

    // 구매 정보 저장
    $orderQuery = "INSERT INTO orders (member_id, product_id, quantity, total_price) 
                   VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $orderQuery);
    if (!$stmt) {
        throw new Exception("쿼리 준비 실패: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'iiii', $user_num, $product_id, $quantity, $total_price);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("주문 내역 저장 중 오류 발생");
    }
    mysqli_stmt_close($stmt);

    // 트랜잭션 커밋
    mysqli_commit($con);

    // 구매 완료 메시지 및 페이지 이동
    echo "<script>
        alert('구매가 성공적으로 완료되었습니다!');
        location.href = '/project/shop/categories.php';
    </script>";
} catch (Exception $e) {
    // 트랜잭션 롤백
    mysqli_rollback($con);

    // 오류 메시지 출력
    echo "<script>
        alert('오류 발생: " . htmlspecialchars($e->getMessage()) . "');
        history.back();
    </script>";
}

// DB 연결 종료
mysqli_close($con);
?>
