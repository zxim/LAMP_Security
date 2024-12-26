<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>스토어</title>
    <link rel="stylesheet" href="categories.css">
</head>
<body>
    <?php include "../login/header.php"; ?>

    <!-- 로고 -->
    <div class="store-logo">
        <img src="/project/shop/images/store.png" alt="스토어 로고">
    </div>

    <!-- 카테고리 버튼 -->
    <div class="category-container">
        <button onclick="showProducts('iPhone')">
            <img src="/project/shop/images/iphone.png" alt="iPhone">
            <span>iPhone</span>
        </button>
        <button onclick="showProducts('Mac')">
            <img src="/project/shop/images/Mac.png" alt="Mac">
            <span>Mac</span>
        </button>
        <button onclick="showProducts('AirPods')">
            <img src="/project/shop/images/AirPods.png" alt="AirPods">
            <span>AirPods</span>
        </button>
        <button onclick="showProducts('iPad')">
            <img src="/project/shop/images/iPad.png" alt="iPad">
            <span>iPad</span>
        </button>
        <button onclick="showProducts('Watch')">
            <img src="/project/shop/images/Watch.png" alt="Apple Watch">
            <span>Apple Watch</span>
        </button>
    </div>

    <div>
        <!-- iPhone 제품 섹션 -->
        <div class="product iPhone" id="iPhone">
            <h2>iPhone 16 Pro</h2>
            <div class="product-image">
                <img id="productImg" src="/project/shop/images/iPhone/iphone_16_pro.jpg" alt="iPhone 16">
            </div>
            <div class="color-options">
                <button onclick="changeImage('black')">Black</button>
                <button onclick="changeImage('natural')">Natural</button>
                <button onclick="changeImage('white')">White</button>
                <button onclick="changeImage('desert')">Desert</button>
            </div>
        </div>
    </div>

    <div class="product Mac" id="Mac">
        <p>Mac 상품 목록</p>
    </div>

    <div class="product AirPods" id="AirPods">
        <p>AirPods 상품 목록</p>
    </div>

    <div class="product iPad" id="iPad">
        <p>iPad 상품 목록</p>
    </div>

    <div class="product Watch" id="Watch">
        <p>Apple Watch 상품 목록</p>
    </div>

    <script>
        // 페이지 로드 시 초기 상태 설정
        document.addEventListener("DOMContentLoaded", () => {
            showProducts("iPhone");
        });

        function showProducts(categoryId) {
            // 모든 카테고리 숨기기
            const products = document.querySelectorAll(".product");
            products.forEach(product => product.style.display = "none");

            // 선택된 카테고리만 보이기
            const selectedProduct = document.getElementById(categoryId);
            if (selectedProduct) {
                selectedProduct.style.display = "block";
            }
        }

        function changeImage(color) {
            const productImg = document.getElementById("productImg");
            const images = {
                black: "/project/shop/images/iPhone/black.png",
                natural: "/project/shop/images/iPhone/natural.png",
                white: "/project/shop/images/iPhone/white.png",
                desert: "/project/shop/images/iPhone/desert.png",
            };
            productImg.src = images[color];
        }
    </script>
</body>
</html>
