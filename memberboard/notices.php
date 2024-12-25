<?php
include "session.php";

// 세션에서 사용자 정보 가져오기
$userid = isset($_SESSION["userid"]) ? $_SESSION["userid"] : "";
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "";
$isAdmin = isset($_SESSION["admin"]) && $_SESSION["admin"] == 1; // 관리자인지 확인

$config = require '../config.php';  // DB 설정 가져오기

// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (mysqli_connect_errno()) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

// 공지사항 가져오기
$sql = "SELECT * FROM notices ORDER BY created_at DESC";
$result = mysqli_query($con, $sql);

// HTML 시작
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>공지사항</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include '../login/header.php'; ?>

    <div class="container">
        <h1>공지사항</h1>
        <?php if ($isAdmin): // 관리자인 경우에만 작성 버튼 표시 ?>
            <a href="notice_write.php" class="btn">작성하기</a>
        <?php endif; ?>

        <ul class="notice-list">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <li>
                    <h3><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <small>작성일: <?php echo $row['created_at']; ?></small>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
<?php
mysqli_close($con); // DB 연결 종료
?>
