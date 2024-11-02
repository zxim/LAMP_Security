<?php
include "session.php"; 	// 세션 처리
?>
<!DOCTYPE html>
<html>
<head> 
<meta charset="utf-8">
<title>오토에버 붙여조</title>
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
<h2><a href="../login/index.php">홈</a>
		<span style="margin-left: 50px;"></span>
		회원 게시판 > 글쓰기
	</h2>
	<form name="board" method="post" action="insert.php" enctype="multipart/form-data">
	    <ul class="board_form">
			<li>
				<span class="col1">이름 : </span>
				<span class="col2"><?=$username?></span>
			</li>					
	    	<li>
	    		<span class="col1">제목 : </span>
	    		<span class="col2"><input name="subject" type="text"></span>
	    	</li>	    	
	    	<li class="area">	
	    		<span class="col1">내용 : </span>
	    		<span class="col2">
	    			<textarea name="content"></textarea>
	    		</span>
	    	</li>
			<li>
			    <span class="col1">첨부 파일:</span>
			    <span class="col2">
			        <label for="file-upload" class="file-upload-label">파일 선택</label>
			        <span class="file-upload-text" id="file-upload-text">선택된 파일 없음</span>
			        <input type="file" name="upfile" id="file-upload">
			    </span>
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
