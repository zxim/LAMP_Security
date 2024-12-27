<?php 
include "../memberboard/session.php";

// DB 연결 설정
$config = require '../config.php';
$db_host = $config['DB_HOST'];
$db_user = $config['DB_USER'];
$db_password = $config['DB_PASSWORD'];
$db_name = $config['DB_NAME'];

// DB 연결
$con = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$con) {
    die("<script>alert('DB 연결 실패: " . mysqli_connect_error() . "');</script>");
}

// 로그인한 사용자의 ID 확인
if ($user_num == 0) {
    die("<script>alert('로그인 후 이용해주세요.'); window.location.href = '../login/login.php';</script>");
}

// 장바구니 데이터 조회
$sql = "SELECT c.cart_id, p.name AS product_name, p.price, c.quantity, 
               (c.quantity * p.price) AS total_price
        FROM cart c
        INNER JOIN products p ON c.product_id = p.product_id
        WHERE c.member_id = $user_num";
$result = mysqli_query($con, $sql);

if (!$result) {
    die("<script>alert('쿼리 실행 실패: " . mysqli_error($con) . "');</script>");
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <?php include "../login/header.php"; ?>

    <div class="cart-container">
        <h1>My Cart</h1>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>상품명</th>
                    <th>가격</th>
                    <th>수량</th>
                    <th>총 금액</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr 
                            onclick="toggleSelect(this, <?= $row['cart_id'] ?>)" 
                            data-cart-id="<?= $row['cart_id'] ?>">
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= number_format($row['price']) ?>원</td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= number_format($row['total_price']) ?>원</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">장바구니에 상품이 없습니다.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="cart-actions">
            <button onclick="selectAll(document.querySelectorAll('.cart-table tbody tr'))">전체 선택</button>
            <button onclick="clearSelection(document.querySelectorAll('.cart-table tbody tr'))">선택 해제</button>
            <button onclick="deleteSelected()">삭제</button>
            <form id="checkoutForm" action="checkout.php" method="POST" style="display: inline;">
                <input type="hidden" name="cartIds" id="cartIds">
                <button type="button" onclick="submitCheckout()">구매하기</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($con);
?>
