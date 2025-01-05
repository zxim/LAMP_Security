<?php
include "../memberboard/session.php";

// CSRF 토큰 검증
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
        die("<script>alert('CSRF 토큰 검증 실패.'); window.history.back();</script>");
    }
}

// DB 연결 설정
$config = require '../config.php';
$con = mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
if (!$con) {
    die("<script>alert('DB 연결 실패: " . mysqli_connect_error() . "'); window.history.back();</script>");
}

// 로그인 확인
if (empty($user_num) || $user_num == 0) {
    die("<script>alert('로그인 후 이용해주세요.'); window.history.back();</script>");
}

// POST 데이터 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['productName'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 0);

    // 유효성 검사
    if (empty($product_name) || $quantity <= 0) {
        die("<script>alert('상품 정보가 올바르지 않습니다.'); window.history.back();</script>");
    }

    // 상품 이름으로 product_id 검색
    $product_sql = "SELECT product_id FROM products WHERE name = ?";
    $stmt = mysqli_prepare($con, $product_sql);
    if (!$stmt) {
        die("<script>alert('쿼리 준비 실패: " . mysqli_error($con) . "'); window.history.back();</script>");
    }
    mysqli_stmt_bind_param($stmt, 's', $product_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$product_data) {
        die("<script>alert('해당 상품이 존재하지 않습니다.'); window.history.back();</script>");
    }
    $product_id = $product_data['product_id'];

    // 장바구니에 동일 상품 확인
    $check_sql = "SELECT cart_id FROM cart WHERE member_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($con, $check_sql);
    if (!$stmt) {
        die("<script>alert('쿼리 준비 실패: " . mysqli_error($con) . "'); window.history.back();</script>");
    }
    mysqli_stmt_bind_param($stmt, 'ii', $user_num, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // 동일 상품이 있으면 수량 업데이트
        $update_sql = "UPDATE cart SET quantity = quantity + ? WHERE member_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($con, $update_sql);
        if (!$stmt) {
            die("<script>alert('쿼리 준비 실패: " . mysqli_error($con) . "'); window.history.back();</script>");
        }
        mysqli_stmt_bind_param($stmt, 'iii', $quantity, $user_num, $product_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        // 새로운 상품 추가
        $insert_sql = "INSERT INTO cart (member_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $insert_sql);
        if (!$stmt) {
            die("<script>alert('쿼리 준비 실패: " . mysqli_error($con) . "'); window.history.back();</script>");
        }
        mysqli_stmt_bind_param($stmt, 'iii', $user_num, $product_id, $quantity);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    echo "<script>alert('상품이 장바구니에 추가되었습니다.'); window.location.href = 'categories.php';</script>";
} else {
    echo "<script>alert('잘못된 요청입니다.'); window.history.back();</script>";
}

mysqli_close($con);
?>
