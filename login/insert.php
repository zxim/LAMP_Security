<?php
$id   = $_POST["id"];               // 아이디
$pass = $_POST["pass"];             // 비밀번호
$name = $_POST["name"];             // 이름
$email = $_POST["email"];           // 이메일

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

// 입력값 검증
if (!preg_match("/^[a-zA-Z0-9!@#$%^&*]{4,15}$/", $id)) {
    echo "<script>alert('아이디는 4~15자 이내의 영어, 숫자, 특수문자(!@#$%^&*)만 허용됩니다.'); history.back();</script>";
    mysqli_close($con);
    exit();
}

if (!preg_match("/^[가-힣a-zA-Z\s]+$/", $name)) {
    echo "<script>alert('이름은 한글 또는 영어만 입력 가능합니다.'); history.back();</script>";
    mysqli_close($con);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('올바른 이메일 형식을 입력해 주세요.'); history.back();</script>";
    mysqli_close($con);
    exit();
}

// 비밀번호 유효성 검사
if (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/", $pass)) {
    echo "<script>alert('비밀번호는 최소 8자리 이상이며, 영어(대문자 또는 소문자), 숫자, 특수기호를 포함해야 합니다.'); history.back();</script>";
    mysqli_close($con); // DB 연결 종료
    exit();
}

// 비밀번호 해시화
$hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

// 이메일 중복 확인 (Prepared Statement 사용)
$email_check_sql = "SELECT * FROM members WHERE email = ?";
$stmt = $con->prepare($email_check_sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$email_check_result = $stmt->get_result();

if ($email_check_result->num_rows > 0) {
    echo "<script>alert('이미 가입된 이메일 주소입니다. 다른 이메일을 사용해 주세요.'); history.back();</script>";
    $stmt->close();
    mysqli_close($con); // DB 연결 종료
    exit();
}
$stmt->close();

// 데이터베이스에 회원 정보 삽입 (Prepared Statement 사용)
$sql = "INSERT INTO members (id, pass, name, email, regist_day) VALUES (?, ?, ?, ?, ?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("sssss", $id, $hashed_pass, $name, $email, $regist_day);

if ($stmt->execute()) {
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

$stmt->close();
mysqli_close($con);  // DB 연결 종료
?>
