<?php
include "session.php"; // 세션 처리

// 로그인 여부 확인
if (!$userid) {
    echo "
        <script>
        alert('게시판 글쓰기는 로그인 후 이용해 주세요!');
        history.go(-1);
        </script>
    ";
    exit;
}

$subject = $_POST["subject"];
$content = $_POST["content"];

// HTML 특수문자 변환 제거 (XSS 허용)
$regist_day = date("Y-m-d (H:i)");

$upload_dir = './data/';

// 파일 정보
$upfile_name = $_FILES["upfile"]["name"];
$upfile_tmp_name = $_FILES["upfile"]["tmp_name"];
$upfile_error = $_FILES["upfile"]["error"];

if ($upfile_name && !$upfile_error) {
    $uploaded_file = $upload_dir . $upfile_name;

    if (!move_uploaded_file($upfile_tmp_name, $uploaded_file)) {
        echo "<script>
        alert('파일 업로드에 실패했습니다.');
        history.go(-1);
        </script>";
        exit;
    }
} else {
    $upfile_name = "";
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
    $sql = "INSERT INTO memberboard (id, name, subject, content, regist_day, file_name)
            VALUES ('$userid', '$username', '$subject', '$content', '$regist_day', '$upfile_name')";

    if (!mysqli_query($con, $sql)) {
        throw new Exception("게시글 저장 오류: " . mysqli_error($con));
    }

    // 포인트 지급 (100점)
    $update_sql = "UPDATE members SET points = points + 100 WHERE id = '$userid'";
    if (!mysqli_query($con, $update_sql)) {
        throw new Exception("포인트 지급 오류: " . mysqli_error($con));
    }

    // 트랜잭션 커밋
    mysqli_commit($con);

    // 글 목록으로 이동
    echo "<script>
        alert('게시글이 성공적으로 등록되었습니다! 100 포인트가 지급되었습니다.');
        location.href = 'list.php';
    </script>";
} catch (Exception $e) {
    // 트랜잭션 롤백
    mysqli_rollback($con);

    echo "<script>
        alert('" . $e->getMessage() . "');
        history.go(-1);
    </script>";
    exit;
}

mysqli_close($con);
?>
