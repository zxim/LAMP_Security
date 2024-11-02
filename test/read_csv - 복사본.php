<?php
header('Content-Type: application/json');

$csvDir = './csv/'; // CSV 파일이 저장된 경로
$csvFiles = glob($csvDir . '*.csv'); // 해당 디렉토리에서 CSV 파일 목록 가져오기

$results = []; // 모든 파일의 결과를 저장할 배열
$vulnerableCount = 0;
$secureCount = 0;

if (count($csvFiles) > 0) {
    // CSV 파일 목록 순서대로 처리
    foreach ($csvFiles as $csvFile) {
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            // 모든 줄 읽기
            while (($data = fgetcsv($handle)) !== FALSE) {
                // 빈 줄 또는 데이터가 누락된 경우 무시
                if (empty(array_filter($data))) {
                    continue;
                }

                // 영향받는 시스템, 진단항목, 취약 여부, 해결 방법을 가져옴
                $system = trim($data[0], "\"'"); // 영향받는 시스템
                $diagnosis = trim($data[1], "\"'"); // 진단항목
                $vulnerabilityStatus = trim($data[2], "\"'"); // 'v' 또는 's' 그대로 사용
                $solution = trim($data[3], "\"'"); // 해결 방법

                // 취약 여부 카운팅 (그래프 데이터용)
                if (strtolower($vulnerabilityStatus) === 'v') {
                    $vulnerableCount++;
                } else {
                    $secureCount++;
                }

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
} else {
    echo json_encode(['error' => 'CSV 파일이 존재하지 않습니다.']);
}
?>

