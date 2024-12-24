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

    <div>
        <h1>현대오토에버</h1>
        <p class="sub-title">SW 모빌티리 스쿨</p>
    </div>
    <div>
        <h1>모의해킹 프로젝트</h1>
        <p class="sub-title">웹 취약점 진단 및 모의해킹</p>
    </div>
    <div>
        <h1>Made By 2Team</h1>
    </div>
    <div><img src="./images/hyundai.png" alt="hyundai"></div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1; // 요소 보이기
                        observer.unobserve(entry.target); // 애니메이션 후 관찰 중지
                    }
                    else {
                        entry.target.style.opacity = 0;
                    }
                });
            });

            const divs = document.querySelectorAll('div'); // 모든 div 선택
            divs.forEach(div => observer.observe(div)); // 각 div 관찰
        });
    </script>
</body> 
</html>
