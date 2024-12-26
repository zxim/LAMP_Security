<!DOCTYPE html>
<html lang="ko">
<head> 
    <meta charset="utf-8">
    <title>게시판</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body> 
    <?php 
    include "session.php";
    include '../login/header.php'; ?>

    <h2>
        <a href="../login/index.php">홈</a>
        <span style="margin-left: 50px;"></span> 회원 게시판 > 목록보기
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
            // 페이지 번호 처리
            $page = isset($_GET["page"]) ? $_GET["page"] : 1;

            // 검색어 처리
            $search = isset($_GET["search"]) ? $_GET["search"] : "";

            $config = require '../config.php';  // 루트에 있는 config.php 파일 불러옴

            // config.php에서 가져온 정보를 변수에 저장
            $db_host = $config['DB_HOST'];
            $db_user = $config['DB_USER'];
            $db_password = $config['DB_PASSWORD'];
            $db_name = $config['DB_NAME'];

            // DB 연결
            $con = mysqli_connect($db_host, $db_user, $db_password, $db_name);

            if (!$con) {
                die("DB 연결 실패: " . mysqli_connect_error());
            }

            if (!empty($search)) {
                // 검색어를 검증하지 않고 그대로 SQL에 포함
                $sql = "SELECT * FROM memberboard 
                        WHERE subject LIKE '%$search%' OR content LIKE '%$search%' 
                        ORDER BY num DESC";
            } else {
                // 검색어가 없는 경우 모든 게시글 조회
                $sql = "SELECT * FROM memberboard ORDER BY num DESC";
            }

            $result = mysqli_query($con, $sql);

            if (!$result) {
                die("쿼리 실행 오류: " . mysqli_error($con));
            }

            // 전체 레코드 수
            $total_record = mysqli_num_rows($result);
            $scale = 4; // 한 화면에 표시할 글 수

            // 전체 페이지 수 계산
            $total_page = ceil($total_record / $scale);

            // 시작 레코드 계산
            $start = ($page - 1) * $scale;

            // 레코드 번호 계산
            $number = $total_record - $start;

            // 결과 출력
            for ($i = $start; $i < $start + $scale && $i < $total_record; $i++) {
                mysqli_data_seek($result, $i);
                $row = mysqli_fetch_assoc($result);
            
                // 데이터 할당
                $num = $row["num"];
                $name = $row["name"];
                $subject = $row["subject"];
                $regist_day = $row["regist_day"];
                $file_image = $row["file_name"] ? "<img src='./file.png' alt='파일'>" : "&nbsp&nbsp;";
                echo "<li>
                    <span class='col1'>$number</span>
                    <span class='col2'><a href='view.php?num=$num&page=$page'>$subject</a></span>
                    <span class='col3'>$name</span>
                    <span class='col4'>$file_image</span>
                    <span class='col5'>$regist_day</span>
                </li>";
                $number--;
            }

            mysqli_close($con);
            ?>

    </ul>

    <!-- 페이지 번호 -->
    <ul class="page_num">
        <?php
        if ($page > 1) {
            $new_page = $page - 1;
            echo "<li><a href='list.php?page=$new_page'>◀ 이전</a></li>";
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
            echo "<li><a href='list.php?page=$new_page'>다음 ▶</a></li>";
        } else {
            echo "<li>&nbsp;</li>";
        }
        ?>
    </ul>

    <!-- 버튼 -->
    <ul class="buttons">
        <?php if ($userid) { // 로그인된 사용자만 글쓰기 가능 ?>
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
