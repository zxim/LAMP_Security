<?php
include "session.php"; // 세션 처리

// 로그인 여부 확인
// if (!$userid) {
//     echo "
//         <script>
//         alert('게시판 글쓰기는 로그인 후 이용해 주세요!');
//         history.go(-1);
//         </script>
//     ";
//     exit;
// }

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
    // 확장자 및 파일 크기 검증 제거 (파일 업로드 취약점 허용)
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

// 데이터 삽입 (XSS 허용)
$sql = "INSERT INTO memberboard (id, name, subject, content, regist_day, file_name)
        VALUES ('$userid', '$username', '$subject', '$content', '$regist_day', '$upfile_name')";


if (!mysqli_query($con, $sql)) {
    echo "<script>alert('게시글 저장 오류: " . mysqli_error($con) . "');</script>";
    exit;
}

mysqli_close($con);

// 글 목록으로 이동
echo "<script>
    location.href = 'list.php';
</script>";
?>
