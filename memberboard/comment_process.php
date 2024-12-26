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
$board_num = isset($_POST['num']) ? intval($_POST['num']) : 0; // 게시글 번호
$comment_content = isset($_POST['comment_content']) ? trim($_POST['comment_content']) : ''; // 댓글 내용

// 입력값 검증
if ($member_id <= 0) {
    echo "<script>alert('로그인 후 댓글을 작성할 수 있습니다.'); location.href='/project/login/login_form.php';</script>";
    exit;
}

if ($board_num <= 0) {
    echo "<script>alert('유효하지 않은 게시글 번호입니다.'); history.back();</script>";
    exit;
}

if (empty($comment_content)) {
    echo "<script>alert('댓글 내용을 입력해주세요.'); history.back();</script>";
    exit;
}

$comment_content = mysqli_real_escape_string($con, $comment_content); // SQL Injection 방지

// 트랜잭션 시작
mysqli_begin_transaction($con);

try {
    // 1. 댓글 데이터 삽입
    $sql = "INSERT INTO comments (board_num, member_id, content) VALUES ($board_num, $member_id, '$comment_content')";
    if (!mysqli_query($con, $sql)) {
        throw new Exception("댓글 작성 실패: " . mysqli_error($con));
    }

    // 2. 포인트 지급 (10점)
    $update_sql = "UPDATE members SET points = points + 10 WHERE num = $member_id";
    if (!mysqli_query($con, $update_sql)) {
        throw new Exception("포인트 지급 실패: " . mysqli_error($con));
    }

    // 트랜잭션 커밋
    mysqli_commit($con);

    // 댓글 작성 후 게시글 페이지로 리디렉션
    header("Location: view.php?num=$board_num");
    exit;
} catch (Exception $e) {
    // 트랜잭션 롤백
    mysqli_rollback($con);

    echo "<script>alert('" . $e->getMessage() . "'); history.back();</script>";
    exit;
} finally {
    // DB 연결 종료
    mysqli_close($con);
}
?>
