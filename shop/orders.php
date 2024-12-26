<?php
include '../memberboard/session.php'; 

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

// 로그인한 사용자의 ID가 있는지 확인
if ($user_num == 0) {
    die("로그인 후 이용해주세요.");
}

// 사용자 구매 목록 가져오기
$query = "
    SELECT o.order_id, p.name AS product_name, p.image_url, o.quantity, o.total_price, o.order_date 
    FROM orders o
    JOIN products p ON o.product_id = p.product_id
    WHERE o.member_id = ?
    ORDER BY o.order_date DESC
";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $user_num);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("쿼리 실행 중 오류 발생: " . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>구매 내역</title>
    <link rel="stylesheet" href="orders.css">
</head>
<body>
<?php include "../login/header.php"; ?>
    <h1>구매 내역</h1>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>이미지</th>
                    <th>상품명</th>
                    <th>수량</th>
                    <th>총 금액</th>
                    <th>주문 날짜</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" style="width: 100px; height: auto; border-radius: 10px;"></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>₩<?php echo number_format($row['total_price']); ?></td>
                        <td><?php echo $row['order_date']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>구매 내역이 없습니다.</p>
    <?php endif; ?>
</body>
</html>
<?php
// DB 연결 종료
mysqli_stmt_close($stmt);
mysqli_close($con);
?>
