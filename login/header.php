<header class="header">
    <div class="container">
        <h3 class="logo"> 
            <ul>
                <span style="font-size: 20px; font-weight: bold;">HYUNDAI</span><br>
                <span style="font-size: 20px; font-weight: bold;">Autoever</span>
            </ul>
            <a href="/project/login/index.php">홈</a>
            <a href="/project/memberboard/list.php" class="memberboard-btn">게시판</a>

        </h3> 
        <nav class="top">
<?php 
    if(!$userid) { 
?>                 
            <a href="/project/login/form.php" class="btn">회원가입</a>
            <a href="/project/login/login_form.php" class="btn">로그인</a>
<?php 
    } else { 
        $logged = $username."(".$userid.")"; 
?> 
            <span class="logged"><?=$logged?> </span>
            <a href="/project/login/logout.php" class="btn">로그아웃</a>
            <a href="/project/login/modify_form.php" class="btn">정보수정</a>
<?php 
    } 
?> 
        </nav> 
    </div>
</header>
