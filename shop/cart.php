<?php
include "../memberboard/session.php";

// DB 연결 설정
$config = require '../config.php';
$db_host = $config['DB_HOST'];
$db_user = $config['DB_USER'];
$db_password = $config['DB_PASSWORD'];
$db_name = $config['DB_NAME'];

// DB 연결
$con = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($con->connect_error) {
    die("<script>alert('DB 연결 실패: " . $con->connect_error . "');</script>");
}

// 로그인한 사용자의 ID 확인
if (!isset($user_num) || empty($user_num)) {
    die("<script>alert('로그인 후 이용해주세요.'); window.location.href = '/project/login/login_form.php';</script>");
}

// CSRF 토큰 생성 및 저장
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// 로그인한 사용자의 포인트 조회
$point_query = "SELECT points FROM members WHERE num = ?";
$stmt = $con->prepare($point_query);
$stmt->bind_param("i", $user_num);
$stmt->execute();
$point_result = $stmt->get_result();
$user_points = $point_result->fetch_assoc()['points'] ?? 0;

// 장바구니 데이터 조회
$sql = "SELECT c.cart_id, p.name AS product_name, p.image_url, p.price, c.quantity, 
            (c.quantity * p.price) AS total_price
        FROM cart c
        INNER JOIN products p ON c.product_id = p.product_id
        WHERE c.member_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_num);
$stmt->execute();
$result = $stmt->get_result();
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
                selectedItems.splice(rowIndex, 1);
                row.classList.remove("selected");
            } else {
                selectedItems.push(cartId);
                row.classList.add("selected");
            }
        }
        function updateQuantity(cartId, change, csrfToken) {
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
            fetch("update_quantity.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ cartId, quantity, csrf_token: csrfToken })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert("수량 업데이트 중 오류가 발생했습니다.");
                }
            });
        }
        function deleteItem(cartId, csrfToken) {
            if (confirm("이 항목을 삭제하시겠습니까?")) {
                fetch("delete_item.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ cartId, csrf_token: csrfToken })
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
        function purchaseAll(csrfToken) {
            if (confirm("장바구니에 있는 모든 상품을 구매하시겠습니까?")) {
                fetch("purchase_all.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ userNum: <?= $user_num ?>, csrf_token: csrfToken })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("전체 구매가 완료되었습니다!");
                        location.reload();
                    } else {
                        alert("구매 처리 중 오류가 발생했습니다: " + data.message);
                    }
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
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr 
                            onclick="toggleSelect(this, <?= htmlspecialchars($row['cart_id']) ?>)" 
                            data-cart-id="<?= htmlspecialchars($row['cart_id']) ?>">
                            <td>
                                <button onclick="event.stopPropagation(); deleteItem(<?= htmlspecialchars($row['cart_id']) ?>, '<?= $csrf_token ?>')">삭제</button>
                            </td>
                            <td>
                                <div class="product-info">
                                    <img src="<?= htmlspecialchars($row['image_url'] ?? '/project/images/default.jpg') ?>" 
                                         alt="<?= htmlspecialchars($row['product_name']) ?>" 
                                         class="product-image">
                                    <?= htmlspecialchars($row['product_name']) ?>
                                </div>
                            </td>
                            <td><?= number_format($row['price']) ?>원</td>
                            <td>
                                <div class="quantity-controls">
                                    <button onclick="event.stopPropagation(); updateQuantity(<?= htmlspecialchars($row['cart_id']) ?>, -1, '<?= $csrf_token ?>')">-</button>
                                    <span id="quantity-<?= htmlspecialchars($row['cart_id']) ?>"><?= htmlspecialchars($row['quantity']) ?></span>
                                    <button onclick="event.stopPropagation(); updateQuantity(<?= htmlspecialchars($row['cart_id']) ?>, 1, '<?= $csrf_token ?>')">+</button>
                                </div>
                            </td>
                            <td id="total-<?= htmlspecialchars($row['cart_id']) ?>" data-unit-price="<?= htmlspecialchars($row['price']) ?>">
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

        <?php if ($result && $result->num_rows > 0): ?>
        <div class="purchase-container">
            <button class="purchase-all-btn" onclick="purchaseAll('<?= $csrf_token ?>')">전체 구매하기</button>
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
    $con->close();
}
?>
