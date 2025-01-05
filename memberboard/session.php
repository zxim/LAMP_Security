<?php
session_start([
    'cookie_lifetime' => 0,             // 브라우저 종료 시 세션 쿠키 삭제
    'cookie_httponly' => true,          // JavaScript에서 세션 쿠키 접근 금지
    'use_strict_mode' => true,          // 엄격한 세션 모드
    'use_only_cookies' => true,         // 쿠키를 통한 세션만 허용
    'gc_maxlifetime' => 1200            // 세션 데이터 유효 시간 (20분)
]);

// 세션 만료 시간 설정
$session_timeout = 1200; // 20분

// 마지막 활동 시간 확인 및 세션 만료 처리
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();  // 세션 변수 초기화
    session_destroy(); // 세션 삭제
    header("Location: login.php?timeout=true"); // 로그인 페이지로 이동
    exit;
}
$_SESSION['last_activity'] = time(); // 현재 시간을 마지막 활동 시간으로 갱신

// 매번 새로운 세션 ID 생성
if (!isset($_SESSION['initialized'])) {
    session_regenerate_id(true); // 새로운 세션 ID 생성
    $_SESSION['initialized'] = true;
}

// 사용자 ID
$userid = $_SESSION["userid"] ?? ""; // 사용자 ID가 없으면 빈 값 설정

// 사용자 이름
$username = $_SESSION["username"] ?? ""; // 사용자 이름이 없으면 빈 값 설정

// 사용자 고유 번호 (user_num)
$user_num = $_SESSION["user_num"] ?? 0; // 기본값 0 설정

// 관리자 여부 (admin)
$isAdmin = $_SESSION["admin"] ?? 0; // 기본값 0 설정
?>

