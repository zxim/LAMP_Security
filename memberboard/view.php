<?php
include "session.php"; // 세션 처리

$num  = isset($_GET["num"]) ? intval($_GET["num"]) : 0; // 게시글 번호
$page  = isset($_GET["page"]) ? intval($_GET["page"]) : 1; // 페이지 번호

$config = require '../config.php'; // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

// 게시글 가져오기
$sql = "SELECT * FROM memberboard WHERE num = $num";
$result = mysqli_query($con, $sql);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "<script>alert('해당 게시글을 찾을 수 없습니다.'); history.back();</script>";
    exit;
}
$row = mysqli_fetch_assoc($result);

// 게시글 데이터
$id = $row["id"]; // 작성자 ID
$name = $row["name"]; // 작성자 이름
$subject = $row["subject"]; // 제목
$regist_day = $row["regist_day"]; // 작성일
$content = nl2br($row["content"]); // 내용

// 파일 정보
$file_name = $row["file_name"];
$file_type = $row["file_type"];
$file_copied = $row["file_copied"];

// 현재 로그인한 사용자의 ID
$userid = isset($_SESSION["userid"]) ? $_SESSION["userid"] : "";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>오토에버</title>
<link rel="stylesheet" href="style.css">
<script>
    function checkLoginBeforeWrite() {
        var loggedIn = "<?= $userid ?>";
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
<?php include "../login/header.php"; ?> 
<h2><a href="../login/index.php">홈</a>
        <span style="margin-left: 50px;"></span>
        회원 게시판 > 내용보기
    </h2>
    <ul class="board_view">
        <li class="row1">
            <span class="col1"><b>제목 :</b> <?= $subject ?></span> <!-- 제목 출력 -->
            <span class="col2"><?= $name ?> | <?= $regist_day ?></span> <!-- 이름, 작성일 출력 -->
        </li>
        <li class="row2">
        <?php
            if ($file_name) {
                $file_path = "./data/" . $file_copied;
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
            echo $content; // 글 내용 출력 (XSS 허용)
        ?>
        </li>       
    </ul>
    <ul class="buttons">
        <li><button onclick="location.href='list.php?page=<?= $page ?>'">목록보기</button></li>
        <li><button onclick="location.href='modify_form.php?num=<?= $num ?>&page=<?= $page ?>'">수정하기</button></li>
        <li><button onclick="location.href='delete.php?num=<?= $num ?>&page=<?= $page ?>'">삭제하기</button></li>
        <li><button onclick="checkLoginBeforeWrite()">글쓰기</button></li>
    </ul>

    <!-- 댓글 입력창 -->
	<div class="comments">
        <h3>댓글 작성</h3>
        <form method="post" action="comment_process.php" class="comment-form">
            <textarea name="comment_content" placeholder="댓글을 작성해주세요!" required></textarea>
            <input type="hidden" name="num" value="<?= $num ?>"> <!-- 게시글 번호 -->
            <button type="submit" class="comment-btn">댓글 작성</button>
        </form>
    </div>
    <!-- 댓글 목록 -->
    <div class="comment-list">
        <h3>댓글 목록</h3>
        <?php
        // 댓글 가져오기
        $sql = "SELECT c.comment_id, c.content, c.regist_day, m.name AS user_name
                FROM comments AS c
                JOIN members AS m ON c.member_id = m.num
                WHERE c.board_num = $num
                ORDER BY c.comment_id DESC";
        $result = mysqli_query($con, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($comment_row = mysqli_fetch_assoc($result)) {
                $comment_id = $comment_row['comment_id'];
                $comment_content = $comment_row['content'];
                $comment_date = $comment_row['regist_day'];
                $comment_user = $comment_row['user_name'];
        ?>
        <div class="comment-item">
            <p><b><?= $comment_user ?></b> | <?= $comment_date ?></p>
            <p><?= nl2br($comment_content) ?></p>
            <?php if ($userid === $id) { // 댓글 작성자만 삭제 가능 ?>
            <form method="post" action="delete_comment.php" class="comment-delete-form">
                <input type="hidden" name="comment_id" value="<?= $comment_id ?>">
                <input type="hidden" name="num" value="<?= $num ?>">
                <button type="submit" class="delete-btn">삭제</button>
            </form>
            <?php } ?>
        </div>
        <hr>
        <?php
            }
        } else {
            echo "<p>작성된 댓글이 없습니다.</p>";
        }
        ?>
    </div>
</body>
</html>
