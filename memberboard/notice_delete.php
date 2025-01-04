<?php
include "session.php";

// 관리자인지 확인
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] != 1) {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href = 'notices.php';</script>";
    exit();
}

$config = require '../config.php'; // DB 설정 가져오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (mysqli_connect_errno()) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

// 공지사항 ID 가져오기
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>alert('잘못된 요청입니다.'); location.href = 'notices.php';</script>";
    exit();
}

// Prepared Statement를 사용하여 공지사항 삭제
$stmt = $con->prepare("DELETE FROM notices WHERE id = ?");
if (!$stmt) {
    echo "<script>alert('쿼리 준비 실패: " . mysqli_error($con) . "'); location.href = 'notices.php';</script>";
    exit();
}

$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo "<script>alert('공지사항이 삭제되었습니다.'); location.href = 'notices.php';</script>";
} else {
    echo "<script>alert('공지사항 삭제에 실패했습니다. 다시 시도하세요.'); location.href = 'notices.php';</script>";
}

$stmt->close();
mysqli_close($con);
?>
