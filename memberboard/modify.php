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

$upload_dir = './data/'; // 업로드 디렉토리

// 파일 업로드 처리
$upfile_name = $_FILES["upfile"]["name"];
$upfile_tmp_name = $_FILES["upfile"]["tmp_name"];
$upfile_error = $_FILES["upfile"]["error"];

if ($upfile_name && !$upfile_error) {
    $uploaded_file = $upload_dir . $upfile_name; // 파일명을 그대로 사용

    // **취약점: 파일 크기 제한 없음, 확장자 검증 없음**
    if (!move_uploaded_file($upfile_tmp_name, $uploaded_file)) {
        echo "<script>
        alert('파일 업로드에 실패했습니다.');
        history.back();  // 업로드 실패 시 이전 페이지로 이동
        </script>";
        exit();
    }
} else {
    $upfile_name = ""; // 파일이 없는 경우 처리
}

$config = require '../config.php';  // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);

if (!$con) {
    echo "<script>
        alert('DB 연결에 실패하였습니다.');
        location.href = 'list.php?page=$page';  // DB 연결 실패 시 목록 페이지로 이동
    </script>";
    exit();
}

// 글 수정 쿼리 (Prepared statement 사용)
$sql = "UPDATE memberboard SET subject = ?, content = ?, regist_day = ?, file_name = ? WHERE num = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ssssi", $subject, $content, $regist_day, $upfile_name, $num);
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
