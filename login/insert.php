\<?php
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

    // 비밀번호 유효성 검사
    if (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/", $pass)) {
        echo "<script>alert('비밀번호는 최소 8자리 이상이며, 영어(대문자 또는 소문자), 숫자, 특수기호를 포함해야 합니다.'); history.back();</script>";
        exit();
    }

    // 비밀번호 암호화 (password_hash 사용)
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    // 데이터베이스에 회원 정보 삽입
    $sql = "insert into members (id, pass, name, email, regist_day) ";    // 데이터 삽입 명령
    $sql .= "values('$id', '$hashed_pass', '$name', '$email', '$regist_day')";       

    mysqli_query($con, $sql);  // SQL 명령 실행
    mysqli_close($con);  // DB 연결 종료

    // 로그인 폼으로 이동
    echo "<script>
              location.href = 'login_form.php';
          </script>";
?>

