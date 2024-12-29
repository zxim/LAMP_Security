<?php
$id   = $_POST["id"];               // 아이디
$pass = $_POST["pass"];             // 비밀번호
$name = $_POST["name"];             // 이름
$email  = $_POST["email"];          // 이메일

// 회원가입 날짜
$regist_day = date("Y-m-d (H:i)");

$config = require '../config.php';  // 루트에 있는 config.php 파일 불러옴

// config.php에서 가져온 정보를 변수에 저장
$db_host = $config['DB_HOST'];
$db_user = $config['DB_USER'];
$db_password = $config['DB_PASSWORD'];
$db_name = $config['DB_NAME'];

// DB 접속
$con = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (mysqli_connect_errno()) {
    echo "<script>alert('데이터베이스 연결에 실패하였습니다. 다시 시도해 주세요.'); history.back();</script>";
    exit();
}

// 비밀번호 유효성 검사 (필요 시 유지)
if (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/", $pass)) {
    echo "<script>alert('비밀번호는 최소 8자리 이상이며, 영어(대문자 또는 소문자), 숫자, 특수기호를 포함해야 합니다.'); history.back();</script>";
    mysqli_close($con); // DB 연결 종료
    exit();
}

// 이메일 중복 확인
$email_check_sql = "SELECT * FROM members WHERE email = '$email'";
$email_check_result = mysqli_query($con, $email_check_sql);

if (mysqli_num_rows($email_check_result) > 0) {
    echo "<script>alert('이미 가입된 이메일 주소입니다. 다른 이메일을 사용해 주세요.'); history.back();</script>";
    mysqli_close($con); // DB 연결 종료
    exit();
}

// 데이터베이스에 회원 정보 삽입
$sql = "INSERT INTO members (id, pass, name, email, regist_day) ";
$sql .= "VALUES ('$id', '$pass', '$name', '$email', '$regist_day')";

if (mysqli_query($con, $sql)) {
    // 회원가입 성공 시 알림 메시지 출력
    echo "<script>
              alert('회원가입에 성공했습니다!');
              location.href = 'login_form.php';
          </script>";
} else {
    // 삽입 실패 시 오류 메시지 출력
    echo "<script>
              alert('회원가입에 실패했습니다. 다시 시도해 주세요.');
              history.back();
          </script>";
}

mysqli_close($con);  // DB 연결 종료
?>
