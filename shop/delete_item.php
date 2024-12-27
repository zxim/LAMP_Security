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

$data = json_decode(file_get_contents("php://input"), true);
$cartId = $data['cartId'] ?? 0;

if ($cartId) {
    $sql = "DELETE FROM cart WHERE cart_id = $cartId AND member_id = $user_num";
    if (mysqli_query($con, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($con)]);
    }
} else {
    echo json_encode(["success" => false, "message" => "잘못된 요청"]);
}

mysqli_close($con);
?>
