<?php
include "session.php"; // 세션 처리

if (!$userid) {
    echo "
        <script>
        alert('게시판 글쓰기는 로그인 후 이용해 주세요!');
        history.go(-1);
        </script>
    ";
    exit;
}

$username = isset($_SESSION["username"]) ? $_SESSION["username"] : ""; // 세션에서 username 가져오기

$subject = trim($_POST["subject"]);
$content = trim($_POST["content"]);
$password = isset($_POST["password"]) ? $_POST["password"] : null; // 비밀번호 가져오기

if (empty($subject) || empty($content)) {
    echo "<script>
        alert('제목과 내용을 입력해주세요.');
        history.go(-1);
        </script>";
    exit;
}

$regist_day = date("Y-m-d (H:i)");

$upload_dir = './data/';

// 파일 정보
$upfile_name = $_FILES["upfile"]["name"];
$upfile_tmp_name = $_FILES["upfile"]["tmp_name"];
$upfile_type = $_FILES["upfile"]["type"];
$upfile_error = $_FILES["upfile"]["error"];

$copied_file_name = "";

// 허용된 파일 확장자 목록
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'doc', 'docx', 'xlsx'];

if ($upfile_name && !$upfile_error) {
    $file_ext = strtolower(pathinfo($upfile_name, PATHINFO_EXTENSION)); // 파일 확장자 추출 (소문자로 변환)

    // 확장자 검사 및 이중 확장자 방지
    if (substr_count($upfile_name, '.') > 1 || !in_array($file_ext, $allowed_extensions)) {
        echo "<script>
        alert('허용되지 않은 파일 형식입니다.');
        history.go(-1);
        </script>";
        exit;
    }

    // 고유 파일 이름 생성
    $unique_file_name = md5(uniqid(rand(), true)) . "." . $file_ext;
    $uploaded_file = $upload_dir . $unique_file_name;

    if (!move_uploaded_file($upfile_tmp_name, $uploaded_file)) {
        echo "<script>
        alert('파일 업로드에 실패했습니다.');
        history.go(-1);
        </script>";
        exit;
    }
    $copied_file_name = $unique_file_name;
} else {
    $upfile_name = "";
    $upfile_type = "";
    $copied_file_name = "";
}

$config = require '../config.php';

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);

if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "');</script>";
    exit;
}

// 트랜잭션 시작
mysqli_begin_transaction($con);

try {
    // 데이터 삽입 (게시글 저장)
    $sql = "INSERT INTO memberboard (id, name, subject, content, password, regist_day, file_name, file_type, file_copied)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssss", $userid, $username, $subject, $content, $password, $regist_day, $upfile_name, $upfile_type, $copied_file_name);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("게시글 저장 오류: " . mysqli_stmt_error($stmt));
    }

    // 포인트 지급 (100점)
    $update_sql = "UPDATE members SET points = points + 100 WHERE id = ?";
    $update_stmt = mysqli_prepare($con, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "s", $userid);

    if (!mysqli_stmt_execute($update_stmt)) {
        throw new Exception("포인트 지급 오류: " . mysqli_stmt_error($update_stmt));
    }

    // 트랜잭션 커밋
    mysqli_commit($con);

    echo "<script>
        alert('게시글이 성공적으로 등록되었습니다! 100 포인트가 지급되었습니다.');
        location.href = 'list.php';
    </script>";
} catch (Exception $e) {
    mysqli_rollback($con);
    echo "<script>
        alert('" . $e->getMessage() . "');
        history.go(-1);
    </script>";
    exit;
}

mysqli_stmt_close($stmt);
mysqli_stmt_close($update_stmt);
mysqli_close($con);
?>