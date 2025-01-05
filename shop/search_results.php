<?php
// 네이버 API 정보
$client_id = ""; 
$client_secret = ""; 

// 검색어 가져오기 및 검증
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
if (empty($query)) {
    echo "<script>alert('검색어를 입력하세요.'); history.back();</script>";
    exit();
}

// 네이버 검색 API 요청
$display_count = 30; // 검색 결과 개수 설정
$url = "https://openapi.naver.com/v1/search/shop.json?query=" . urlencode($query) . "&display=" . $display_count;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Naver-Client-Id: $client_id",
    "X-Naver-Client-Secret: $client_secret"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
if ($response === false) {
    echo "API 요청 실패: " . curl_error($ch);
    curl_close($ch);
    exit();
}

curl_close($ch);
$result = json_decode($response, true); // JSON 응답 디코딩
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <link rel="stylesheet" href="search.css">
    <?php include "../login/header.php"; ?>
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
            text-decoration: none; /* 링크 스타일 제거 */
            color: inherit; /* 기본 텍스트 색상 유지 */
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
        .search-header {
            display: flex;
            align-items: center;
            justify-content: center; /* 가로 정렬 */
            gap: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .search-header h1 {
            margin: 0;
            color: #007aff;
            flex: 1; /* 가운데 정렬을 위한 공간 확보 */
            text-align: center;
        }
        .search-header form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-header input {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 200px;
        }
        .search-header button {
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #007aff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .search-header button:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
    <div class="search-header">
        <h1>검색 결과: "<?php echo htmlspecialchars($query); ?>"</h1>
        <form method="get" action="search_results.php">
            <input type="text" name="query" placeholder="검색어를 입력하세요" required>
            <button type="submit">검색</button>
        </form>
    </div>
    <div>
        <?php
        if (isset($result['items']) && count($result['items']) > 0) {
            foreach ($result['items'] as $item) {
                echo '<a href="' . htmlspecialchars($item['link']) . '" target="_blank" class="product">'; // 상품 링크 추가
                echo '<img src="' . htmlspecialchars($item['image']) . '" alt="상품 이미지">';
                echo '<div class="product-name">' . htmlspecialchars($item['title']) . '</div>';
                echo '<div class="product-price">' . htmlspecialchars($item['lprice']) . '원</div>';
                echo '</a>'; // 링크 종료
            }
        } else {
            echo '<p>검색 결과가 없습니다.</p>';
        }
        ?>
    </div>
</body>
</html>
