<?php
include "../memberboard/session.php"; // 세션 포함
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>회원 정보 수정</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .join_form .col2 {
    flex: 2;
    display: flex;
    align-items: center;
    justify-content: flex-start; /* 왼쪽 정렬 */
}

.join_form .col2 span {
    display: inline-block;
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    width: 100%; /* 입력 필드와 동일한 너비로 설정 */
    box-sizing: border-box;
}

    </style>
    <script>
        function check_input() {
            const form = document.member;

            if (!form.pass.value) {
                alert("비밀번호를 입력하세요!");
                form.pass.focus();
                return;
            }
            if (!form.pass_confirm.value) {
                alert("비밀번호 확인을 입력하세요!");
                form.pass_confirm.focus();
                return;
            }
            if (form.pass.value !== form.pass_confirm.value) {
                alert("비밀번호가 일치하지 않습니다!");
                form.pass.focus();
                return;
            }
            form.submit();
        }

        function reset_form() {
            const form = document.member;
            form.pass.value = "";
            form.pass_confirm.value = "";
            form.name.value = "";
            form.email.value = "";
            form.pass.focus();
        }

        function go_back() {
            window.history.back();
        }
    </script>
</head>
<body>
    <?php include "header.php"; ?>

    <?php
    // 데이터베이스 연결
    $config = require '../config.php';
    $con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
    if (!$con) {
        die("데이터베이스 연결 실패: " . mysqli_connect_error());
    }

    // Prepared Statement를 사용하여 SQL 인젝션 방지
    $stmt = $con->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("로그인을 해주세요.");
    }

    $row = $result->fetch_assoc();
    $pass = htmlspecialchars($row["pass"], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($row["email"], ENT_QUOTES, 'UTF-8');
    $points = htmlspecialchars($row["points"], ENT_QUOTES, 'UTF-8');

    $stmt->close();
    mysqli_close($con);
    ?>

    <div class="container">
        <form name="member" action="modify.php?id=<?= htmlspecialchars($userid, ENT_QUOTES, 'UTF-8') ?>" method="post">
            <h2>회원 정보 수정</h2>
            <ul class="join_form">
                <li>
                    <span class="col1" style="font-weight: bold;">아이디</span>
                    <span class="col2"><?= htmlspecialchars($userid, ENT_QUOTES, 'UTF-8') ?></span>
                </li>
                <li>
                    <span class="col1" style="font-weight: bold;">비밀번호</span>
                    <span class="col2"><input type="password" name="pass"></span>
                </li>
                <li>
                    <span class="col1" style="font-weight: bold;">비밀번호 확인</span>
                    <span class="col2"><input type="password" name="pass_confirm"></span>
                </li>
                <li>
                    <span class="col1" style="font-weight: bold;">이름</span>
                    <span class="col2"><input type="text" name="name" value="<?= $name ?>"></span>
                </li>
                <li>
                    <span class="col1" style="font-weight: bold;">이메일</span>
                    <span class="col2"><input type="text" name="email" value="<?= $email ?>"></span>
                </li>
                <li>
                    <span class="col1" style="font-weight: bold;">포인트</span>
                    <span class="col2">₩ <?= number_format($points) ?></span>
                </li>
                    <ul class="buttons">
                    <button type="button" onclick="check_input()">저장하기</button>
                    <button type="button" onclick="reset_form()">지우기</button>
                    <button type="button" onclick="go_back()">나가기</button>
                </ul>
            </ul>
        </form>
    </div>
</body>
</html>
