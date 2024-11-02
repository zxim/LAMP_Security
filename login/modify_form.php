<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>오토에버 불여조</title>
    <!-- Link the CSS file -->
    <link rel="stylesheet" href="./css/style.css">
    <script>
        function check_input() {
            if (!document.member.pass.value) {
                alert("비밀번호를 입력하세요!");    
                document.member.pass.focus();
                return;
            }
            if (!document.member.pass_confirm.value) {
                alert("비밀번호확인을 입력하세요!");    
                document.member.pass_confirm.focus();
                return;
            }
            if (!document.member.name.value) {
                alert("이름을 입력하세요!");    
                document.member.name.focus();
                return;
            }
            if (!document.member.email.value) {
                alert("이메일 주소를 입력하세요!");    
                document.member.email.focus();
                return;
            }
            if (document.member.pass.value != document.member.pass_confirm.value) {
                alert("비밀번호가 일치하지 않습니다.\n다시 입력해 주세요!");
                document.member.pass.focus();
                document.member.pass.select();
                return;
            }
            document.member.submit();
        }

        function reset_form() {
            document.member.id.value = "";  
            document.member.pass.value = "";
            document.member.pass_confirm.value = "";
            document.member.name.value = "";
            document.member.email.value = "";
            document.member.id.focus();
            return;
        }

        function go_back() {
            window.history.back(); // 이전 페이지로 돌아가기
        }
    </script>
</head>
<body>
    <?php 
        session_start();
        if (isset($_SESSION["userid"])) 
            $userid = $_SESSION["userid"];
        else 
            $userid = "";

        $config = require '../config.php';  // 루트에 있는 config.php 파일 불러옴
        // config.php에서 가져온 정보를 변수에 저장
        $db_host = $config['DB_HOST'];
        $db_user = $config['DB_USER'];
        $db_password = $config['DB_PASSWORD'];
        $db_name = $config['DB_NAME'];


        $con = mysqli_connect($db_host, $db_user, $db_password, $db_name);
        $sql    = "select * from members where id='$userid'";
        $result = mysqli_query($con, $sql);
        $row    = mysqli_fetch_assoc($result);

        $pass = $row["pass"];
        $name = $row["name"];
        $email = $row["email"];

        mysqli_close($con);
    ?>    

    <div class="container">
        <form name="member" action="modify.php?id=<?=$userid?>" method="post">
            <h2>회원 정보 수정</h2>
            <ul class="join_form">
                <li>
                    <span class="col1">아이디</span>
                    <span class="col2"><?=$userid?></span>                
                </li>
                <li>
                    <span class="col1">비밀번호</span>
                    <span class="col2"><input type="password" name="pass" value="<?=$pass?>"></span>               
                </li>
                <li>
                    <span class="col1">비밀번호 확인</span>
                    <span class="col2"><input type="password" name="pass_confirm"></span>               
                </li>            
                <li>
                    <span class="col1">이름</span>
                    <span class="col2"><input type="text" name="name" value="<?=$name?>"></span>               
                </li>
                <li>
                    <span class="col1">이메일</span>
                    <span class="col2"><input type="text" name="email" value="<?=$email?>"></span>               
                </li>                        
            </ul>                       

            <ul class="buttons">
                <li><button class="btn" type="button" onclick="check_input()">저장하기</button></li>
                <li><button class="btn" type="button" onclick="reset_form()">지우기</button></li>
                <li><button class="btn" type="button" onclick="go_back()">나가기</button></li>
            </ul>
        </form>
    </div>
</body>
</html>
