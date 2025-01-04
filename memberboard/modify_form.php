<?php
include "session.php"; // 세션 처리

$num = isset($_GET["num"]) ? intval($_GET["num"]) : 0; // 게시글 번호
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1; // 페이지 번호

$config = require '../config.php'; // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

// Prepared Statement를 사용하여 SQL 쿼리 실행
$sql = "SELECT id, name, subject, content, file_name FROM memberboard WHERE num = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $num);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('해당 게시글을 찾을 수 없습니다.'); location.href = 'list.php?page=$page';</script>";
    exit;
}

$row = $result->fetch_assoc();

// 현재 로그인한 사용자와 게시글 작성자 비교
if (!isset($_SESSION["userid"]) || $_SESSION["userid"] !== $row["id"]) {
    echo "<script>alert('수정 권한이 없습니다.'); location.href = 'list.php?page=$page';</script>";
    exit;
}

$name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8'); // 이름
$subject = htmlspecialchars($row["subject"], ENT_QUOTES, 'UTF-8'); // 제목
$content = htmlspecialchars($row["content"], ENT_QUOTES, 'UTF-8'); // 내용
$file_name = htmlspecialchars($row["file_name"], ENT_QUOTES, 'UTF-8'); // 파일 이름

$stmt->close();
mysqli_close($con);
?>	
<!DOCTYPE html>
<html lang="ko">
<head> 
    <meta charset="utf-8">
    <title>게시판</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function check_input() {		
            if (!document.board.subject.value) { // 제목 체크
                alert("제목을 입력하세요!");
                document.board.subject.focus();
                return;
            }
            if (!document.board.content.value) { // 내용 체크
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
    <form name="board" method="post" action="/project/memberboard/modify.php?num=<?= $num ?>&page=<?= $page ?>" style="margin-top: 30px;">
        <ul class="board_form">
            <li>
                <span class="col1" style="font-weight: bold;">이름 : </span>
                <span class="col2" style="font-weight: bold;"><?= $name ?></span>
            </li>			
            <li>
                <span class="col1" style="font-weight: bold;">제목 : </span>
                <span class="col2"><input name="subject" type="text" style="font-weight: bold;" value="<?= $subject ?>" ></span>
            </li>	    	
            <li class="area">	
                <span class="col1" style="font-weight: bold;">내용 : </span>
                <span class="col2">
                    <textarea name="content"><?= $content ?></textarea>
                </span>
            </li>
            <li>
                <span class="col1" style="font-weight: bold;">첨부 파일 : </span>
                <span class="col2"><?= $file_name ?></span>
            </li>	
        </ul>
        <ul class="buttons">
            <li><button type="button" onclick="check_input()">저장하기</button></li>
            <li><button type="button" onclick="location.href='list.php'">목록보기</button></li>
        </ul>
    </form>
</body>
</html>
