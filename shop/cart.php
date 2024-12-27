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
    <script>
        let selectedItems = [];

        function toggleSelect(row, cartId) {
            const rowIndex = selectedItems.indexOf(cartId);

            if (rowIndex > -1) {
                // 이미 선택된 경우 해제
                selectedItems.splice(rowIndex, 1);
                row.classList.remove("selected");
            } else {
                // 선택되지 않은 경우 추가
                selectedItems.push(cartId);
                row.classList.add("selected");
            }

            console.log(`선택된 항목: ${selectedItems}`);
        }

        function updateQuantity(cartId, change) {
            const quantityElement = document.querySelector(`#quantity-${cartId}`);
            const totalElement = document.querySelector(`#total-${cartId}`);
            const price = parseInt(totalElement.dataset.unitPrice, 10);

            let quantity = parseInt(quantityElement.textContent, 10);
            quantity += change;

            if (quantity < 1) {
                alert("수량은 1개 이상이어야 합니다.");
                return;
            }

            quantityElement.textContent = quantity;
            totalElement.textContent = `${(price * quantity).toLocaleString()}원`;

            // 서버에 수량 업데이트 요청
            fetch("update_quantity.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ cartId, quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert("수량 업데이트 중 오류가 발생했습니다.");
                }
            });
        }
    </script>
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
                            <td>
                                <div class="quantity-controls">
                                    <button onclick="event.stopPropagation(); updateQuantity(<?= $row['cart_id'] ?>, -1)">-</button>
                                    <span id="quantity-<?= $row['cart_id'] ?>"><?= $row['quantity'] ?></span>
                                    <button onclick="event.stopPropagation(); updateQuantity(<?= $row['cart_id'] ?>, 1)">+</button>
                                </div>
                            </td>
                            <td id="total-<?= $row['cart_id'] ?>" data-unit-price="<?= $row['price'] ?>">
                                <?= number_format($row['total_price']) ?>원
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">장바구니에 상품이 없습니다.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
if ($con) {
    mysqli_close($con);
}
?>
