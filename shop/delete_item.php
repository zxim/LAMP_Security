<?php
include "../memberboard/session.php";

header("Content-Type: application/json");

// DB 연결 설정
$config = require '../config.php';
$db_host = $config['DB_HOST'];
$db_user = $config['DB_USER'];
$db_password = $config['DB_PASSWORD'];
$db_name = $config['DB_NAME'];

$con = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$con) {
    echo json_encode(["success" => false, "message" => "DB 연결 실패"]);
    exit;
}

// 요청 데이터 가져오기 및 검증
$data = json_decode(file_get_contents("php://input"), true);
$cartId = $data['cartId'] ?? null;
$csrfToken = $data['csrf_token'] ?? null;

// CSRF 토큰 검증
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($csrfToken) || $csrfToken !== $_SESSION['csrf_token']) {
    echo json_encode(["success" => false, "message" => "CSRF 토큰 검증 실패"]);
    exit;
}

// 입력값 검증
if (!is_numeric($cartId) || $cartId <= 0) {
    echo json_encode(["success" => false, "message" => "잘못된 요청"]);
    exit;
}

// SQL 인젝션 방지: Prepared Statement 사용
$sql = "DELETE FROM cart WHERE cart_id = ? AND member_id = ?";
$stmt = mysqli_prepare($con, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $cartId, $user_num);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "쿼리 실행 실패"]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["success" => false, "message" => "쿼리 준비 실패"]);
}

mysqli_close($con);
?>
