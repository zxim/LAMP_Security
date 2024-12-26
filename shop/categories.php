<!DOCTYPE html>
<html lang="ko">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>스토어</title>
</head>
<body>
    <!-- 헤더 포함 -->
    <?php include "../login/header.php"; ?>

    <!-- 파랑-보라-핑크 텍스트 제목 -->
    <h1 class="gradient-text">스토어</h1>

    <!-- 카테고리 컨테이너 -->
    <div class="category-container">
        <a href="products.php?category=iPhone">iPhone</a>
        <a href="products.php?category=Mac">Mac</a>
        <a href="products.php?category=AirPods">AirPods</a>
        <a href="products.php?category=iPad">iPad</a>
        <a href="products.php?category=Watch">Apple Watch</a>
    </div>
</body>
</html>
