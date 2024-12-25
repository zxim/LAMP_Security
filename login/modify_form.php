<?php
    include "../memberboard/session.php"; // 세션 포함
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>모의해킹</title>
    <link rel="stylesheet" href="./css/style.css">
    <script>
        function check_input() {
            if (!document.member.pass.value) {
                alert("비밀번호를 입력하세요!");
                document.member.pass.focus();
                return;
            }
            if (!document.member.pass_confirm.value) {
                alert("비밀번호 확인을 입력하세요!");
                document.member.pass_confirm.focus();
                return;
            }
            if (document.member.pass.value !== document.member.pass_confirm.value) {
                alert("비밀번호가 일치하지 않습니다!");
                document.member.pass.focus();
                return;
            }
            document.member.submit();
        }

        function reset_form() {
            document.member.pass.value = "";
            document.member.pass_confirm.value = "";
            document.member.name.value = "";
            document.member.email.value = "";
            document.member.pass.focus();
        }

        function go_back() {
            window.history.back();
        }
    </script>
</head>
<body>
    <?php include "header.php" ?>
    <?php
        // 데이터베이스 연결
        $config = require '../config.php';
        $con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
        if (!$con) {
            die("데이터베이스 연결 실패: " . mysqli_connect_error());
        }

        // SQL 쿼리 (취약)
        $sql = "SELECT * FROM members WHERE id='$userid'";
        $result = mysqli_query($con, $sql);
        if (!$result) {
            die("쿼리 실행 실패: " . mysqli_error($con));
        }

        $row = mysqli_fetch_assoc($result);
        if (!$row) {
            die("회원 정보를 찾을 수 없습니다.");
        }

        $pass = $row["pass"];
        $name = $row["name"];
        $email = $row["email"];

        mysqli_close($con);
    ?>
    <div class="container">
        <form name="member" action="modify.php?id=<?= $userid ?>" method="post">
            <h2>회원 정보 수정</h2>
            <ul class="join_form">
                <li>
                    <span class="col1">아이디</span>
                    <span class="col2"><?= $userid ?></span>
                </li>
                <li>
                    <span class="col1">비밀번호</span>
                    <span class="col2"><input type="password" name="pass" value="<?= $pass ?>"></span>
                </li>
                <li>
                    <span class="col1">비밀번호 확인</span>
                    <span class="col2"><input type="password" name="pass_confirm"></span>
                </li>
                <li>
                    <span class="col1">이름</span>
                    <span class="col2"><input type="text" name="name" value="<?= $name ?>"></span>
                </li>
                <li>
                    <span class="col1">이메일</span>
                    <span class="col2"><input type="text" name="email" value="<?= $email ?>"></span>
                </li>
            </ul>
            <ul class="buttons">
                <li><button type="button" onclick="check_input()">저장하기</button></li>
                <li><button type="button" onclick="reset_form()">지우기</button></li>
                <li><button type="button" onclick="go_back()">나가기</button></li>
            </ul>
        </form>
    </div>
</body>
</html> 