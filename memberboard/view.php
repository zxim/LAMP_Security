<?php
include "session.php";  // 세션 처리

$num  = $_GET["num"];
$page  = $_GET["page"];

$config = require '../config.php';  // 루트에 있는 config.php 파일 불러옴

// config.php에서 가져온 정보를 변수에 저장
$db_host = $config['DB_HOST'];
$db_user = $config['DB_USER'];
$db_password = $config['DB_PASSWORD'];
$db_name = $config['DB_NAME'];

$con = mysqli_connect($db_host, $db_user, $db_password, $db_name); // DB 접속
$sql = "select * from memberboard where num=$num"; // 레코드 검색
$result = mysqli_query($con, $sql); // SQL 명령 실행

$row = mysqli_fetch_assoc($result); // 레코드 가져오기

$id      = $row["id"]; // 작성자 ID
$name    = $row["name"]; // 이름
$subject = $row["subject"]; // 제목
$regist_day = $row["regist_day"]; // 작성일
$content = $row["content"]; // 내용

$content = str_replace(" ", "&nbsp;", $content); // 공백 변환
$content = str_replace("\n", "<br>", $content); // 줄바꿈 변환

$file_name    = $row["file_name"];
$file_type    = $row["file_type"];
$file_copied  = $row["file_copied"];

// 현재 로그인한 사용자의 ID
$userid = isset($_SESSION["userid"]) ? $_SESSION["userid"] : "";
?>	
<!DOCTYPE html>
<html>
<head> 
<meta charset="utf-8">
<title>오토에버 붙여조</title>
<link rel="stylesheet" href="style.css">
<script>
    // 로그인 여부 확인하여 글쓰기 페이지로 이동 또는 로그인 페이지로 이동
    function checkLoginBeforeWrite() {
        var loggedIn = "<?= $userid ?>"; // 서버에서 userid 정보 가져오기
        if (!loggedIn) {
            alert("로그인이 필요합니다.");
            location.href = "/project/login/login_form.php"; // 로그인 페이지로 이동
        } else {
            location.href = "form.php"; // 글쓰기 페이지로 이동
        }
    }
</script>
</head>
<body> 
<h2><a href="../login/index.php">홈</a>
		<span style="margin-left: 50px;"></span>
		회원 게시판 > 내용보기
	</h2>
	<ul class="board_view">
		<li class="row1">
			<span class="col1"><b>제목 :</b> <?=$subject?></span> <!-- 제목 출력 -->
			<span class="col2"><?=$name?> | <?=$regist_day?></span> <!-- 이름, 작성일 출력 -->
		</li>
		<li class="row2">
		<?php
			if($file_name) {
				$file_path = "./data/".$file_copied;
				$file_size = filesize($file_path);

				// 이미지 파일 타입 확인
				$img_types = array('image/jpeg', 'image/png', 'image/gif');
				if (in_array($file_type, $img_types)) {
					// 이미지 파일일 경우
					echo "▷ 첨부파일 : $file_name ($file_size Byte) <br>";
					echo "<img src='$file_path' alt='$file_name' style='width: 500px; height: auto;'><br><br>";
				} else {
					// 이미지가 아닌 경우 다운로드 링크 표시
					echo "▷ 첨부파일 : $file_name ($file_size Byte) &nbsp;&nbsp;&nbsp;&nbsp;
			       	<a href='download.php?num=$num&file_copied=$file_copied&file_name=$file_name&file_type=$file_type'>[저장]</a><br><br>";
				}
			}	
			echo $content; // 글 내용 출력
		?>
		</li>		
	</ul>
	<ul class="buttons">
		<li><button onclick="location.href='list.php?page=<?=$page?>'">목록보기</button></li>
		<?php
			// 로그인한 사용자의 ID와 작성자의 ID가 일치하는 경우에만 수정/삭제 버튼을 표시
			if ($userid === $id) {
		?>
			<li><button onclick="location.href='modify_form.php?num=<?=$num?>&page=<?=$page?>'">수정하기</button></li>   
			<li><button onclick="location.href='delete.php?num=<?=$num?>&page=<?=$page?>'">삭제하기</button></li>
		<?php
			} else {
		?>
			<script>
				function alertNotAllowed() {
					alert('본인의 글만 수정/삭제할 수 있습니다.');
				}
			</script>
			<li><button onclick="alertNotAllowed()">수정하기</button></li>
			<li><button onclick="alertNotAllowed()">삭제하기</button></li>
		<?php
			}
		?>
		<!-- 글쓰기 버튼 클릭 시 로그인 여부를 확인 -->
		<li><button onclick="checkLoginBeforeWrite()">글쓰기</button></li>
	</ul>
</body>
</html>
