<?php
	include "session.php"; 	// 세션 처리

	$num  = $_GET["num"];
	$page = $_GET["page"];

	$con = mysqli_connect("localhost", "min", "guseodhxhdpqj@", "project");
	$sql = "select * from memberboard where num=$num";	// 레코드 검색
	$result = mysqli_query($con, $sql);		// SQL 명령 실행

	$row = mysqli_fetch_assoc($result);

	$name    	= $row["name"];			// 이름
	$subject    = $row["subject"];		// 제목
	$content    = $row["content"];		// 내용
	$regist_day = date("Y-m-d (H:i)");  // UTC 기준 현재 '년-월-일 (시:분)'
	$file_name  = $row["file_name"];

	mysqli_close($con);
?>	
<!DOCTYPE html>
<html>
<head> 
<meta charset="utf-8">
<title>오토에버 불여조</title>
<link rel="stylesheet" href="style.css">
<script>
  	function check_input() {		
      	if (!document.board.subject.value) {		// 제목 체크
          	alert("제목을 입력하세요!");
          	document.board.subject.focus();
          	return;
		}
      	if (!document.board.content.value) {		// 내용 체크
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
		회원 게시판 > 글 수정하기
	</h2>
	<form name="board" method="post" action="modify.php?num=<?=$num?>&page=<?=$page?>">
	    <ul class="board_form">
			<li>
				<span class="col1">이름 : </span>
				<span class="col2"><?=$name?></span>
			</li>			
	    	<li>
	    		<span class="col1">제목 : </span>
	    		<span class="col2"><input name="subject" type="text" value="<?=$subject?>"></span>
	    	</li>	    	
	    	<li class="area">	
	    		<span class="col1">내용 : </span>
	    		<span class="col2">
	    			<textarea name="content"><?=$content?></textarea>
	    		</span>
	    	</li>
			<li>
			        <span class="col1"> 첨부 파일 : </span>
			        <span class="col2"><?=$file_name?></span>
			</li>	
	    </ul>
	    <ul class="buttons">
			<li><button type="button" onclick="check_input()">저장하기</button></li>
			<li><button type="button" onclick="location.href='list.php'">목록보기</button></li>
		</ul>
	</form>
</body>
</html>
