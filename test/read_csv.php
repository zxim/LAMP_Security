<?php
header('Content-Type: application/json');
session_start(); // 세션 시작

if (!isset($_SESSION['userid'])) {
    // 사용자가 로그인하지 않은 경우
    echo json_encode(['error' => '사용자가 로그인되어 있지 않습니다.']);
    exit;
}

$userId = $_SESSION['userid']; // 세션에서 사용자 ID 가져오기

$csvDir = './csv/'; // CSV 파일이 저장된 경로
$csvFiles = glob($csvDir . '*.csv'); // 해당 디렉토리에서 CSV 파일 목록 가져오기

$results = []; // 모든 파일의 결과를 저장할 배열
$vulnerableCount = 0;
$secureCount = 0;

// DB 연결 설정
$config = require '../config.php';  // config.php에서 DB 정보 가져오기
$conn = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);

// 연결 확인
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

if (count($csvFiles) > 0) {
    $sql = "INSERT INTO security_results (diagnostic_item, system, vulnerability_status, solution, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // CSV 파일 목록 순서대로 처리
    foreach ($csvFiles as $csvFile) {
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            // 헤더를 처리하기 위해 첫 줄을 읽음
            //$headers = fgetcsv($handle);

            // 모든 줄 읽기
            while (($data = fgetcsv($handle)) !== FALSE) {
                // 행에서 모든 요소가 비어있는지 확인하고, 비어있는 경우 건너뛰기
                if (empty(array_filter($data, 'trim'))) {
                    continue; // 빈 행을 건너뜀
                }

                // CSV에서 데이터 추출 및 트림
                $system = trim($data[0], "\"'");
                $diagnosis = trim($data[1], "\"'");
                $vulnerabilityStatus = strtoupper(trim($data[2], "\"'")); // Trim and ensure uppercase 'S' or 'V'
                $solution = trim($data[3], "\"'");

                // 취약 여부 카운팅
                if ($vulnerabilityStatus === 'V') {
                    $vulnerableCount++;
                } elseif ($vulnerabilityStatus === 'S') {
                    $secureCount++;
                }

                // DB에 삽입 (user_id 추가)
                $stmt->bind_param("sssss", $diagnosis, $system, $vulnerabilityStatus, $solution, $userId);
                $stmt->execute();

                // 결과 배열에 추가
                $results[] = [$system, $diagnosis, $vulnerabilityStatus, $solution];
            }
            fclose($handle);
        }
    }

    // 결과를 JSON으로 반환
    echo json_encode([
        'results' => $results,
        'chartData' => [$vulnerableCount, $secureCount] // 취약, 안전 순서로 반환
    ]);

    // 자원 해제
    $stmt->close();
} else {
    echo json_encode(['error' => 'CSV 파일이 존재하지 않습니다.']);
}

$conn->close(); // DB 연결 종료
?>