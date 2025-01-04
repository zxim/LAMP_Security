<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("잘못된 접근입니다.");
}

$id = $_GET["id"] ?? null;
$current_pass = $_POST["current_pass"] ?? null;
$new_pass = $_POST["new_pass"] ?? null;
$name = $_POST["name"] ?? null;
$email = $_POST["email"] ?? null;
$csrf_token = $_POST["csrf_token"] ?? null;

$config = require '../config.php'; // config.php 파일 로드

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    die("<script>alert('데이터베이스 연결 실패. 다시 시도해 주세요.'); history.back();</script>");
}

// CSRF 토큰 검증
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    die("<script>alert('잘못된 요청입니다. (CSRF 토큰 검증 실패)'); history.back();</script>");
}

// 기존 비밀번호 확인
$stmt = $con->prepare("SELECT pass FROM members WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<script>alert('존재하지 않는 사용자입니다.'); history.back();</script>");
}

$row = $result->fetch_assoc();
$db_pass = $row["pass"];

// 비밀번호 해싱 비교
if (!password_verify($current_pass, $db_pass)) {
    die("<script>alert('현재 비밀번호가 올바르지 않습니다.'); history.back();</script>");
}

// 비밀번호 유효성 검사
if (!empty($new_pass) && !preg_match("/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/", $new_pass)) {
    die("<script>alert('새 비밀번호는 최소 8자리 이상, 영어, 숫자, 특수기호를 포함해야 합니다.'); history.back();</script>");
}

// 이메일 유효성 검사
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("<script>alert('유효한 이메일 형식을 입력하세요.'); history.back();</script>");
}

// 새 비밀번호 해싱
$new_pass_hashed = !empty($new_pass) ? password_hash($new_pass, PASSWORD_DEFAULT) : $db_pass;

// 회원 정보 업데이트
$stmt = $con->prepare("UPDATE members SET pass = ?, name = ?, email = ? WHERE id = ?");
$stmt->bind_param("ssss", $new_pass_hashed, $name, $email, $id);

if ($stmt->execute()) {
    echo "<script>alert('회원 정보가 성공적으로 수정되었습니다.'); location.href = 'index.php';</script>";
} else {
    echo "<script>alert('회원 정보 수정에 실패하였습니다. 다시 시도해 주세요.'); history.back();</script>";
}

// 자원 해제 및 DB 연결 종료
$stmt->close();
mysqli_close($con);
?>
