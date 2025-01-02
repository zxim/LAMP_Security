<?php
include "session.php";

$config = require '../config.php';  // DB 설정 가져오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (mysqli_connect_errno()) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

// 세션에서 관리자인지 확인
$isAdmin = isset($_SESSION["admin"]) && $_SESSION["admin"] == 1;

// 공지사항 ID 가져오기
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("잘못된 요청입니다.");
}

// 공지사항 데이터 가져오기
$stmt = $con->prepare("SELECT title, content, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at FROM notices WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("해당 공지사항을 찾을 수 없습니다.");
}

$row = $result->fetch_assoc();
$title = $row['title'];
$content = nl2br($row['content']);
$created_at = $row['created_at'];

$stmt->close();
mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title><?= $title ?> - 공지사항</title>
    <style>
        .notice-container {
            margin: 20px auto;
            max-width: 800px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .notice-header {
            margin-bottom: 20px;
        }
        .notice-header h1 {
            font-size: 24px;
            margin: 0;
        }
        .notice-header small {
            color: #777;
            font-size: 14px;
        }
        .notice-content {
            font-size: 16px;
            line-height: 1.5;
        }
        .notice-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .notice-buttons form {
            display: inline-block;
        }
        .notice-buttons button {
            width: 120px;
            height: 40px;
            background-color: #fff;
            font-weight: bold;
            color: #007aff;
            border: #fff;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }
        .notice-buttons button:hover {
            background-color: #007aff; 
            color: #fff;
            border: 1px solid #007aff;
            box-shadow: 0 4px 8px rgba(0, 122, 255, 0.4);
        }
    </style>
</head>
<body>
    <?php include '../login/header.php'; ?>

    <div class="notice-container">
        <div class="notice-header">
            <h1><?= $title ?></h1>
            <small>작성일: <?= $created_at ?></small>
        </div>
        <div class="notice-content">
            <?= $content ?>
        </div>

        <div class="notice-buttons">
            <?php if ($isAdmin): ?>
                <form action="notice_edit.php" method="get">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit">수정</button>
                </form>
                <form action="notice_delete.php" method="get" onsubmit="return confirm('정말 삭제하시겠습니까?')">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit">삭제</button>
                </form>
            <?php endif; ?>
            <form action="notices.php" method="get">
                <button type="submit" class="notice-back">목록으로</button>
            </form>
        </div>
    </div>
</body>
</html>
