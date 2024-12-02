<?php
include "session.php"; 	// 세션 처리

// 로그인 여부 확인
if (!isset($_SESSION["userid"]) || empty($_SESSION["userid"])) {
    echo "<script>
        alert('로그인이 필요합니다.');
        location.href = 'login_form.php';  // 로그인하지 않았으면 로그인 페이지로 이동
    </script>";
    exit();
}

$userid = $_SESSION["userid"];  // 현재 로그인한 사용자의 ID
$num = isset($_GET["num"]) ? intval($_GET["num"]) : 0;  // 게시글 번호
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;  // 페이지 번호

// 제목과 내용이 입력되었는지 확인
if (empty($_POST["subject"]) || empty($_POST["content"])) {
    echo "<script>
        alert('제목과 내용을 모두 입력해주세요.');
        history.back();  // 제목과 내용이 비어있을 경우 이전 페이지로 이동
    </script>";
    exit();
}

$subject = htmlspecialchars($_POST["subject"], ENT_QUOTES); // HTML 특수문자 처리
$content = htmlspecialchars($_POST["content"], ENT_QUOTES); // HTML 특수문자 처리
$regist_day = date("Y-m-d (H:i)");  // 수정 시간

$config = require '../config.php';  // 루트에 있는 config.php 파일 불러옴

// config.php에서 가져온 정보를 변수에 저장
$db_host = $config['DB_HOST'];
$db_user = $config['DB_USER'];
$db_password = $config['DB_PASSWORD'];
$db_name = $config['DB_NAME'];

// DB 연결
$con = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$con) {
    echo "<script>
        alert('DB 연결에 실패하였습니다.');
        location.href = 'list.php?page=$page';  // DB 연결 실패 시 목록 페이지로 이동
    </script>";
    exit();
}

// 글 수정 쿼리 (Prepared statement 사용)
$sql = "UPDATE memberboard SET subject = ?, content = ?, regist_day = ? WHERE num = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "sssi", $subject, $content, $regist_day, $num);
$execute_result = mysqli_stmt_execute($stmt);

// 수정 성공 여부 확인
if ($execute_result) {
    echo "<script>
        alert('글이 수정되었습니다.');
        location.href = 'list.php?page=$page';  // 수정 성공 시 목록 페이지로 이동
    </script>";
} else {
    echo "<script>
        alert('글 수정에 실패했습니다. 다시 시도해주세요.');
        location.href = 'list.php?page=$page';  // 수정 실패 시 목록 페이지로 이동
    </script>";
}

// DB 연결 종료
mysqli_stmt_close($stmt);
mysqli_close($con);
?>
