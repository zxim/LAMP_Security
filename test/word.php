<?php
// 오류 출력 설정 (디버깅을 위해)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php'; // PHPWord autoload

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// CSV 파일 경로 설정 (실제 파일 경로로 수정)
$csvFilePath = './csv/Diag_File.csv'; 

// CSV 파일이 존재하는지 확인
if (!file_exists($csvFilePath)) {
    die('CSV 파일을 찾을 수 없습니다.');
}

// CSV 파일을 읽어오기
$csvData = [];
if (($handle = fopen($csvFilePath, 'r')) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (empty(array_filter($data))) {
            continue;
        }
        $csvData[] = $data;
    }
    fclose($handle);
} else {
    die('CSV 파일을 열 수 없습니다.');
}

// LAMP 분류별 테이블 데이터 저장
$linuxData = [];
$apacheData = [];
$mysqlData = [];
$phpData = [];

// CSV 데이터를 각 분류(LAMP)에 맞게 나누기 (3번째 열이 V인 것만 추가)
foreach ($csvData as $row) {
    $category = isset($row[0]) ? strtolower(trim($row[0])) : '';
    $status = isset($row[2]) ? strtoupper(trim($row[2])) : '';

    if ($status === 'V') {
        switch ($category) {
            case 'server(linux)':
                $linuxData[] = $row;
                break;
            case 'apache':
                $apacheData[] = $row;
                break;
            case 'mysql':
                $mysqlData[] = $row;
                break;
            case 'php':
                $phpData[] = $row;
                break;
            default:
                break;
        }
    }
}

// PHPWord 객체 생성
$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Malgun Gothic');
$phpWord->setDefaultFontSize(10);

// 테이블 스타일 설정
$tableStyle = array(
    'borderSize' => 6,
    'borderColor' => '999999',
    'cellMargin' => 50,
    'alignment' => 'center',
    'width' => 100 * 70
);
$firstRowStyle = array('bgColor' => 'ADD8E6');
$phpWord->addTableStyle('SecurityTable', $tableStyle, $firstRowStyle);

// CVE 데이터 추가 (간단히 CVE 번호만 포함)
$cveData = [
    'Server(Linux)' => [
        'CVE-2024-1086',
        'CVE-2023-3390',
        'CVE-2023-0266',
        'CVE-2022-47939'
    ],
    'Apache' => [
        'CVE-2024-38476',
        'CVE-2021-41773',
        'CVE-2022-23943',
        'CVE-2022-30522'
    ],
    'MySQL' => [
        'CVE-2023-21875',
        'CVE-2022-21592',
        'CVE-2023-22028',
        'CVE-2023-22084'
    ],
    'PHP' => [
        'CVE-2024-4577',
        'CVE-2022-31630',
        'CVE-2021-21703',
        'CVE-2021-21702'
    ]
];

// 테이블 생성 함수
function addCombinedTable($phpWord, $title, $details, $tableData) {
    global $cveData;
    
    // 페이지당 하나의 섹션 추가
    $section = $phpWord->addSection(['breakType' => 'continuous']); // 새로운 섹션을 각 페이지로 구분

    $section->addText($title . ' 취약점 분석', array('bold' => true, 'size' => 14));
    $section->addTextBreak(1);

    $table = $section->addTable('SecurityTable');

    $table->addRow();
    $table->addCell(2000, array('bgColor' => 'ADD8E6'))->addText('구분', array('bold' => true, 'alignment' => 'center'));
    $table->addCell(7000, array('bgColor' => 'ADD8E6'))->addText('상세 내용', array('bold' => true, 'alignment' => 'center'));

    $table->addRow();
    $table->addCell(2000)->addText('취약점 명', array('bold' => true, 'alignment' => 'center'));
    $table->addCell(7000)->addText($title . ' 취약점', array('alignment' => 'left'));

    $table->addRow();
    $table->addCell(2000)->addText('상세 정보', array('bold' => true, 'alignment' => 'center'));
    $table->addCell(7000)->addText($details, array('alignment' => 'left'));

    $description = [];
    $solution = [];

    foreach ($tableData as $row) {
        $item1 = isset($row[1]) ? trim($row[1]) : '';
        $item4 = isset($row[3]) ? trim($row[3]) : '';

        if (!empty($item1)) {
            $description[] = $item1;
        }
        if (!empty($item4)) {
            $solution[] = $item4;
        }
    }

    $table->addRow();
    $table->addCell(2000)->addText('취약점 설명', array('bold' => true, 'alignment' => 'center'));
    $textRunDescription = $table->addCell(7000)->addTextRun();
    foreach ($description as $index => $line) {
        $textRunDescription->addText(($index + 1) . '. ' . $line);
        $textRunDescription->addTextBreak();
    }

    $table->addRow();
    $table->addCell(2000)->addText('해결방안', array('bold' => true, 'alignment' => 'center'));
    $textRunSolution = $table->addCell(7000)->addTextRun();
    foreach ($solution as $index => $line) {
        $textRunSolution->addText(($index + 1) . '. ' . $line);
        $textRunSolution->addTextBreak();
    }

    $table->addRow();
    $table->addCell(2000)->addText('주의사항', array('bold' => true, 'alignment' => 'center'));
    $textRunWarning = $table->addCell(7000)->addTextRun();
    $textRunWarning->addText('업데이트 시 시스템/서비스에 미치는 영향도 확인 후 적용', array('color' => 'FF0000', 'alignment' => 'left'));

    $table->addRow();
    $table->addCell(2000)->addText('참고자료', array('bold' => true, 'alignment' => 'center'));
    $textRunReferences = $table->addCell(7000)->addTextRun();
    foreach ($cveData[$title] as $cve) {
        $textRunReferences->addText($cve);
        $textRunReferences->addTextBreak();
    }
}

// LAMP 각 분류에 맞는 테이블 생성
if (!empty($linuxData)) {
    addCombinedTable($phpWord, 'Server(Linux)', 'Amazon Linux 2023', $linuxData);
}
if (!empty($apacheData)) {
    addCombinedTable($phpWord, 'Apache', 'Apache/2.4.62 (Amazon Linux)', $apacheData);
}
if (!empty($mysqlData)) {
    addCombinedTable($phpWord, 'MySQL', 'MySQL 8.0.40', $mysqlData);
}
if (!empty($phpData)) {
    addCombinedTable($phpWord, 'PHP', 'PHP 8.3.10', $phpData);
}

// Word 파일을 생성하여 다운로드
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="LAMP_취약_항목_분석_보고서.docx"');
header('Cache-Control: max-age=0');

$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
