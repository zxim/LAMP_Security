<?php
include "session.php";

// 관리자인지 확인
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] != 1) {
    echo "<script>alert('관리자만 접근 가능합니다.'); history.back();</script>";
    exit();
}

$config = require '../config.php'; // DB 설정 가져오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (mysqli_connect_errno()) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

// 작성 폼 제출 처리
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    if (empty($title) || empty($content)) {
        echo "<script>alert('제목과 내용을 입력하세요.'); history.back();</script>";
        exit();
    }

    $stmt = $con->prepare("INSERT INTO notices (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);

    if ($stmt->execute()) {
        echo "<script>alert('공지사항이 작성되었습니다.'); location.href = 'notices.php';</script>";
    } else {
        echo "<script>alert('공지사항 작성에 실패했습니다. 다시 시도하세요.'); history.back();</script>";
    }

    $stmt->close();
    mysqli_close($con);
    exit();
}

// HTML 시작
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>공지사항 작성</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include '../login/header.php'; ?>

    <div class="container">
        <h1>공지사항 작성</h1>
        <form method="post" action="notice_write.php">
            <div>
                <label for="title">제목</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="content">내용</label>
                <textarea id="content" name="content" rows="10" required></textarea>
            </div>
            <button type="submit" class="btn">작성</button>
        </form>
    </div>
</body>
</html>
