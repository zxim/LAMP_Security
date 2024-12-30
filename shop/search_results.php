<?php
// 네이버 API 정보
$client_id = ""; 
$client_secret = ""; 

// 검색어 가져오기
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
if (!$query) {
    echo "<script>alert('검색어를 입력하세요.'); history.back();</script>";
    exit();
}

// 네이버 검색 API 요청 URL
$url = "https://openapi.naver.com/v1/search/shop.json?query=" . urlencode($query);

// CURL로 API 호출
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Naver-Client-Id: $client_id",
    "X-Naver-Client-Secret: $client_secret"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "API 요청 실패: " . curl_error($ch);
    curl_close($ch);
    exit();
}

curl_close($ch);
$result = json_decode($response, true); // JSON 응답 디코딩

// 검색 결과 출력
?>
<!DOCTYPE html>
<html lang="ko">
<link rel="stylesheet" href="search.css">
<head>
    <?php include "../login/header.php" ?>
    <meta charset="UTF-8">
    <title>검색 결과</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
        }
        .product {
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 10px;
            padding: 10px;
            display: inline-block;
            width: 200px;
            text-align: center;
        }
        .product img {
            max-width: 100%;
            height: auto;
        }
        .product-name {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        .product-price {
            color: #007aff;
        }
    </style>
</head>
<body>
    <div>
        <h1>검색 결과: "<?php echo htmlspecialchars($query); ?>"</h1>
        <form method="get" action="search_results.php" style="display: inline-block; float: right; margin: 10px;">
            <input type="text" name="query" placeholder="검색어를 입력하세요" required style="padding: 5px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 5px 10px; border-radius: 5px; background-color: #007aff; color: #fff; border: none; cursor: pointer;">
                검색
            </button>
        </form>
        </div>
    <div>
        <?php
        if (isset($result['items']) && count($result['items']) > 0) {
            foreach ($result['items'] as $item) {
                echo '<div class="product">';
                echo '<img src="' . htmlspecialchars($item['image']) . '" alt="상품 이미지">';
                echo '<div class="product-name">' . htmlspecialchars($item['title']) . '</div>';
                echo '<div class="product-price">' . htmlspecialchars($item['lprice']) . '원</div>';
                echo '</div>';
            }
        } else {
            echo '<p>검색 결과가 없습니다.</p>';
        }
        ?>
    </div>
</body>
</html>
