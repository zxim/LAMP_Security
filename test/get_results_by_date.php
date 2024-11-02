<?php
header('Content-Type: application/json');
session_start();

// 세션에 userid가 있는지 확인
if (!isset($_SESSION['userid'])) {
    echo json_encode(['error' => '로그인되지 않았습니다.']);
    exit;
}

$userId = $_SESSION['userid'];  // 세션에서 로그인된 사용자 ID 가져오기
$date = $_GET['date'];  // 요청으로부터 날짜 가져오기

// DB 연결
$config = require '../config.php';
$conn = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);

// 연결 확인
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 선택한 날짜와 일치하는 데이터를 가져오는 쿼리
$sql = "SELECT diagnostic_item, system, vulnerability_status, solution 
        FROM security_results 
        WHERE user_id = ? AND DATE(created_at) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $userId, $date);  // 사용자 ID와 선택된 날짜 바인딩
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}

// 결과를 JSON 형식으로 반환
echo json_encode(['results' => $results]);

$stmt->close();
$conn->close();
?>
