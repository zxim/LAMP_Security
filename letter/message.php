<?php
include "../memberboard/session.php"; // 세션 처리
$config = require '../config.php'; // DB 설정 불러오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo "<script>alert('데이터베이스 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit;
}

// 세션에서 로그인 사용자 정보 가져오기
$user_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

if (!$user_id) {
    echo "<script>alert('로그인 후 이용 가능합니다.'); location.href='/project/login/login_form.php';</script>";
    exit;
}

// 받은 쪽지 목록
$received_query = "
    SELECT m.*, s.name AS sender_name 
    FROM messages AS m
    JOIN members AS s ON m.sender_id = s.id
    WHERE m.receiver_id = '$user_id' 
    ORDER BY m.sent_at DESC";
$received_result = mysqli_query($con, $received_query);

// 보낸 쪽지 목록
$sent_query = "
    SELECT m.*, r.name AS receiver_name 
    FROM messages AS m
    JOIN members AS r ON m.receiver_id = r.id
    WHERE m.sender_id = '$user_id' 
    ORDER BY m.sent_at DESC";
$sent_result = mysqli_query($con, $sent_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>쪽지함</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .message-layout {
            display: flex;
        }
        .message-sidebar {
            width: 15%;
            padding: 10px;
            background-color: #f9f9f9;
            border-right: 1px solid #ddd;
            height: 100vh;
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
        }
        .message-sidebar a {
            display: block;
            padding: 8px 12px;
            margin-bottom: 8px;
            background-color: white;
            color: #007BFF;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        .message-sidebar a:hover {
            background-color: #007BFF;
            color: white;
        }
        .message-content {
            width: 85%;
            padding: 20px;
        }
        .message-content-section {
            display: none;
        }
        .message-content-section.active {
            display: block;
        }
        .message-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .message-delete-form {
            margin: 0;
        }
        .message-delete-form button {
            background-color: white;
            color: #d9534f;
            border: 1px solid #d9534f;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
            border-radius: 3px;
            transition: all 0.3s;
        }
        .message-delete-form button:hover {
            background-color: #d9534f;
            color: white;
        }
    </style>
    <script>
        function toggleMessages(type) {
            const received = document.getElementById('received-messages');
            const sent = document.getElementById('sent-messages');

            if (type === 'received') {
                received.classList.add('active');
                sent.classList.remove('active');
            } else if (type === 'sent') {
                sent.classList.add('active');
                received.classList.remove('active');
            }
        }

        // 초기화 (보낸 쪽지만 표시)
        document.addEventListener('DOMContentLoaded', () => {
            toggleMessages('sent');
        });
    </script>
</head>
<body>
<?php include "../login/header.php"; ?>

<div class="message-layout">
    <!-- Sidebar -->
    <div class="message-sidebar">
        <a href="send.php">쪽지 쓰기</a>
        <a href="javascript:void(0);" onclick="toggleMessages('received')">받은 쪽지</a>
        <a href="javascript:void(0);" onclick="toggleMessages('sent')">보낸 쪽지</a>
    </div>

    <!-- Content -->
    <div class="message-content">
        <!-- 받은 쪽지 -->
        <div id="received-messages" class="message-content-section">
            <h2>받은 쪽지</h2>
            <div>
                <?php while ($row = mysqli_fetch_assoc($received_result)) { ?>
                    <div class="message-list-item">
                        <div>
                            <a href="view.php?id=<?= $row['id'] ?>">
                                [<?= htmlspecialchars($row['subject']) ?>] 
                                <span class="message-meta">- <?= htmlspecialchars($row['sender_name']) ?> (<?= $row['sent_at'] ?>)</span>
                            </a>
                        </div>
                        <form action="delete.php" method="post" class="message-delete-form">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit">삭제</button>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- 보낸 쪽지 -->
        <div id="sent-messages" class="message-content-section active">
            <h2>보낸 쪽지</h2>
            <div>
                <?php while ($row = mysqli_fetch_assoc($sent_result)) { ?>
                    <div class="message-list-item">
                        <div>
                            <a href="view.php?id=<?= $row['id'] ?>">
                                [<?= htmlspecialchars($row['subject']) ?>] 
                                <span class="message-meta">- <?= htmlspecialchars($row['receiver_name']) ?> (<?= $row['sent_at'] ?>)</span>
                            </a>
                        </div>
                        <form action="delete.php" method="post" class="message-delete-form">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit">삭제</button>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php mysqli_close($con); ?>
