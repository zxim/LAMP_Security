<?php
// 세션 정보 포함
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
    die("DB 연결 실패: " . mysqli_connect_error());
}

// 로그인 확인
if ($user_num == 0) {
    die("로그인이 필요합니다.");
}

// POST 데이터 가져오기
$productName = isset($_POST['productName']) ? $_POST['productName'] : '';
$selectedColor = isset($_POST['selectedColor']) ? $_POST['selectedColor'] : '';
$storage = isset($_POST['storage']) ? $_POST['storage'] : '';
$additionalPrice = isset($_POST['additionalPrice']) ? intval($_POST['additionalPrice']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// 상품 기본 가격 가져오기
$productQuery = "SELECT product_id, price FROM products WHERE name = ?";
$productStmt = mysqli_prepare($con, $productQuery);
if (!$productStmt) {
    die("쿼리 준비 실패: " . mysqli_error($con));
}
mysqli_stmt_bind_param($productStmt, "s", $productName);
mysqli_stmt_execute($productStmt);
$productResult = mysqli_stmt_get_result($productStmt);

if ($row = mysqli_fetch_assoc($productResult)) {
    $product_id = $row['product_id'];
    $base_price = $row['price'];
} else {
    die("상품 정보를 찾을 수 없습니다.");
}

// 총 결제 금액 계산
$total_price = ($base_price + $additionalPrice) * $quantity;

// 사용자 포인트 확인
$pointsQuery = "SELECT points FROM members WHERE num = ?";
$pointsStmt = mysqli_prepare($con, $pointsQuery);
if (!$pointsStmt) {
    die("쿼리 준비 실패: " . mysqli_error($con));
}
mysqli_stmt_bind_param($pointsStmt, "i", $user_num);
mysqli_stmt_execute($pointsStmt);
$pointsResult = mysqli_stmt_get_result($pointsStmt);

if ($pointsRow = mysqli_fetch_assoc($pointsResult)) {
    $currentPoints = $pointsRow['points'];

    // 포인트 부족 여부 확인
    if ($currentPoints < $total_price) {
        die("포인트가 부족합니다.");
    }

    // 포인트 차감
    $newPoints = $currentPoints - $total_price;
    $updatePointsQuery = "UPDATE members SET points = ? WHERE num = ?";
    $updatePointsStmt = mysqli_prepare($con, $updatePointsQuery);
    mysqli_stmt_bind_param($updatePointsStmt, "ii", $newPoints, $user_num);
    if (!mysqli_stmt_execute($updatePointsStmt)) {
        die("포인트 차감 중 오류: " . mysqli_error($con));
    }
    mysqli_stmt_close($updatePointsStmt);
} else {
    die("회원 정보를 찾을 수 없습니다.");
}
mysqli_stmt_close($pointsStmt);

// 구매 정보 저장
$orderQuery = "INSERT INTO orders (member_id, product_id, quantity, total_price) 
               VALUES (?, ?, ?, ?)";
$orderStmt = mysqli_prepare($con, $orderQuery);
if (!$orderStmt) {
    die("쿼리 준비 실패: " . mysqli_error($con));
}
mysqli_stmt_bind_param($orderStmt, "iiii", $user_num, $product_id, $quantity, $total_price);

if (mysqli_stmt_execute($orderStmt)) {
    echo "<script>
        alert('구매가 성공적으로 완료되었습니다!');
        location.href = '/project/shop/categories.php';
    </script>";
} else {
    die("구매 처리 중 오류: " . mysqli_error($con));
}

// DB 연결 종료
mysqli_stmt_close($orderStmt);
mysqli_close($con);
?>
