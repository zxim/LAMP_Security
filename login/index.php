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
    <title>오토에버 로그인</title> 
    <link rel="stylesheet" href="./css/index.css"> 
</head> 
<body> 
    <header class="header"> 
        <div class="container">
            <h3 class="logo"> 
                <a href="index.php">홈</a>
                <span style="margin-left: 50px;"></span>
                <a href="../memberboard/list.php" class="memberboard-btn">게시판</a>
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
</body> 
</html>
