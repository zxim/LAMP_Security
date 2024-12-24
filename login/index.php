<?php 
    session_start(); 
    if (isset($_SESSION["userid"]))  
        $userid = $_SESSION["userid"]; 
    else  
        $userid = ""; 
         
    if (isset($_SESSION["username"]))  
        $username = $_SESSION["username"]; 
    else  
        $username = ""; 
?>    
<!DOCTYPE html> 
<html lang="ko"> 
<head>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>모의해킹</title> 
    <link rel="stylesheet" href="./css/index.css">
</head> 
<body> 
    <?php include 'header.php'; ?>

    <div class="title">
        <h1>새로워진 iPhone 16</h1>
        <p class="sub-title">지금 바로 만나보시죠</p>
        <img class="iPhone16" src="./images/iphone_intel.jpg" alt="iPhone16" class="active fade-in" id="black" style="display: block;">
    </div>

    <div class="title">
        <h1>견고함. 아름다움.<br>
            티타늄
        </h1>
    </div>

    <div class="title iphone-section">
        <h1>네 가지 색상</h1>
        <p class="sub-title">블랙 티타늄부터 새로운 데저트 티타늄까지</p>
        <div class="iphone-gallery">
            <img src="./images/iphone_black.jpg" alt="Black iPhone" class="fade-in active" id="black-img">
            <img src="./images/iphone_white.jpg" alt="White iPhone" class="fade-in" id="white-img" style="display: none;">
            <img src="./images/iphone_desert.jpg" alt="Gold iPhone" class="fade-in" id="gold-img" style="display: none;">
            <img src="./images/iphone_natural.jpg" alt="Silver iPhone" class="fade-in" id="silver-img" style="display: none;">
        </div>
        <div class="color-selectors">
            <button class="color-btn black" onclick="showImage('black-img')"></button>
            <button class="color-btn white" onclick="showImage('white-img')"></button>
            <button class="color-btn gold" onclick="showImage('gold-img')"></button>
            <button class="color-btn silver" onclick="showImage('silver-img')"></button>
        </div>
    </div>

    <div class="title">
        <h1>전보다 더 선명해진 카메라</h1>
        <p class="sub-title">초당 120 프레임의 4K Dolby Vision.<br>
        그야말로 시네마틱.</p>
    </div>
    
    <div class="title" style="margin-bottom: 200px;">
    <h1>
        오디오 믹스<br>
        더욱 또렷하게 들리는 목소리.
    </h1>
    <p class="sub-title"><br>
        첨단 지능 및 공간 음향 캡처 기술로 구현되는 ‘오디오 믹스’는<br>
        세 가지 <span class="highlight">비디오 속 음성 재생 방식</span>을 사용해 비디오 속 음성 재생 방식을 조정할 수 있게 해줍니다.<br>
        배경 사운드를 줄이고 싶나요? 아니면 프레임에 잡힌 피사체의 음성에만 집중하고 싶나요?<br>
        비디오를 촬영한 후에 원하는 믹스 방식을 선택하고,<br>
        마음에 드는 사운드가 되도록 강도를 조절하면 된답니다.
    </p>
</div>


    <script>
        function showImage(colorId) {
            // 모든 이미지 숨기기
            document.querySelectorAll('.iphone-gallery img').forEach(img => {
                img.style.display = 'none';
                img.classList.remove('fade-in');
            });

            // 선택된 이미지 표시
            const selectedImg = document.getElementById(colorId);
            selectedImg.style.display = 'block';
            setTimeout(() => {
                selectedImg.classList.add('fade-in');
            }, 0);
        }

        // 기본 검은색 아이폰 표시
        document.addEventListener('DOMContentLoaded', () => {
            showImage('black-img');
        });

        // 텍스트 및 이미지 섹션 애니메이션 기능
        document.addEventListener("DOMContentLoaded", () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    } else {
                        entry.target.style.opacity = 0;
                        entry.target.style.transform = 'translateY(50px)';
                    }
                });
            });

            const elements = document.querySelectorAll('.title, .iphone-gallery img');
            elements.forEach(el => observer.observe(el));
        });
    </script>
</body> 
</html>
