<!DOCTYPE html>
<html lang="ko">
<head> 
    <meta charset="utf-8">
    <title>회원가입</title>
    <style>
    /* 기본 스타일 */
    body {
        margin: 0;
        padding: 0;
        background-color: #f5f5f5; /* 배경 회색 */
        font-family: 'Noto Sans', Arial, sans-serif;
        color: #333;
    }

    h2 {
        text-align: center;
        color: #333;
        font-size: 28px;
        font-weight: bold;
        margin: 20px 0;
    }

    /* 폼 컨테이너 스타일 */
    .join_form {
        width: 90%;
        max-width: 600px; /* 박스를 넓게 조정 */
        margin: 20px auto;
        padding: 30px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4); /* 그림자 추가 */
    }

    .join_form li {
        display: flex;
        align-items: center;
        justify-content: space-between; /* 입력 필드와 레이블 간격 균일화 */
        margin-bottom: 20px; /* 필드 간 간격 */
        position: relative;
    }

    .join_form .col1 {
        flex: 1;
        color: #333;
        font-weight: bold;
        text-align: right; /* 레이블 오른쪽 정렬 */
        margin-right: 20px; /* 레이블과 입력 필드 간격 */
    }

    .join_form .col2 {
        flex: 3;
        position: relative;
    }

    .join_form .col2 input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .join_form .col2 input:focus {
        border-color: #007aff; /* 포커스 시 파란색 테두리 */
        outline: none;
        box-shadow: 0 0 5px rgba(0, 122, 255, 0.3);
    }

    /* 중복체크 버튼 */
    .check-btn {
        position: absolute;
        top: 50%; /* 입력 필드와 겹치도록 위로 올림 */
        right: 10px; /* 오른쪽 여백 조정 */
        transform: translateY(-50%);
        padding: 8px 15px;
        border: #fff;
        border-radius: 5px;
        background-color: #fff; /* 기본 파란색 */
        color: #007aff;
        cursor: pointer;
        font-size: 12px;
        white-space: nowrap;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* 그림자 추가 */
        height: 37px;
    }

    .check-btn:hover {
        background-color: #007aff;
        color: #fff;
        border: 1px solid #007aff;
    }

    /* 버튼 스타일 */
    .buttons {
        text-align: center;
        margin-top: 30px;
    }

    .buttons button {
        padding: 10px 20px;
        border: #fff;
        border-radius: 5px;
        background-color: #fff; 
        color: #007aff;
        font-weight: bold;
        cursor: pointer;
        margin-right: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4); /* 그림자 추가 */
    }

    .buttons button:hover {
        background-color: #007aff;
        color: #fff;
        border: 1px solid #007aff;
        box-shadow: 0 4px 8px rgba(0, 122, 255, 0.4); /* 강조된 그림자 */
    }

    .buttons button:last-child {
        margin-right: 0;
    }
    h2 {
        text-align: center;
        color: #333;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }
</style>
    <script>
        var isIdChecked = false;

        function check_input() {
            const form = document.member;

            if (!form.id.value) {
                alert("아이디를 입력하세요!");
                form.id.focus();
                return;
            }
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
            if (!form.name.value) {
                alert("이름을 입력하세요!");
                form.name.focus();
                return;
            }
            if (!form.email.value) {
                alert("이메일 주소를 입력하세요!");
                form.email.focus();
                return;
            }
            if (form.pass.value !== form.pass_confirm.value) {
                alert("비밀번호가 일치하지 않습니다.");
                form.pass.focus();
                return;
            }
            if (!isIdChecked) {
                alert("아이디 중복 체크를 해주세요.");
                return;
            }
            form.submit();
        }

        function check_id() {
            const id = document.member.id.value;

            if (!id) {
                alert("아이디를 입력하세요!");
                return;
            }

            const popup = window.open(`check_id.php?id=${encodeURIComponent(id)}`, 'checkId', 'width=400,height=300');
            popup.onload = function () {
                isIdChecked = true;
            };
        }

        function reset_form() {
            document.member.reset();
            isIdChecked = false;
        }

        function go_back() {
            window.history.back();
        }
    </script>
</head>
<body>
    <?php include "header.php"; ?>
    <form name="member" action="insert.php" method="post">
        <!-- <h2>회원 가입</h2> -->
        <ul class="join_form">
            <h2>회원 가입</h2>
            <li>
                <span class="col1">아이디</span>
                <div class="col2">
                    <input type="text" name="id">
                    <button type="button" class="check-btn" onclick="check_id()" style="font-weight: bold;">중복체크</button>
                </div>
            </li>
            <li>
                <span class="col1">비밀번호</span>
                <div class="col2">
                    <input type="password" name="pass">
                </div>
            </li>
            <li>
                <span class="col1">비밀번호 확인</span>
                <div class="col2">
                    <input type="password" name="pass_confirm">
                </div>
            </li>
            <li>
                <span class="col1">이름</span>
                <div class="col2">
                    <input type="text" name="name">
                </div>
            </li>
            <li>
                <span class="col1">이메일</span>
                <div class="col2">
                    <input type="text" name="email">
                </div>
            </li>
            <div class="buttons">
                <button type="button" onclick="check_input()">저장하기</button>
                <button type="button" onclick="reset_form()">지우기</button>
                <button type="button" onclick="go_back()">나가기</button>
            </div>
        </ul>
    </form>
</body>
</html>
