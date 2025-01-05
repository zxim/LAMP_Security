<?php
include "../memberboard/session.php";

header('Content-Type: application/json');

// DB 연결 정보
$config = require '../config.php';
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    echo json_encode(["success" => false, "message" => "DB 연결 실패"]);
    exit();
}

// 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 데이터 가져오기
    $data = json_decode(file_get_contents("php://input"), true);
    $cartId = intval($data['cartId'] ?? 0);
    $quantity = intval($data['quantity'] ?? 0);
    $csrfToken = $data['csrf_token'] ?? '';

    // CSRF 토큰 검증
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($csrfToken) || $csrfToken !== $_SESSION['csrf_token']) {
        echo json_encode(["success" => false, "message" => "CSRF 토큰 검증 실패"]);
        exit();
    }

    // 입력값 검증
    if ($cartId <= 0 || $quantity <= 0) {
        echo json_encode(["success" => false, "message" => "잘못된 입력값"]);
        exit();
    }

    // SQL 실행
    $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ? AND member_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "쿼리 준비 실패: " . mysqli_error($con)]);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "iii", $quantity, $cartId, $user_num);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "수량이 성공적으로 업데이트되었습니다."]);
    } else {
        echo json_encode(["success" => false, "message" => "수량 업데이트 실패: " . mysqli_stmt_error($stmt)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["success" => false, "message" => "잘못된 요청 방식"]);
}

mysqli_close($con);
?>
