<?php
include "session.php"; // 세션 처리

// CSRF 방지 토큰 생성
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html>
<head> 
<meta charset="utf-8">
<title>오토에버</title>
<link rel="stylesheet" href="style.css">
<script>
  	function check_input() {	
      	if (!document.board.subject.value) {	// 제목 체크
          	alert("제목을 입력하세요!");
          	document.board.subject.focus();
          	return;
		}
      	if (!document.board.content.value) {	// 내용 체크
          	alert("내용을 입력하세요!");    
          	document.board.content.focus();
          	return;
      	}
	  	document.board.submit();
   	}
</script>
</head>
<body>
<?php include "../login/header.php"; ?> 
	<form name="board" method="post" action="insert.php" enctype="multipart/form-data" style="margin-top: 30px";>
	    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
	    <ul class="board_form">
			<li>
				<span class="col1">이름 : </span>
				<span class="col2"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></span>
			</li>					
	    	<li>
	    		<span class="col1">제목</span>
	    		<span class="col2"><input name="subject" type="text"></span>
	    	</li>	    	
	    	<li class="area">	
	    		<span class="col1">내용</span>
	    		<span class="col2">
	    			<textarea name="content"></textarea>
	    		</span>
	    	</li>
			<li>
			    <span class="col1">첨부 파일</span>
			    <span class="col2">
			        <label for="file-upload" class="file-upload-label">파일 선택</label>
			        <span class="file-upload-text" id="file-upload-text">선택된 파일 없음</span>
			        <input type="file" name="upfile" id="file-upload">
			    </span>
			</li>
			<li>
				<span class="col1">비밀번호</span>
				<span class="col2"><input type="password" name="password" id="password"></span>
			</li>			
	    </ul>
	    <ul class="buttons">
			<li><button type="button" class="btn" onclick="check_input()">저장하기</button></li>
			<li><button type="button" class="btn" onclick="location.href='list.php'">목록보기</button></li>
		</ul>
	</form>

	<!-- 파일 선택 후 파일명을 표시하는 스크립트 -->
	<script>
		document.getElementById('file-upload').addEventListener('change', function() {
		    var fileName = this.files[0] ? this.files[0].name : '선택된 파일 없음';
		    document.getElementById('file-upload-text').textContent = fileName;
		});
	</script>
</body>
</html>
