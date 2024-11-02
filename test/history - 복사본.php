<?php 
    include '../memberboard/session.php';

    // 세션에 userid가 설정되어 있는지 확인
    if (!isset($_SESSION['userid'])) {
        echo "<script>alert('로그인 후 이용해 주세요.'); window.location.href = '../login/index.php';</script>";
        exit; 
    }
?>
<!DOCTYPE html>
<html lang="ko">
<head>  
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>진단 히스토리</title> 
    <link rel="stylesheet" href="../login/css/header.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <style>
        /* 날짜 선택 큰 달력 스타일 */
        #datepicker {
            margin: 20px auto;
            width: 300px;
        }

        /* 테이블 스타일 */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }
    </style>
</head> 
<body> 
    <?php 
    // 헤더 파일 포함
    include '../login/header.php'; 
    ?>

    <div class="content" id="contentArea">
        <h1>진단 히스토리</h1>

        <!-- 날짜 선택을 위한 달력 -->
        <div class="date-filter text-center">
            <input type="text" id="datepicker" class="form-control" placeholder="날짜를 선택하세요">
        </div>

        <!-- 진단 결과를 표로 표시 -->
        <h3>세부진단 결과</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>취약점 ID</th>
                    <th>
                        영향받는 시스템<br>
                        <select id="systemFilter" class="filter-dropdown">
                            <option value="all">전체</option>
                            <option value="linux">Server(Linux)</option>
                            <option value="apache">Apache</option>
                            <option value="mysql">MySQL</option>
                            <option value="php">PHP</option>
                        </select>
                    </th>
                    <th>진단항목</th>
                    <th>
                        취약 여부<br>
                        <select id="vulnerabilityFilter" class="filter-dropdown">
                            <option value="all">전체</option>
                            <option value="V">취약</option>
                            <option value="S">안전</option>
                        </select>
                    </th>
                    <th>해결 방법</th>
                </tr>
            </thead>
            <tbody id="resultTableBody">
                <tr>
                    <td colspan="5">결과를 선택하세요.</td> <!-- 기본 안내 메시지 -->
                </tr>
            </tbody>
        </table>
    </div>

    <!-- jQuery UI 및 달력 기능 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script>
        $(function() {
    // jQuery UI Datepicker 초기화
    $("#datepicker").datepicker({
        dateFormat: 'yy-mm-dd',  // 년도-월-일 포맷으로 설정
        onSelect: function(dateText) {
            // 날짜 선택 시 AJAX로 데이터 가져오기
            fetchResultsByDate(dateText);
        }
    });

    // 필터 적용
    $('#systemFilter, #vulnerabilityFilter').change(function() {
        applyFilter();
    });

    function fetchResultsByDate(selectedDate) {
        $.ajax({
            url: 'get_results_by_date.php',  // 데이터를 불러올 PHP 파일
            method: 'GET',
            data: { date: selectedDate },  // 선택한 날짜를 전달
            dataType: 'json',
            success: function(response) {
                const resultTableBody = $("#resultTableBody");
                resultTableBody.empty(); // 기존 데이터를 초기화

                if (response.results.length > 0) {
                    response.results.forEach(function(result, index) {
                        // 취약 여부에 따른 표시 및 색상 설정
                        let vulnerabilityStatus = result.vulnerability_status ? result.vulnerability_status.trim() : '';
                        let vulnerabilityText = '';
                        let vulnerabilityColor = '';

                        // 상태에 따라 취약 여부를 표시하고 색상을 설정
                        if (vulnerabilityStatus === 'V') {
                            vulnerabilityText = '취약';
                            vulnerabilityColor = 'red'; // 빨간색
                        } else if (vulnerabilityStatus === 'S') {
                            vulnerabilityText = '안전';
                            vulnerabilityColor = 'green'; // 초록색
                        } else {
                            vulnerabilityText = '알 수 없음'; // 알 수 없는 상태 처리
                            vulnerabilityColor = 'gray'; // 기본 색상
                        }

                        // 시스템에 따른 색상 설정
                        let system = result.system ? result.system.trim().toLowerCase() : '';
                        let systemColor = '';
                        if (system.includes('linux')) {
                            systemColor = '#63CC63'; // 초록색 (Server(Linux))
                        } else if (system.includes('apache')) {
                            systemColor = '#CD426B'; // 빨간색 (Apache)
                        } else if (system.includes('mysql')) {
                            systemColor = '#ffce56'; // 노란색 (MySQL)
                        } else if (system.includes('php')) {
                            systemColor = '#6482B9'; // 파란색 (PHP)
                        }

                        // 'data-vulnerability' 속성에 원래 'V', 'S' 값을 저장
                        const row = `<tr>
                            <td>VULN-${String(index + 1).padStart(3, '0')}</td>
                            <td style="color: ${systemColor};" data-system="${result.system}">${result.system}</td>
                            <td>${result.diagnostic_item}</td>
                            <td style="color: ${vulnerabilityColor};" data-vulnerability="${vulnerabilityStatus}">${vulnerabilityText}</td>
                            <td>${result.solution}</td>
                        </tr>`;
                        resultTableBody.append(row);
                    });
                } else {
                    const emptyRow = '<tr><td colspan="5">선택한 날짜에 해당하는 데이터가 없습니다.</td></tr>';
                    resultTableBody.append(emptyRow);
                }

                applyFilter(); // 필터 적용
            },
            error: function() {
                alert("데이터를 불러오는 중 오류가 발생했습니다.");
            }
        });
    }

    function applyFilter() {
        const systemFilter = $('#systemFilter').val();
        const vulnerabilityFilter = $('#vulnerabilityFilter').val();

        $('#resultTableBody tr').each(function() {
            const system = $(this).find('td[data-system]').data('system').toLowerCase();
            const vulnerability = $(this).find('td[data-vulnerability]').data('vulnerability'); // 원래 'V', 'S' 값을 가져옴

            let systemMatch = (systemFilter === 'all' || system.includes(systemFilter));
            let vulnerabilityMatch = (vulnerabilityFilter === 'all' || vulnerability === vulnerabilityFilter);

            if (systemMatch && vulnerabilityMatch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});

    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body> 
</html>
