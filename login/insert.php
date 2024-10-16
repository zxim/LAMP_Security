<?php
    $id   = $_POST["id"];               // 아이디
    $pass = $_POST["pass"];             // 비밀번호
    $name = $_POST["name"];             // 이름
    $email  = $_POST["email"];          // 이메일

    $regist_day = date("Y-m-d (H:i)");  // UTC 현재 '년-월-일 (시:분)'
              
    $con = mysqli_connect("localhost", "min", "guseodhxhdpqj@", "project");  // DB 접속

	$sql = "insert into members (id, pass, name, email, regist_day) ";    // 데이터 삽입 명령
	$sql .= "values('$id', '$pass', '$name', '$email', '$regist_day')";       

	mysqli_query($con, $sql);       // SQL 명령 실행
    mysqli_close($con);     

    // 로그인 폼으로 이동
    echo "<script>
	          location.href = 'login_form.php';
	      </script>";
?>