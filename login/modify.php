<?php
    $id   = $_GET["id"];               // 아이디는 GET 방식으로 전달
    $pass = $_POST["pass"];            // 비밀번호
    $name = $_POST["name"];            // 이름
    $email  = $_POST["email"];         // 이메일

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

    // 이메일 유효성 검사
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('유효한 이메일 주소를 입력하세요.'); history.back();</script>";
        exit();
    }

    // Prepared Statement 사용해서 SQL 인젝션 방지
    $stmt = $con->prepare("UPDATE members SET pass=?, name=?, email=? WHERE id=?");
    $stmt->bind_param("ssss", $pass, $name, $email, $id);

    if ($stmt->execute()) {
        echo "<script>location.href = 'index.php';</script>";  // 수정 완료 후 메인 페이지로 이동
    } else {
        echo "<script>alert('회원정보 수정에 실패하였습니다. 다시 시도해 주세요.'); history.back();</script>";
    }

    // 자원 해제 및 DB 연결 종료
    $stmt->close();
    mysqli_close($con);
?>
