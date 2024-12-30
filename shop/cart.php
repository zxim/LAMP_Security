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
if (!isset($user_num) || empty($user_num)) {
    die("<script>alert('로그인 후 이용해주세요.'); window.location.href = '/project/login/login_form.php';</script>");
}

// 로그인한 사용자의 포인트 조회
$point_query = "SELECT points FROM members WHERE num = $user_num";
$point_result = mysqli_query($con, $point_query);
if (!$point_result) {
    die("<script>alert('포인트 조회 실패: " . mysqli_error($con) . "');</script>");
}
$user_points = mysqli_fetch_assoc($point_result)['points'] ?? 0;

// 장바구니 데이터 조회
$sql = "SELECT c.cart_id, p.name AS product_name, p.image_url, p.price, c.quantity, 
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
        function deleteItem(cartId) {
            if (confirm("이 항목을 삭제하시겠습니까?")) {
                fetch("delete_item.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ cartId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("항목이 삭제되었습니다.");
                        location.reload();
                    } else {
                        alert("삭제 중 오류가 발생했습니다.");
                    }
                });
            }
        }
        function purchaseAll() {
            if (confirm("장바구니에 있는 모든 상품을 구매하시겠습니까?")) {
                fetch("purchase_all.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ userNum: <?= $user_num ?> })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("전체 구매가 완료되었습니다!");
                        location.reload();
                    } else {
                        alert("구매 처리 중 오류가 발생했습니다: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("네트워크 오류가 발생했습니다.");
                });
            }
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
                    <th>삭제</th>
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
                            <!-- 삭제 버튼 -->
                            <td>
                                <button onclick="event.stopPropagation(); deleteItem(<?= $row['cart_id'] ?>)">삭제</button>
                            </td>
                            <!-- 상품명과 사진 -->
                            <td>
                                <div class="product-info">
                                    <?php if (!empty($row['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="product-image">
                                    <?php else: ?>
                                        <img src="/project/images/default.jpg" alt="기본 이미지" class="product-image">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($row['product_name']) ?>
                                </div>
                            </td>
                            <!-- 가격 -->
                            <td><?= number_format($row['price']) ?>원</td>
                            <!-- 수량 -->
                            <td>
                                <div class="quantity-controls">
                                    <button onclick="event.stopPropagation(); updateQuantity(<?= $row['cart_id'] ?>, -1)">-</button>
                                    <span id="quantity-<?= $row['cart_id'] ?>"><?= $row['quantity'] ?></span>
                                    <button onclick="event.stopPropagation(); updateQuantity(<?= $row['cart_id'] ?>, 1)">+</button>
                                </div>
                            </td>
                            <!-- 총 금액 -->
                            <td id="total-<?= $row['cart_id'] ?>" data-unit-price="<?= $row['price'] ?>">
                                <?= number_format($row['total_price']) ?>원
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">장바구니에 상품이 없습니다.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- 전체 구매하기 버튼 -->
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <div class="purchase-container">
            <button class="purchase-all-btn" onclick="purchaseAll()">전체 구매하기</button>
        </div>
            <p style="position: absolute; bottom: 10px; right: 20px; font-size: 16px; font-weight: bold; color: #333;">
                My Points: ₩ <?= number_format($user_points) ?>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
if ($con) {
    mysqli_close($con);
}
?>
