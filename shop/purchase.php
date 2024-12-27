<?php
// 세션 정보 포함
include "../memberboard/session.php";

// DB 연결 정보 설정
$config = require '../config.php';
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    die("DB 연결 실패");
}

// 로그인 확인
if ($user_num == 0) {
    die("로그인이 필요합니다.");
}

// POST 데이터 가져오기
$productName = $_POST['productName'];
$selectedColor = $_POST['selectedColor'];
$storage = $_POST['storage'];
$additionalPrice = (int)$_POST['additionalPrice'];
$quantity = (int)$_POST['quantity'];

// 상품 기본 가격 가져오기
$productQuery = "SELECT product_id, price FROM products WHERE name = '$productName'";
$productResult = mysqli_query($con, $productQuery);
$productRow = mysqli_fetch_assoc($productResult);

if ($productRow) {
    $product_id = $productRow['product_id'];
    $base_price = $productRow['price'];
} else {
    die("상품 정보를 찾을 수 없습니다.");
}

// 총 결제 금액 계산
$total_price = ($base_price + $additionalPrice) * $quantity;

// 사용자 포인트 가져오기
$pointsQuery = "SELECT points FROM members WHERE num = $user_num";
$pointsResult = mysqli_query($con, $pointsQuery);
$pointsRow = mysqli_fetch_assoc($pointsResult);

if ($pointsRow) {
    $currentPoints = $pointsRow['points'];

    // 포인트 부족 확인
    if ($currentPoints < $total_price) {
        die("포인트가 부족합니다.");
    }

    // 포인트 차감
    $newPoints = $currentPoints - $total_price;
    $updatePointsQuery = "UPDATE members SET points = $newPoints WHERE num = $user_num";
    mysqli_query($con, $updatePointsQuery);
} else {
    die("회원 정보를 찾을 수 없습니다.");
}

// 구매 정보 저장
$orderQuery = "INSERT INTO orders (member_id, product_id, quantity, total_price) 
               VALUES ($user_num, $product_id, $quantity, $total_price)";
mysqli_query($con, $orderQuery);

// 구매 완료 메시지 및 페이지 이동
echo "<script>
    alert('구매가 성공적으로 완료되었습니다!');
    location.href = '/project/shop/categories.php';
</script>";

// DB 연결 종료
mysqli_close($con);
?>
