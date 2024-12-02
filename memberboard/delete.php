<?php
include "session.php";  // 세션 처리 (테스트를 위해 세션 유지)

// 로그인 여부 확인 제거
// 기존 코드를 주석 처리하거나 삭제하여 로그인 여부를 확인하지 않음
// if (!isset($_SESSION["userid"])) {
//     echo "<script>
//         alert('로그인이 필요합니다.');
//         location.href = '/project/login/login_form.php';  // 로그인하지 않았으면 로그인 페이지로 이동
//     </script>";
//     exit();
// }

$config = require '../config.php';  // 루트에 있는 config.php 파일 불러옴

// config.php에서 가져온 정보를 변수에 저장
$db_host = $config['DB_HOST'];
$db_user = $config['DB_USER'];
$db_password = $config['DB_PASSWORD'];
$db_name = $config['DB_NAME'];

// 게시글 번호 가져오기 (num 파라미터)
$num = isset($_GET["num"]) ? intval($_GET["num"]) : 0;  // 기본값 0 설정
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;  // 기본값 1 설정

// DB 연결
$con = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$con) {
    echo "<script>
        alert('DB 연결에 실패하였습니다.');
        location.href = 'list.php?page=$page';  // DB 연결 실패 시 목록 페이지로 이동
    </script>";
    exit();
}

// 작성자 확인 제거
// 기존에 작성자와 현재 사용자를 비교하던 코드를 제거하여 누구나 글을 삭제할 수 있도록 허용
// $sql = "SELECT id FROM memberboard WHERE num = ?";
// $stmt = mysqli_prepare($con, $sql);
// mysqli_stmt_bind_param($stmt, "i", $num);
// mysqli_stmt_execute($stmt);
// $result = mysqli_stmt_get_result($stmt);
// $row = mysqli_fetch_assoc($result);

// if (!$row) {
//     echo "<script>
//         alert('해당 글을 찾을 수 없습니다.');
//         location.href = 'list.php?page=$page';  // 글이 존재하지 않으면 목록 페이지로 이동
//     </script>";
//     mysqli_close($con);
//     exit();
// }

// if (strval($row["id"]) !== strval($_SESSION["userid"])) {
//     echo "<script>
//         alert('본인의 글만 삭제할 수 있습니다.');
//         location.href = 'list.php?page=$page';  // 작성자 불일치 시 목록 페이지로 이동
//     </script>";
//     mysqli_close($con);
//     exit();
// }

// 글 삭제
$sql = "DELETE FROM memberboard WHERE num = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $num);
$execute_result = mysqli_stmt_execute($stmt);

// 삭제 성공 여부 확인
if ($execute_result) {
    echo "<script>
        alert('글이 삭제되었습니다.');
        location.href = 'list.php?page=$page';  // 삭제 성공 시 목록 페이지로 이동
    </script>";
} else {
    echo "<script>
        alert('글 삭제에 실패했습니다. 다시 시도해주세요.');
        location.href = 'list.php?page=$page';  // 삭제 실패 시 목록 페이지로 이동
    </script>";
}

// DB 연결 종료
mysqli_stmt_close($stmt);
mysqli_close($con);
?>
