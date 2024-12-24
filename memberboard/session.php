<?php
session_start(); // 세션 시작

// 사용자 ID
if (isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];
} else {
    $userid = "";
}

// 사용자 이름
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    $username = "";
}

// 사용자 고유 번호 (user_num)
if (isset($_SESSION["user_num"])) {
    $user_num = $_SESSION["user_num"];
} else {
    $user_num = 0; // 기본값 설정
}
?>
