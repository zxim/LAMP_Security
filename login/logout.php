<?php
session_start(); // 세션 시작

// 모든 세션 데이터 삭제
$_SESSION = []; // 세션 배열 초기화

// 세션 쿠키 삭제 (클라이언트 측)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000, // 과거 시간으로 설정하여 삭제
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 세션 자체를 파기 (서버 측)
session_destroy();

// 로그아웃 후 리다이렉트
echo("
    <script>
        location.href = '/project/login/index.php';
    </script>
");
?>

