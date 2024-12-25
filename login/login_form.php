<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>로그인</title>
<link rel="stylesheet" type="text/css" href="./css/style.css">
<style>
	/* 공통 설정 */
body {
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
    font-family: 'Noto Sans', Arial, sans-serif;
    color: #333;
}

h2 {
    text-align: center;
    color: #333;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

/* 폼 컨테이너 */
.form-container {
    width: 100%;
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* 폼 그룹 */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #666;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group input:focus {
    border-color: #333;
    outline: none;
}

/* 버튼 스타일 */
.form-actions {
    text-align: center;
    margin-top: 20px;
}

.form-actions button {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn {
    background-color: #333;
    color: #fff;
}

.btn:hover {
    background-color: #fff;
    color: #333;
    border: 1px solid #333;
}


</style>
<script>
    function check_input() {
        const form = document.login;
        if (!form.id.value) {
            alert("아이디를 입력하세요.");
            form.id.focus();
            return false;
        }
        if (!form.pass.value) {
            alert("비밀번호를 입력하세요.");
            form.pass.focus();
            return false;
        }
        form.submit();
    }

    function go_back() {
        window.history.back();
    }
</script>	
</head>
<body>
    <?php include "header.php"; ?>

    <!-- 로그인 폼 -->
    <div class="form-container">
        <h2>로그인</h2>
        <form name="login" method="post" action="login.php">		       	
            <div class="form-group">
                <label for="id">아이디</label>
                <input type="text" id="id" name="id" placeholder="아이디를 입력하세요">
            </div>
            <div class="form-group">
                <label for="pass">비밀번호</label>
                <input type="password" id="pass" name="pass" placeholder="비밀번호를 입력하세요">
            </div>
            <div class="form-actions">
                <button type="button" class="btn" onclick="check_input()">로그인</button>
                <button type="button" class="btn" onclick="go_back()">나가기</button>
            </div>
        </form>
    </div>
</body>
</html>
