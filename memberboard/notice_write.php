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
        // 작성 성공 시 목록으로 리디렉션
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
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans', Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        .notice-write-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .notice-write-container h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .notice-write-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .notice-write-form label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .notice-write-form input[type="text"],
        .notice-write-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .notice-write-form input[type="text"]:focus,
        .notice-write-form textarea:focus {
            border-color: #555;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }
        .notice-write-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .notice-write-buttons .btn {
            width: 120px;
            padding: 10px 0;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .notice-write-buttons .btn:hover {
            background-color: #444;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <?php include '../login/header.php'; ?>

    <div class="notice-write-container">
        <h1>공지사항 작성</h1>
        <form class="notice-write-form" method="post" action="notice_write.php">
            <div>
                <label for="title">제목</label>
                <input type="text" id="title" name="title" placeholder="공지사항 제목을 입력하세요" required>
            </div>
            <div>
                <label for="content">내용</label>
                <textarea id="content" name="content" rows="10" placeholder="공지사항 내용을 입력하세요" required></textarea>
            </div>
            <div class="notice-write-buttons">
                <button type="submit" class="btn">작성</button>
                <button type="button" class="btn" onclick="location.href='notices.php'">목록으로</button>
            </div>
        </form>
    </div>
</body>
</html>
