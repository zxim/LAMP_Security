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

// 검색 및 페이지 처리
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;

$limit = 10;
$offset = ($page - 1) * $limit;

// 검색 조건 추가
$where = "";
if (!empty($search)) {
    $search = mysqli_real_escape_string($con, $search);
    $where = "WHERE title LIKE '%$search%'";
}

// 공지사항 가져오기
$sql_count = "SELECT COUNT(*) as total FROM notices $where";
$result_count = mysqli_query($con, $sql_count);
$total = mysqli_fetch_assoc($result_count)['total'];

$total_pages = ceil($total / $limit);

$sql = "SELECT id, title, DATE(created_at) as created_date 
        FROM notices 
        $where 
        ORDER BY created_at DESC 
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($con, $sql);

if (!$result) {
    die("쿼리 실행 실패: " . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>공지사항</title>
    <style>
        body {
            font-family: 'Noto Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        .notice-container {
            margin: 40px auto;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .notice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .notice-header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }
        .notice-search {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .notice-search input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .notice-search button {
            padding: 10px 20px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .notice-search button:hover {
            background-color: #333;
        }
        .notice-list {
            list-style: none;
            padding: 0;
            margin: 0;
            border-top: 2px solid #ddd;
        }
        .notice-list li {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            color: #333;
            font-weight: bold;
        }
        .notice-list li a {
            text-decoration: none;
            color: #333;
            flex-grow: 1;
        }
        .notice-list li a:hover {
            text-decoration: underline;
        }
        .notice-list small {
            color: #666;
            font-size: 14px;
            text-align: right;
        }
        .notice-btn {
            background-color: black;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }
        .notice-btn:hover {
            background-color: #333;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        .pagination a {
            padding: 10px 15px;
            background-color: black;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .pagination a:hover {
            background-color: #333;
        }
        .pagination .current {
            padding: 10px 15px;
            border: 1px solid black;
            background-color: white;
            color: black;
            font-weight: bold;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include '../login/header.php'; ?>

    <div class="notice-container">
        <div class="notice-header">
            <h1>공지사항</h1>
            <?php if ($isAdmin): ?>
                <a href="notice_write.php" class="notice-btn">작성하기</a>
            <?php endif; ?>
        </div>

        <form class="notice-search" method="get" action="notices.php">
            <input type="text" name="search" placeholder="제목 검색" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">검색</button>
        </form>

        <ul class="notice-list">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <li>
                    <a href="notice_view.php?id=<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <small><?= htmlspecialchars($row['created_date'], ENT_QUOTES, 'UTF-8'); ?></small>
                </li>
            <?php endwhile; ?>
        </ul>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">◀ 이전</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="current"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">다음 ▶</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($con); // DB 연결 종료
?>
