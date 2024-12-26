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

    <div class="product iPhone" id="iPhone">
        <h2>iPhone 16 Pro</h2>

        <div class="product-item">
            <img src="/project/shop/images/iPhone/iPhone_16_black.jpg" alt="Titanium Black">
            <div class="product-details">
                <h3>Titanium Gold</h3>
                <p>Experience the premium look and feel of titanium in gold.</p>
                <div class="product-actions">
                    <button class="buy-btn">Buy Now</button>
                    <button class="cart-btn">Add to Cart</button>
                </div>
            </div>
        </div>

        <div class="product-item">
            <img src="/project/shop/images/iPhone/iPhone_16_desert.jpg" alt="Titanium Desert">
            <div class="product-details">
                <h3>Titanium White</h3>
                <p>Sleek and sophisticated titanium in a pristine white finish.</p>
                <div class="product-actions">
                    <button class="buy-btn">Buy Now</button>
                    <button class="cart-btn">Add to Cart</button>
                </div>
            </div>
        </div>

        <div class="product-item">
            <img src="/project/shop/images/iPhone/iPhone_16_natural.jpg" alt="Titanium Natural">
            <div class="product-details">
                <h3>Titanium Black</h3>
                <p>A bold and modern design in titanium black.</p>
                <div class="product-actions">
                    <button class="buy-btn">Buy Now</button>
                    <button class="cart-btn">Add to Cart</button>
                </div>
            </div>
        </div>

        <div class="product-item">
            <img src="/project/shop/images/iPhone/iPhone_16_white.jpg" alt="Titanium White">
            <div class="product-details">
                <h3>Titanium Desert</h3>
                <p>Unique and adventurous titanium in a desert-inspired tone.</p>
                <div class="product-actions">
                    <button class="buy-btn">Buy Now</button>
                    <button class="cart-btn">Add to Cart</button>
                </div>
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
    </script>
</body>
</html>
