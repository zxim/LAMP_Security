<?php
header("Content-Type: text/plain");

// 환경 정보 출력
echo "Server Environment Information:\n";
echo "SERVER_NAME: " . $_SERVER['SERVER_NAME'] . "\n";
echo "SERVER_ADDR: " . $_SERVER['SERVER_ADDR'] . "\n";
echo "SERVER_SOFTWARE: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "\n";

// 민감한 파일 읽기
$file = '/etc/passwd'; // Linux 환경에서 사용자 정보 파일
if (file_exists($file)) {
    echo "Contents of $file:\n";
    echo file_get_contents($file);
} else {
    echo "$file does not exist.\n";
}
?>

