<?php
$id   = $_POST["id"];
$pass = $_POST["pass"];

$config = require '../config.php';  // 루트에 있는 config.php 파일 불러옴

// config.php에서 가져온 정보를 변수에 저장
$db_host = $config['DB_HOST'];
$db_user = $config['DB_USER'];
$db_password = $config['DB_PASSWORD'];
$db_name = $config['DB_NAME'];

// DB 연결
$con = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// 다중 쿼리 허용
$sql = "SELECT * FROM members WHERE id='$id' AND pass='$pass'";
if (mysqli_multi_query($con, $sql)) {
    do {
        // 첫 번째 SELECT 결과 처리
        if ($result = mysqli_store_result($con)) {
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                session_start();
                $_SESSION["userid"] = $row["id"];
                $_SESSION["username"] = $row["name"];
                $_SESSION["user_num"] = $row["num"];
                $_SESSION["admin"] = $row['admin'];

                echo "<script>
                        alert('로그인 성공!');
                        location.href = 'index.php';
                      </script>";
            } else {
                echo "<script>
                         alert('아이디 또는 비밀번호가 틀립니다.');
                         history.go(-1);
                      </script>";
            }
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($con));
} else {
    echo "SQL 실행 오류: " . mysqli_error($con);
}

mysqli_close($con);
?>
