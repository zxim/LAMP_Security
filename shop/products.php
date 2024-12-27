<?php
include "session.php";
$config = require '../config.php';

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

// 카테고리 가져오기
$category = isset($_GET['category']) ? mysqli_real_escape_string($con, $_GET['category']) : '';
if (empty($category)) {
    echo "<script>alert('유효한 카테고리를 선택해주세요.'); history.back();</script>";
    exit;
}

// 해당 카테고리 상품 데이터 가져오기
$sql = "SELECT * FROM products WHERE category = '$category'";
$result = mysqli_query($con, $sql);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "<script>alert('해당 카테고리에 상품이 없습니다.'); history.back();</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category) ?> - 상품 목록</title>
    <style>
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            padding: 15px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .product-card img {
            width: 100%;
            border-radius: 10px;
        }
        .product-card h3 {
            margin: 10px 0;
        }
        .product-card p {
            font-size: 16px;
            margin: 5px 0;
        }
        .product-card button {
            background-color: black;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .product-card button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;"><?= htmlspecialchars($category) ?> 상품 목록</h1>
    <div class="product-container">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="product-card">
                <img src="./images/<?= $row['product_id'] ?>.jpg" alt="<?= htmlspecialchars($row['name']) ?>">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p>가격: <?= htmlspecialchars($row['price']) ?> 포인트</p>
                <form action="purchase.php" method="post">
                    <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                    <button type="submit">구매하기</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

<?php
mysqli_close($con);
?>