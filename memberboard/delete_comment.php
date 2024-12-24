<?php
include "session.php"; // 세션 처리
$config = require '../config.php'; // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('잘못된 요청입니다. POST 요청만 허용됩니다.'); history.back();</script>";
    exit;
}

// 세션에서 로그인 사용자 정보 가져오기
$member_id = isset($_SESSION['user_num']) ? intval($_SESSION['user_num']) : 0;

// 입력값 가져오기
$comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0; // 댓글 ID
$board_num = isset($_POST['num']) ? intval($_POST['num']) : 0; // 게시글 번호

// 입력값 검증
if ($member_id <= 0) {
    echo "<script>alert('로그인 후 댓글을 삭제할 수 있습니다.'); location.href='/project/login/login_form.php';</script>";
    exit;
}

if ($comment_id <= 0 || $board_num <= 0) {
    echo "<script>alert('유효하지 않은 요청입니다.'); history.back();</script>";
    exit;
}

// 댓글 작성자 확인 (comments.member_id와 현재 로그인된 user_num 비교)
$sql = "SELECT member_id FROM comments WHERE comment_id = $comment_id AND board_num = $board_num";
$result = mysqli_query($con, $sql);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "<script>alert('해당 댓글을 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

$row = mysqli_fetch_assoc($result);

// 댓글 작성자와 현재 로그인 사용자의 member_id 비교
if ((int)$row['member_id'] !== $member_id) {
    echo "<script>alert('댓글을 삭제할 권한이 없습니다.'); history.back();</script>";
    exit;
}

// 댓글 삭제
$sql = "DELETE FROM comments WHERE comment_id = $comment_id";
if (!mysqli_query($con, $sql)) {
    echo "<script>alert('댓글 삭제 실패: " . mysqli_error($con) . "'); history.back();</script>";
    mysqli_close($con);
    exit;
}

// DB 연결 종료
mysqli_close($con);

// 댓글 삭제 후 게시글 페이지로 리디렉션
header("Location: view.php?num=$board_num");
exit;
?>
