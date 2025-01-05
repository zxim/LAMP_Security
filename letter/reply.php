<?php
// 세션 및 데이터베이스 설정
include "../memberboard/session.php"; // 세션 처리
$config = require '../config.php'; // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

// 세션에서 로그인 사용자 정보 가져오기
$user_id = $_SESSION['userid'] ?? null;

if (!$user_id) {
    echo "<script>alert('로그인 후 이용 가능합니다.'); location.href='/project/login/login_form.php';</script>";
    exit;
}

// 받는 사람 ID 가져오기
$receiver_id = isset($_GET['receiver_id']) ? htmlspecialchars(trim($_GET['receiver_id'])) : '';
if (empty($receiver_id)) {
    echo "<script>alert('받는 사람 ID가 유효하지 않습니다.'); history.back();</script>";
    exit;
}

// 받는 사람 존재 확인
$receiver_query = "SELECT id FROM members WHERE id = ?";
$stmt = mysqli_prepare($con, $receiver_query);
mysqli_stmt_bind_param($stmt, "s", $receiver_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    echo "<script>alert('받는 사람 ID가 존재하지 않습니다.'); history.back();</script>";
    exit;
}
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <title>답장하기</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .message-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
        .message-title {
            text-align: center;
            margin-bottom: 20px;
            color: #007BFF;
        }
        .message-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .message-form input,
        .message-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .message-form button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .message-form button:hover {
            background-color: #0056b3;
        }
        .message-btn {
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .message-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php include "../login/header.php"; ?>

<div class="message-container">
    <h1 class="message-title">답장하기</h1>
    <form action="send.php" method="post" class="message-form">
        <label for="receiver_id">받는 사람 ID:</label>
        <input type="text" id="receiver_id" name="receiver_id" value="<?= htmlspecialchars($receiver_id) ?>" readonly>

        <label for="subject">제목:</label>
        <input type="text" id="subject" name="subject" placeholder="제목을 입력하세요" required>

        <label for="content">내용:</label>
        <textarea id="content" name="content" rows="10" placeholder="내용을 입력하세요" required></textarea>

        <button type="submit" class="message-btn">보내기</button>
    </form>
    <div style="text-align: center; margin-top: 20px;">
        <a class="message-btn" href="message.php">목록으로 돌아가기</a>
    </div>
</div>
</body>
</html>
<?php
mysqli_close($con);
?>
