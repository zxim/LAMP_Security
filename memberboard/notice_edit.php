<?php
include "session.php";

// 관리자인지 확인
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] != 1) {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href='notices.php';</script>";
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
    echo "<script>alert('잘못된 요청입니다.'); location.href='notices.php';</script>";
    exit();
}

// 공지사항 데이터 가져오기
$stmt = $con->prepare("SELECT title, content FROM notices WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('해당 공지사항을 찾을 수 없습니다.'); location.href='notices.php';</script>";
    exit();
}

$row = $result->fetch_assoc();
$title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
$content = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');

$stmt->close();

// 폼 제출 처리
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = isset($_POST["title"]) ? trim($_POST["title"]) : '';
    $content = isset($_POST["content"]) ? trim($_POST["content"]) : '';

    if (empty($title) || empty($content)) {
        echo "<script>alert('제목과 내용을 입력하세요.'); location.href='notice_edit.php?id=$id';</script>";
        exit();
    }

    // Prepared Statement를 사용하여 데이터 업데이트
    $update_stmt = $con->prepare("UPDATE notices SET title = ?, content = ? WHERE id = ?");
    $update_stmt->bind_param("ssi", $title, $content, $id);

    if ($update_stmt->execute()) {
        echo "<script>alert('공지사항이 수정되었습니다.'); location.href='notices.php';</script>";
    } else {
        echo "<script>alert('공지사항 수정에 실패했습니다. 다시 시도하세요.'); location.href='notice_edit.php?id=$id';</script>";
    }

    $update_stmt->close();
    mysqli_close($con);
    exit();
}

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>공지사항 수정</title>
    <style>
        .edit-container {
            margin: 20px auto;
            max-width: 800px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .edit-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .edit-form div {
            margin-bottom: 20px;
        }
        .edit-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .edit-form input[type="text"],
        .edit-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .edit-form textarea {
            resize: vertical;
        }
        .edit-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .edit-buttons button {
            padding: 10px 20px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
        }
        .edit-buttons button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <?php include '../login/header.php'; ?>

    <div class="edit-container">
        <h1>공지사항 수정</h1>
        <form class="edit-form" method="post" action="notice_edit.php?id=<?= $id ?>">
            <div>
                <label for="title">제목</label>
                <input type="text" id="title" name="title" value="<?= $title ?>" required>
            </div>
            <div>
                <label for="content">내용</label>
                <textarea id="content" name="content" rows="10" required><?= $content ?></textarea>
            </div>
            <div class="edit-buttons">
                <button type="submit">수정</button>
                <button type="button" onclick="location.href='notices.php'">목록으로</button>
            </div>
        </form>
    </div>
</body>
</html>
