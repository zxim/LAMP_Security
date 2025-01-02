<!DOCTYPE html>
<html lang="ko">
<head> 
    <meta charset="utf-8">
    <title>게시판</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* 비밀번호 입력 창 스타일 */
        .password-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            border: 2px solid #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 1, 0.4); 
            z-index: 1000;
            border-radius: 10px;
            text-align: center;
        }

        .password-modal h3 {
            margin-bottom: 10px;
            font-size: 18px;
            color: #007bff;
            font-weight: bold;
        }

        .password-modal form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .password-modal input[type="password"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
            font-size: 14px;
        }

        .password-modal button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .password-modal button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .password-modal button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .password-modal button[type="button"] {
            background-color: #f8f9fa;
            color: #007bff;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .password-modal button[type="button"]:hover {
            background-color: #e2e6ea;
        }
    </style>
</head>
<body>
<?php 
include "session.php";
include '../login/header.php'; ?>

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
        $page = isset($_GET["page"]) ? $_GET["page"] : 1;
        $search = isset($_GET["search"]) ? $_GET["search"] : "";
        $config = require '../config.php';
        $con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);

        if (!$con) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }

        $sql = !empty($search) 
            ? "SELECT * FROM memberboard WHERE subject LIKE '%$search%' OR content LIKE '%$search%' ORDER BY num DESC" 
            : "SELECT * FROM memberboard ORDER BY num DESC";

        $result = mysqli_query($con, $sql);
        if (!$result) {
            die("쿼리 실행 오류: " . mysqli_error($con));
        }

        $total_record = mysqli_num_rows($result);
        $scale = 10;
        $total_page = ceil($total_record / $scale);
        $start = ($page - 1) * $scale;
        $number = $total_record - $start;

        for ($i = $start; $i < $start + $scale && $i < $total_record; $i++) {
            mysqli_data_seek($result, $i);
            $row = mysqli_fetch_assoc($result);
            $num = $row["num"];
            $name = $row["name"];
            $subject = $row["subject"];
            $regist_day = $row["regist_day"];
            $password = $row["password"];
            $file_image = $row["file_name"] ? "<img src='./file.png' alt='파일'>" : "&nbsp;";
            $is_secret = !empty($password);
            $display_subject = $is_secret ? "<b> 🔒 </b> $subject" : $subject;

            echo "<li>
                <span class='col1'>$number</span>";
            echo $is_secret 
                ? "<span class='col2'><a href='#' class='secret-post' data-num='$num' data-password='$password'>$display_subject</a></span>" 
                : "<span class='col2'><a href='view.php?num=$num&page=$page'>$display_subject</a></span>";
            echo "<span class='col3'>$name</span>
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

<!-- 비밀번호 입력 창 -->
<div id="password-modal" class="password-modal">
    <form method="get" action="view.php" class="password-form">
        <h3>비밀번호가 필요한 게시글입니다.</h3>
        <input type="password" name="input_password" placeholder="비밀번호 입력" required>
        <input type="hidden" name="num" id="hidden-num">
        <input type="hidden" name="page" id="hidden-page">
        <input type="hidden" name="password" id="hidden-password">
        <button type="submit">확인</button>
        <button type="button" onclick="hidePasswordModal()">취소</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const secretLinks = document.querySelectorAll('.secret-post');
        const modal = document.getElementById('password-modal');
        const hiddenNum = document.getElementById('hidden-num');
        const hiddenPage = document.getElementById('hidden-page');
        const hiddenPassword = document.getElementById('hidden-password');
        const submitButton = document.querySelector('.password-form button[type="submit"]');
        let attemptCount = 0;

        function checkLockout() {
            const lockoutEndTime = localStorage.getItem('lockoutEndTime');
            if (lockoutEndTime) {
                const remainingTime = new Date(lockoutEndTime) - new Date();
                if (remainingTime > 0) {
                    disableModal(remainingTime);
                    return true;
                } else {
                    localStorage.removeItem('lockoutEndTime');
                }
            }
            return false;
        }

        function disableModal(remainingTime) {
            submitButton.disabled = true;
            submitButton.textContent = `잠금 해제까지 ${Math.ceil(remainingTime / 1000)}초`;
            const interval = setInterval(() => {
                const now = new Date();
                const remaining = new Date(localStorage.getItem('lockoutEndTime')) - now;
                if (remaining <= 0) {
                    clearInterval(interval);
                    submitButton.disabled = false;
                    submitButton.textContent = '확인';
                    localStorage.removeItem('lockoutEndTime');
                } else {
                    submitButton.textContent = `잠금 해제까지 ${Math.ceil(remaining / 1000)}초`;
                }
            }, 1000);
        }

        secretLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                if (!checkLockout()) {
                    hiddenNum.value = this.dataset.num;
                    hiddenPage.value = <?= $page ?>;
                    hiddenPassword.value = this.dataset.password;
                    modal.style.display = 'block';
                }
            });
        });

        window.hidePasswordModal = function () {
            modal.style.display = 'none';
            attemptCount = 0;
        };

        document.querySelector('.password-form').addEventListener('submit', function (e) {
            const inputPassword = document.querySelector('input[name="input_password"]').value;
            if (inputPassword !== hiddenPassword.value) {
                attemptCount++;
                if (attemptCount >= 3) {
                    alert('비밀번호를 3회 틀렸습니다. 1분 동안 잠금 상태가 유지됩니다.');
                    const lockoutEndTime = new Date();
                    lockoutEndTime.setMinutes(lockoutEndTime.getMinutes() + 1);
                    localStorage.setItem('lockoutEndTime', lockoutEndTime);
                    disableModal(60000);
                    hidePasswordModal();
                } else {
                    alert(`비밀번호가 일치하지 않습니다. 남은 시도 횟수: ${3 - attemptCount}`);
                }
                e.preventDefault();
            }
        });

        checkLockout();
    });
</script>
</body>
</html>