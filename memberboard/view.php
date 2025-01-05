<?php
include "session.php"; // 세션 처리

$num = isset($_GET["num"]) ? intval($_GET["num"]) : 0; // 게시글 번호
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1; // 페이지 번호

$config = require '../config.php'; // DB 설정 불러오기

$is_admin = isset($_SESSION["admin"]) && $_SESSION["admin"] == 1;

// 비밀번호 입력 제한 확인 및 초기화
if (isset($_SESSION["password_attempts"][$num])) {
    $attempts = $_SESSION["password_attempts"][$num];
    if (time() >= $attempts["timestamp"] + 60) {
        // 1분이 지나면 시도 횟수를 초기화
        unset($_SESSION["password_attempts"][$num]);
    } elseif ($attempts["count"] >= 3) {
        $remaining_time = 60 - (time() - $attempts["timestamp"]);
        echo "<script>alert('$remaining_time 초 후에 다시 시도하세요.'); history.back();</script>";
        exit;
    }
}


// DB 연결
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

// 게시글 가져오기
$sql = "SELECT * FROM memberboard WHERE num = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $num);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo "<script>alert('해당 게시글을 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

$row = $result->fetch_assoc();

// 게시글 데이터
$id = $row["id"];
$name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
$subject = htmlspecialchars($row["subject"], ENT_QUOTES, 'UTF-8');
$regist_day = htmlspecialchars($row["regist_day"], ENT_QUOTES, 'UTF-8');
$content = nl2br(htmlspecialchars($row["content"], ENT_QUOTES, 'UTF-8'));

// 파일 정보
$file_name = htmlspecialchars($row["file_name"], ENT_QUOTES, 'UTF-8');
$file_type = htmlspecialchars($row["file_type"], ENT_QUOTES, 'UTF-8');
$file_copied = htmlspecialchars($row["file_copied"], ENT_QUOTES, 'UTF-8');

// 비밀글 여부 확인
$password = $row["password"];
$is_secret = !empty($password);

// 비밀번호 확인
if ($is_secret && !$is_admin) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $input_password = $_POST["input_password"];
        if ($input_password !== $password) { // 평문 비교
            // 비밀번호 시도 횟수 저장
            if (!isset($_SESSION["password_attempts"][$num])) {
                $_SESSION["password_attempts"][$num] = ["count" => 0, "timestamp" => 0];
            }
            $_SESSION["password_attempts"][$num]["count"] += 1;
            $_SESSION["password_attempts"][$num]["timestamp"] = time();
        
            if ($_SESSION["password_attempts"][$num]["count"] >= 3) {
                echo "<script>alert('비밀번호를 3회 이상 틀렸습니다. 1분 후에 다시 시도하세요.'); location.href = 'list.php?page=$page';</script>";
                exit;
            }
        
            echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
            exit;
        } else {
            // 비밀번호가 맞으면 시도 횟수 초기화
            unset($_SESSION["password_attempts"][$num]);
        }
        
    } else {
        // 비밀번호 입력 폼 표시
        ?>
        <!DOCTYPE html>
        <html lang="ko">
        <head>
            <meta charset="utf-8">
            <title>비밀번호 확인</title>
            <link rel="stylesheet" href="style.css">
            <style>
                .password-modal {
                    display: block;
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: #fff;
                    border: 2px solid #ccc;
                    padding: 20px;
                    border-radius: 10px;
                    text-align: center;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                }

                .password-modal h3 {
                    margin-bottom: 10px;
                    font-size: 18px;
                    color: #007bff;
                    font-weight: bold;
                }

                .password-modal input {
                    width: 100%;
                    padding: 10px;
                    margin-bottom: 10px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }

                .password-modal button {
                    padding: 10px 20px;
                    border: none;
                    background-color: #007bff;
                    color: #fff;
                    font-weight: bold;
                    border-radius: 5px;
                    cursor: pointer;
                }

                .password-modal button:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="password-modal">
                <h3>비밀글 비밀번호 확인</h3>
                <form method="post" action="view.php?num=<?= $num ?>&page=<?= $page ?>">
                    <input type="password" name="input_password" placeholder="비밀번호를 입력하세요." required>
                    <button type="submit">확인</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>게시글 보기</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "../login/header.php"; ?>
    <ul class="board_view">
        <li class="row1">
            <span class="col1"><b>제목 :</b> <?= $subject ?></span>
            <span class="col2"><?= $name ?> | <?= $regist_day ?></span>
        </li>
        <li class="row2">
        <?php
            if ($file_name) {
                $file_path = "./data/" . $file_copied;
                $file_size = filesize($file_path);

                $img_types = array('image/jpeg', 'image/png', 'image/gif');
                if (in_array($file_type, $img_types)) {
                    echo "▷ 첨부파일 : $file_name ($file_size Byte)<br>";
                    echo "<img src='$file_path' alt='$file_name' style='width: 500px; height: auto;'><br><br>";
                } else {
                    echo "▷ 첨부파일 : $file_name ($file_size Byte) &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href='download.php?num=$num&file_copied=$file_copied&file_name=$file_name&file_type=$file_type'>[다운로드]</a><br><br>";
                }
            }
            echo $content;
        ?>
        </li>
    </ul>
    <ul class="buttons">
        <li><button onclick="location.href='list.php?page=<?= $page ?>'">목록보기</button></li>
        <li><button onclick="location.href='modify_form.php?num=<?= $num ?>&page=<?= $page ?>'">수정하기</button></li>
        <li><button onclick="location.href='delete.php?num=<?= $num ?>&page=<?= $page ?>'">삭제하기</button></li>
    </ul>

    <!-- 댓글 입력창 -->
    <div class="comments">
        <h3>댓글 작성</h3>
        <form method="post" action="comment_process.php" class="comment-form">
            <textarea name="comment_content" placeholder="댓글을 작성해주세요!" required></textarea>
            <input type="hidden" name="num" value="<?= $num ?>">
            <button type="submit" class="comment-btn">댓글 작성</button>
        </form>
    </div>

    <!-- 댓글 목록 -->
    <div class="comment-list">
        <h3>댓글 목록</h3>
        <?php
        $comment_sql = "SELECT c.comment_id, c.content, c.regist_day, m.name AS user_name, m.num AS member_id
                        FROM comments AS c
                        JOIN members AS m ON c.member_id = m.num
                        WHERE c.board_num = ?
                        ORDER BY c.comment_id DESC";
        $comment_stmt = $con->prepare($comment_sql);
        $comment_stmt->bind_param("i", $num);
        $comment_stmt->execute();
        $comment_result = $comment_stmt->get_result();

        if ($comment_result->num_rows > 0) {
            while ($comment_row = $comment_result->fetch_assoc()) {
                $comment_user = htmlspecialchars($comment_row['user_name'], ENT_QUOTES, 'UTF-8');
                $comment_content = nl2br(htmlspecialchars($comment_row['content'], ENT_QUOTES, 'UTF-8'));
                $comment_date = htmlspecialchars($comment_row['regist_day'], ENT_QUOTES, 'UTF-8');
                $comment_id = intval($comment_row['comment_id']);
                $comment_member_id = intval($comment_row['member_id']);
        ?>
        <div class="comment-item">
            <p><b><?= $comment_user ?></b> | <?= $comment_date ?></p>
            <p><?= $comment_content ?></p>
            <?php if ($comment_member_id === intval($_SESSION["user_num"])) { ?>
            <form method="post" action="delete_comment.php" class="comment-delete-form">
                <input type="hidden" name="comment_id" value="<?= $comment_id ?>">
                <input type="hidden" name="num" value="<?= $num ?>">
                <button type="submit" class="delete-btn">삭제</button>
            </form>
            <?php } ?>
        </div>
        <hr>
        <?php
            }
        } else {
            echo "<p>작성된 댓글이 없습니다.</p>";
        }
        $comment_stmt->close();
        mysqli_close($con);
        ?>
    </div>
</body>
</html>

