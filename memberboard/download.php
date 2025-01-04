<?php
    $file_copied = isset($_GET["file_copied"]) ? basename($_GET["file_copied"]) : null;
    $file_name = isset($_GET["file_name"]) ? basename($_GET["file_name"]) : null;

    if (!$file_copied || !$file_name) {
        die("잘못된 요청입니다.");
    }

    $file_path = realpath("./data/" . $file_copied);

    // 파일 경로 검증
    if ($file_path === false || strpos($file_path, realpath('./data/')) !== 0) {
        die("잘못된 접근입니다.");
    }

    // 파일 존재 확인
    if (!file_exists($file_path)) {
        die("파일이 존재하지 않습니다.");
    }

    // 실제 MIME 타입 확인
    $mime_type = mime_content_type($file_path);
    header("Content-Type: $mime_type");
    header("Content-Disposition: attachment; filename=" . urlencode($file_name));
    header("Content-Transfer-Encoding: binary");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public");
    header("Content-Length: " . filesize($file_path));

    // 파일 출력
    $handle = fopen($file_path, 'rb');
    while (!feof($handle)) {
        echo fread($handle, 8192);
        flush();
    }
    fclose($handle);
    exit();
?>
