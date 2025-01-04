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

// Prepared Statement를 사용하여 SQL 실행
$sql = "SELECT id, name, num, admin, pass FROM members WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $id);  // 사용자 입력값을 안전하게 바인딩
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // 비밀번호 검증
    if (password_verify($pass, $row["pass"])) {
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
} else {
    echo "<script>
             alert('아이디 또는 비밀번호가 틀립니다.');
             history.go(-1);
          </script>";
}

// 자원 정리
$stmt->close();
mysqli_close($con);
?>
