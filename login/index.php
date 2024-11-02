<?php 
    // 로그인 UI 
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
    <title>오토에버 붙여조</title> 
    <link rel="stylesheet" href="./css/index.css"> 
    <style>
        body, html {
            height: 100%;
            margin: 0;
            scroll-behavior: smooth;
        }
        .video-section {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        .video-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            margin-top: 20px;
        }

        /* 헤더 관련 수정 - 줄어드는 효과 제거 */
        .header {
            background: rgba(15, 0, 114, 0.8);
            padding: 20px;
            color: white;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10;
        }

        .top a.btn {
            margin-left: 10px;
            color: #0f0072;
            text-decoration: none;
            background-color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .top a.btn:hover {
            color: white;
            background-color: #0f0072;
            text-decoration: none;
        }

        /* 흰 화면 섹션 */
        .content-section {
            height: 150vh;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            font-size: 48px;
            font-weight: bold;
            z-index: 1;
        }

        /* 이미지 섹션 (배경 흰색) */
        .image-section {
            height: 120vh;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 0;
            overflow: hidden;
        }

        /* 이미지 초기 상태 */
        .content-image {
            width: 40vw;
            height: auto;
            transition: width 0.9s ease;
        }

    </style>
</head> 
<body> 
    <header class="header"> 
        <div class="container">
            <h3 class="logo"> 
                <ul>
                    <span style="font-size: 20px; font-weight: bold;">HYUNDAI</span><br>
                    <span style="font-size: 20px; font-weight: bold;">Autoever 붙여조</span>
                </ul>
                <a href="index.php">홈</a>
                <a href="../memberboard/list.php" class="memberboard-btn">게시판</a>
                <a href="/project/test/test.php" class="result-btn">진단결과</a>
                <a href="/project/test/history.php" class="result-btn">히스토리</a>
            </h3> 
            <nav class="top">
<?php 
    if(!$userid) { 
?>                 
                <a href="form.php" class="btn">회원가입</a>
                <a href="login_form.php" class="btn">로그인</a>
<?php 
    } else { 
        $logged = $username."(".$userid.")"; 
?> 
                <span class="logged"><?=$logged?> </span>
                <a href="logout.php" class="btn">로그아웃</a>
                <a href="modify_form.php" class="btn">정보수정</a>
<?php 
    } 
?> 
            </nav> 
        </div>
    </header> 

    <!-- 동영상 섹션 -->
    <div class="video-section" id="video-section">
        <video class="video-bg" autoplay muted loop>
            <source src="./videos/background.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- 흰 화면 섹션 -->
    <div class="content-section" id="content-section" style="display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center; flex-direction: column;">
        <br><br><br><br>
        <p style="margin-bottom: 20px;">현대오토에버 SW 모빌리티 스쿨</p>
        <p style="margin-bottom: 40px;">IT 보안 프로젝트</p>
        <p style="font-size: 20px; color: #999;">클라우드 보안 취약점 점검 자동화 도구 개발</p>
        <br><br><br> 
    </div>

    <!-- 이미지가 나오는 섹션-->
    <div class="image-section" id="image-section">
        <img src="./images/sample.png" class="content-image" id="content-image" alt="배경 이미지">
    </div>

    <!-- 스크롤 이벤트 처리 -->
    <script>
        const contentImage = document.getElementById('content-image');
        const imageSection = document.getElementById('image-section');
        const header = document.querySelector('.header');
        const videoSection = document.getElementById('video-section');
        const contentSection = document.getElementById('content-section');

        window.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY + window.innerHeight;
            const imageSectionTop = imageSection.offsetTop;
            const imageSectionHeight = imageSection.offsetHeight;

            // 이미지 크기 조정 로직
            if (scrollPosition > imageSectionTop && scrollPosition <= imageSectionTop + imageSectionHeight) {
                const progress = (scrollPosition - imageSectionTop) / imageSectionHeight;
                const newWidth = 40 + (70 * progress); 
                contentImage.style.width = `${newWidth}vw`;
            }
        });

        // 동영상 섹션에서 흰 화면 섹션으로 자동으로 빠르게 스크롤
        window.addEventListener('wheel', function(event) {
            if (window.scrollY === 0 && event.deltaY > 0) { // 스크롤을 아래로 할 때
                window.scrollTo({
                    top: contentSection.offsetTop,
                    behavior: 'smooth' // 부드럽게 스크롤
                });
            }
        });
    </script>
</body> 
</html>
