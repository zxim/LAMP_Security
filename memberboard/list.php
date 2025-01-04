<?php
include "session.php";
include '../login/header.php'; 
?>

<!DOCTYPE html>
<html lang="ko">
<head> 
    <meta charset="utf-8">
    <title>게시판</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .page_num {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .page_num a {
            padding: 10px 15px;
            background-color: #fff;
            color: #007aff;
            border: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4); /* 약간의 그림자 */
        }

        .page_num a:hover {
            background-color: #007aff; 
            color: #fff; /* 글씨 파란색으로 변경 */
            border: 1px solid #007aff;
            box-shadow: 0 4px 8px rgba(0, 122, 255, 0.4); /* 강조된 그림자 */
        }

        .page_num .current {
            padding: 10px 15px;
            border: 1px solid #007aff;
            background-color: white;
            color: #007aff;
            font-weight: bold;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 122, 255, 0.2); 
        }
    </style>
</head>
    <body>
        <h2>
            <form name="search" method="get" action="list.php">    
                <input type="text" name="search" placeholder="검색하기">
                <button class="btn">검색</button>
            </form>
        </h2>

        <ul class="board_list">
            <li>
                <span class="col1">번호</span>
                <span class="col2">제목</span>
                <span class="col3">글쓴이</span>
                <span class="col4">첨부</span>
                <span class="col5">등록일</span>
            </li>

            <?php
            // DB 연결
            $config = require '../config.php';
            $con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
            if (!$con) {
                die("DB 연결 실패: " . mysqli_connect_error());
            }
        
            // 검색어와 페이지 초기화
            $page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
            $search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
        
            // Prepared Statement를 활용한 쿼리 작성
            if (!empty($search)) {
                $sql = "SELECT * FROM memberboard WHERE subject LIKE ? OR content LIKE ? ORDER BY num DESC";
                $stmt = $con->prepare($sql);
                $like_search = "%" . $search . "%";
                $stmt->bind_param("ss", $like_search, $like_search);
            } else {
                $sql = "SELECT * FROM memberboard ORDER BY num DESC";
                $stmt = $con->prepare($sql);
            }
        
            $stmt->execute();
            $result = $stmt->get_result();
        
            // 페이지네이션 계산
            $total_record = $result->num_rows;
            $scale = 10;
            $total_page = ceil($total_record / $scale);
            $start = ($page - 1) * $scale;
            $number = $total_record - $start;
        
            $stmt->data_seek($start); // 데이터 시작점 이동
        
            for ($i = 0; $i < $scale && $i + $start < $total_record; $i++) {
                $row = $result->fetch_assoc();
                $num = htmlspecialchars($row["num"], ENT_QUOTES, 'UTF-8');
                $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
                $subject = htmlspecialchars($row["subject"], ENT_QUOTES, 'UTF-8');
                $regist_day = htmlspecialchars($row["regist_day"], ENT_QUOTES, 'UTF-8');
                $file_image = !empty($row["file_name"]) ? "<img src='./file.png' alt='파일'>" : "&nbsp;";
            
                // 비밀글 여부 확인
                $is_secret = !empty($row["password"]);
                $display_subject = $is_secret ? "🔒 $subject" : $subject;
            
                echo "<li>
                        <span class='col1'>$number</span>
                        <span class='col2'><a href='view.php?num=$num&page=$page'>$display_subject</a></span>
                        <span class='col3'>$name</span>
                        <span class='col4'>$file_image</span>
                        <span class='col5'>$regist_day</span>
                      </li>";
                $number--;
            }
        
            $stmt->close();
            mysqli_close($con);
            ?>
        </ul>
        
        <!-- 페이지 번호 -->
        <ul class="page_num">
            <?php
            if ($page > 1) {
                $new_page = $page - 1;
                echo "<li><a href='list.php?page=$new_page'>이전</a></li>";
            } else {
                echo "<li>&nbsp;</li>";
            }
        
            for ($i = 1; $i <= $total_page; $i++) {
                if ($page == $i) {
                    echo "<li><b>$i</b></li>";
                } else {
                    echo "<li><a href='list.php?page=$i'>$i</a></li>";
                }
            }
        
            if ($page < $total_page) {
                $new_page = $page + 1;
                echo "<li><a href='list.php?page=$new_page'>다음</a></li>";
            } else {
                echo "<li>&nbsp;</li>";
            }
            ?>
        </ul>
        
        <!-- 글쓰기 버튼 -->
        <ul class="buttons">
            <?php if (isset($userid)) { ?>
                <li><button class="btn" onclick="location.href='form.php'">글쓰기</button></li>
            <?php } else { ?>
                <script>
                    function alertLoginRequired() {
                        alert('로그인이 필요합니다.');
                        location.href = '/project/login/login_form.php';
                    }
                </script>
                <li><button class="btn" onclick="alertLoginRequired()">글쓰기</button></li>
            <?php } ?>
        </ul>
    </body>
</html>
