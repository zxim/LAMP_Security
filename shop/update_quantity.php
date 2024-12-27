<?php
include "../memberboard/session.php";

$config = require '../config.php';
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $cartId = intval($data['cartId']);
    $quantity = intval($data['quantity']);

    if ($cartId > 0 && $quantity > 0) {
        $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ? AND member_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $quantity, $cartId, $user_num);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false]);
        }
    } else {
        echo json_encode(["success" => false]);
    }
}
mysqli_close($con);
?>
