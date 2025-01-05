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

// 클라이언트 IP 가져오기
$client_ip = $_SERVER['REMOTE_ADDR'];

// 실패 기록 가져오기
$check_ip_query = "SELECT attempt_count, blocked_until FROM login_attempts WHERE ip_address = ?";
$stmt = $con->prepare($check_ip_query);
$stmt->bind_param("s", $client_ip);
$stmt->execute();
$result = $stmt->get_result();
$ip_data = $result->fetch_assoc();

// 초기값 설정
$attempt_count = 0;
$blocked_until = null;

if ($ip_data) {
    $attempt_count = $ip_data["attempt_count"];
    $blocked_until = $ip_data["blocked_until"];

    // 차단된 상태인지 확인
    if ($blocked_until && strtotime($blocked_until) > time()) {
        $remaining_time = strtotime($blocked_until) - time();
        $remaining_hours = floor($remaining_time / 3600);
        $remaining_minutes = floor(($remaining_time % 3600) / 60);

        echo "<script>
                alert('로그인 시도가 차단되었습니다. 남은 시간: {$remaining_hours}시간 {$remaining_minutes}분');
                history.go(-1);
              </script>";
        exit;
    }

    // 차단 시간이 지난 경우 실패 횟수 초기화
    if ($blocked_until && strtotime($blocked_until) <= time()) {
        $attempt_count = 0; // 실패 횟수 초기화
        $reset_attempts_query = "UPDATE login_attempts SET attempt_count = 0, blocked_until = NULL WHERE ip_address = ?";
        $stmt = $con->prepare($reset_attempts_query);
        $stmt->bind_param("s", $client_ip);
        $stmt->execute();
    }
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
        // 로그인 성공 시 실패 기록 초기화
        $delete_ip_query = "DELETE FROM login_attempts WHERE ip_address = ?";
        $stmt = $con->prepare($delete_ip_query);
        $stmt->bind_param("s", $client_ip);
        $stmt->execute();

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
        // 로그인 실패 처리
        $attempt_count++;
        if ($attempt_count >= 5) { // 5번 초과 시 차단
            $blocked_until = date("Y-m-d H:i:s", strtotime("+2 hours")); // 2시간 차단
            $update_ip_query = "REPLACE INTO login_attempts (ip_address, attempt_count, blocked_until, last_attempt) VALUES (?, ?, ?, NOW())";
            $stmt = $con->prepare($update_ip_query);
            $stmt->bind_param("sis", $client_ip, $attempt_count, $blocked_until);
        } else {
            $update_ip_query = "REPLACE INTO login_attempts (ip_address, attempt_count, last_attempt) VALUES (?, ?, NOW())";
            $stmt = $con->prepare($update_ip_query);
            $stmt->bind_param("si", $client_ip, $attempt_count);
        }
        $stmt->execute();

        echo "<script>
                 alert('아이디 또는 비밀번호가 틀립니다.');
                 history.go(-1);
              </script>";
    }
} else {
    // 아이디가 없는 경우도 실패 처리
    $attempt_count++;
    if ($attempt_count >= 5) { // 5번 초과 시 차단
        $blocked_until = date("Y-m-d H:i:s", strtotime("+2 hours")); // 2시간 차단
        $update_ip_query = "REPLACE INTO login_attempts (ip_address, attempt_count, blocked_until, last_attempt) VALUES (?, ?, ?, NOW())";
        $stmt = $con->prepare($update_ip_query);
        $stmt->bind_param("sis", $client_ip, $attempt_count, $blocked_until);
    } else {
        $update_ip_query = "REPLACE INTO login_attempts (ip_address, attempt_count, last_attempt) VALUES (?, ?, NOW())";
        $stmt = $con->prepare($update_ip_query);
        $stmt->bind_param("si", $client_ip, $attempt_count);
    }
    $stmt->execute();

    echo "<script>
	     alert('아이디 또는 비밀번호가 틀렸습니다.');
             history.go(-1);
          </script>";
}

// 자원 정리
$stmt->close();
mysqli_close($con);
?>

