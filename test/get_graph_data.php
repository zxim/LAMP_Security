<?php
// DB 연결 설정
$config = require '../config.php';  // config.php에서 DB 정보 가져오기
$conn = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);

// 연결 확인
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'DB 연결 실패']));
}

$date = $_GET['date'];  // 전달받은 날짜

// 날짜별 안전, 취약 데이터를 가져오는 쿼리
$sql = "SELECT vulnerability_status, COUNT(*) as count 
        FROM security_results 
        WHERE date(diagnosis_date) <= ? 
        GROUP BY vulnerability_status";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$safeData = 0;
$vulnerableData = 0;

while ($row = $result->fetch_assoc()) {
    if ($row['vulnerability_status'] === 'S') {
        $safeData = $row['count'];
    } elseif ($row['vulnerability_status'] === 'V') {
        $vulnerableData = $row['count'];
    }
}

$stmt->close();
$conn->close();

// 결과를 JSON으로 반환
echo json_encode([
    'success' => true,
    'labels' => [$date],
    'safeData' => [$safeData],
    'vulnerableData' => [$vulnerableData]
]);
