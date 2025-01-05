<?php
// CSRF 토큰 생성
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
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

    <form method="get" action="search_results.php" style="display: inline-block; float: right; margin: 10px;">
        <input type="text" name="query" placeholder="검색어를 입력하세요" required style="padding: 5px; border-radius: 5px; border: 1px solid #ccc;">
        <button type="submit" style="padding: 5px 10px; border-radius: 5px; background-color: #007aff; color: #fff; border: none; cursor: pointer;">
            검색
        </button>
    </form>


    <!-- 카테고리 버튼 -->
    <div class="category-container">
        <button onclick="showProducts('iPhone')">
            <img src="/project/shop/images/iPhone.png" alt="iPhone">
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
            <div class="color-selectors">
                <button class="color-btn black" onclick="showImage('black-img', 'black', 'Black Titanium')"></button>
                <button class="color-btn white" onclick="showImage('white-img', 'white', 'White Titanium')"></button>
                <button class="color-btn gold" onclick="showImage('gold-img', 'desert', 'Desert Titanium')"></button>
                <button class="color-btn silver" onclick="showImage('silver-img', 'natural', 'Natural Titanium')"></button>
            </div>


            <p class="selected-color">선택된 색상: <span id="selectedColor">None</span></p>

            <div class="options">
                <div class="option-group">
                    <label for="model">모델</label>
                    <select id="model" onchange="updatePrice()">
                        <option value="1550000">iPhone 16 Pro (₩1,550,000)</option>
                        <option value="1900000">iPhone 16 Pro Max (₩1,900,000)</option>
                    </select>
                </div>

                <div class="option-group">
                    <label for="storage">저장 용량</label>
                    <select id="storage" onchange="updatePrice()">
                        <option value="0">128GB (+₩0)</option>
                        <option value="40000">256GB (+₩40,000)</option>
                        <option value="75000">512GB (+₩75,000)</option>
                        <option value="150000">1TB (+₩150,000)</option>
                    </select>
                </div>

                <p class="price-display">총 금액 : ₩ <span id="totalPrice">1,550,000</span></p>
            </div>

            <div class="color-options">
                <form action="purchase.php" method="POST" id="purchaseForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="productName" id="formProductName">
                    <input type="hidden" name="selectedColor" id="formSelectedColor">
                    <input type="hidden" name="storage" id="formStorage">
                    <input type="hidden" name="additionalPrice" id="formAdditionalPrice">
                    <input type="hidden" name="quantity" value="1">
                    <button type="button" onclick="preparePurchase()">Buy</button>
                </form>
                <form action="insert.php" method="POST" id="cartForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="productName" id="formCartProductName">
                    <input type="hidden" name="selectedColor" id="formCartSelectedColor">
                    <input type="hidden" name="storage" id="formCartStorage">
                    <input type="hidden" name="additionalPrice" id="formCartAdditionalPrice">
                    <input type="hidden" name="quantity" value="1">
                    <button type="button" onclick="prepareCart()">Cart</button>
                </form>
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
       // 페이지 로드 시 초기 설정
        document.addEventListener("DOMContentLoaded", () => {
            showProducts("iPhone");
            setProductName(); // 초기 상품 이름 설정
        });

        // 선택한 상품 이름 동적으로 설정
        function setProductName() {
            const modelElement = document.getElementById('model');
            const selectedModel = modelElement.options[modelElement.selectedIndex].text;

            // 모델 이름에서 상품 이름만 추출
            const productName = selectedModel.split('(')[0].trim();
            document.getElementById('formProductName').value = productName;
        }

        document.getElementById('model').addEventListener('change', setProductName);


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

        function showImage(colorId, color, colorName) {
            // 모든 이미지 숨기기
            document.querySelectorAll('.iphone-gallery img').forEach(img => {
                img.style.display = 'none';
                img.classList.remove('fade-in');
            });
        
            // 선택된 이미지 표시
            const selectedImg = document.getElementById(colorId);
            if (selectedImg) {
                selectedImg.style.display = 'block';
                setTimeout(() => {
                    selectedImg.classList.add('fade-in');
                }, 0);
            }
        
            // 메인 이미지 변경
            const productImg = document.getElementById("productImg");
            const images = {
                black: "/project/shop/images/iPhone/black.png",
                natural: "/project/shop/images/iPhone/natural.png",
                white: "/project/shop/images/iPhone/white.png",
                desert: "/project/shop/images/iPhone/desert.png",
            };
            if (color && productImg) {
                productImg.src = images[color];
            }
        
            // 선택된 색상 업데이트
            const selectedColorElement = document.getElementById("selectedColor");
            selectedColorElement.textContent = colorName;
        }


        // 가격 업데이트
        function updatePrice() {
            const modelPrice = parseInt(document.getElementById('model').value); // 모델 가격
            const storagePrice = parseInt(document.getElementById('storage').value); // 저장 용량 추가 가격
        
            // 총 금액 계산
            const totalPrice = modelPrice + storagePrice;
        
            // 표시 업데이트
            document.getElementById('totalPrice').textContent = totalPrice.toLocaleString();
        }

        // 구매 처리
        function preparePurchase() {
            // 선택된 옵션 가져오기
            const selectedColor = document.getElementById('selectedColor').textContent;
            const storage = document.getElementById('storage').options[document.getElementById('storage').selectedIndex].text;
            const additionalPrice = parseInt(document.getElementById('storage').value);

            // 폼 데이터 설정
            document.getElementById('formSelectedColor').value = selectedColor;
            document.getElementById('formStorage').value = storage;
            document.getElementById('formAdditionalPrice').value = additionalPrice;

            // 폼 제출
            document.getElementById('purchaseForm').submit();
        }

        function prepareCart() {
            // 선택된 옵션 가져오기
            const selectedColor = document.getElementById('selectedColor').textContent;
            const storage = document.getElementById('storage').options[document.getElementById('storage').selectedIndex].text;
            const additionalPrice = parseInt(document.getElementById('storage').value);
            const productName = document.getElementById('model').options[document.getElementById('model').selectedIndex].text.split('(')[0].trim();

            // 폼 데이터 설정
            document.getElementById('formCartProductName').value = productName;
            document.getElementById('formCartSelectedColor').value = selectedColor;
            document.getElementById('formCartStorage').value = storage;
            document.getElementById('formCartAdditionalPrice').value = additionalPrice;

            // 폼 제출
            document.getElementById('cartForm').submit();
        }
    </script>
</body>
</html>
