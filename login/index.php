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
        <h1>현대오토에버</h1>
        <p class="sub-title">SW 모빌티리 스쿨</p>
    </div>
    <div class="title">
        <h1>모의해킹 프로젝트</h1>
        <p class="sub-title">웹 취약점 진단 및 모의해킹</p>
    </div>
    <div class="title" style="margin-bottom: 200px;">
        <h1>2조의 취약한 웹사이트</h1>
        <p class="sub-title">지금 바로 만나보세요</p>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1; // 요소 보이기
                        entry.target.style.transform = 'translateY(0)'; // 제자리 이동
                    } else {
                        entry.target.style.opacity = 0; // 요소 숨기기
                        entry.target.style.transform = 'translateY(50px)'; // 아래로 이동
                    }
                });
            });
        
            const divs = document.querySelectorAll('.title'); // 모든 .title 선택
            divs.forEach(div => observer.observe(div)); // 각 .title 관찰
        });

    </script>
</body> 
</html>
