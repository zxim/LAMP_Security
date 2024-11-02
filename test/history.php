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
            width: 100%; /* 테이블 폭을 100%로 설정 */
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

        /* 그래프 스타일 */
        .graph-container {
            width: 80%;
            margin: 40px auto;
            display: none; /* 초기에는 숨김 */
        }

        canvas {
            width: 100% !important;
            height: 400px !important;
        }

        /* 사이드바 스타일 */
        #sidebar {
            position: fixed;
            top: 0;
            right: -250px; /* 사이드바 숨김 */
            width: 250px;
            height: 100%;
            background-color: #343a40; /* 어두운 배경색 */
            color: white; /* 텍스트 색상 */
            border-left: 1px solid #ccc;
            transition: right 0.3s;
            z-index: 1000; /* 가장 위에 표시 */
            padding: 20px;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5); /* 그림자 효과 */
        }

        #sidebar.active {
            right: 0; /* 사이드바 보이기 */
        }

        #sidebar h4 {
            color: #ffffff; /* 헤더 색상 */
        }

        #sidebar ul {
            list-style: none; /* 불릿 제거 */
            padding: 0;
        }

        #sidebar ul li {
            margin: 10px 0; /* 항목 간 간격 */
        }

        #sidebar ul li a {
            color: #ffffff; /* 링크 색상 */
            text-decoration: none; /* 밑줄 제거 */
            transition: color 0.2s; /* 색상 변화 애니메이션 */
        }

        #sidebar ul li a:hover {
            color: #63CC63; /* 호버 시 색상 */
        }

        #toggleSidebar {
            position: fixed;
            top: 20px;
            right: 10px;
            z-index: 1100; /* 사이드바보다 위에 표시 */
            transition: right 0.3s; /* 사이드바가 보일 때 버튼이 움직이는 애니메이션 */
        }

        /* 비교 결과 스타일 */
        .comparison-table {
            width: 100%; /* 테이블 폭을 100%로 설정 */
            margin: 40px auto;
        }

        .vulnerability-status-safe {
            color: green; /* 안전 상태 글씨 색상 */
        }

        .vulnerability-status-vulnerable {
            color: red; /* 취약 상태 글씨 색상 */
        }
    </style>
</head> 
<body> 
    <?php 
    // 헤더 파일 포함
    include '../login/header.php'; 
    ?>

    <div id="sidebar">
        <h4>섹션 선택</h4>
        <ul>
            <li><a href="#graphSection">진단 그래프</a></li>
            <li><a href="#resultSection">세부진단 결과</a></li>
            <li><a href="#comparisonSection">취약 여부 변동 비교</a></li>
        </ul>
    </div>

    <button id="toggleSidebar" class="btn btn-primary">☰</button>

    <div class="content" id="contentArea">
        <h1>진단 히스토리</h1>

        <!-- 날짜 선택을 위한 달력 -->
        <div class="date-filter text-center">
            <input type="text" id="datepicker" class="form-control" placeholder="날짜를 선택하세요">
        </div>

        <!-- 그래프를 표시할 영역 -->
        <h3 id="graphSection">진단 그래프</h3>
        <div class="graph-container">
            <canvas id="myChart"></canvas>
        </div>

        <!-- 진단 결과를 표로 표시 -->
        <h3 id="resultSection">세부진단 결과</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>취약점 ID</th>
                    <th>
                        영향받는 시스템<br>
                        <select id="systemFilter" class="filter-dropdown">
                            <option value="all" style="color: black;">전체</option>
                            <option value="linux" style="color: #63CC63;">Server(Linux)</option>
                            <option value="apache" style="color: #CD426B;">Apache</option>
                            <option value="mysql" style="color: #ffce56;">MySQL</option>
                            <option value="php" style="color: #6482B9;">PHP</option>
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

        <!-- 비교 결과 표시 -->
        <div class="comparison-table">
            <h3 id="comparisonSection">취약 여부 변동 비교</h3>
            <table class="table table-bordered" id="comparisonTable">
                <thead>
                    <tr>
                        <th>영향받는 시스템</th>
                        <th>진단항목</th>
                        <th>현재 취약 여부</th>
                        <th>이전 취약 여부</th>
                    </tr>
                </thead>
                <tbody id="comparisonTableBody">
                    <tr>
                        <td colspan="4">비교할 데이터가 없습니다.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery UI 및 달력 기능 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let myChart; // 그래프 인스턴스를 전역변수로 선언

        $(function() {
            // jQuery UI Datepicker 초기화
            $("#datepicker").datepicker({
                dateFormat: 'yy-mm-dd',  // 년도-월-일 포맷으로 설정
                onSelect: function(dateText) {
                    // 날짜 선택 시 AJAX로 데이터 가져오기
                    fetchResultsByDate(dateText);
                    fetchGraphData(dateText); // 날짜 선택 시 그래프 데이터도 가져오기
                    fetchComparisonData(dateText); // 취약 여부 변동 비교 데이터 가져오기
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
                                let vulnerabilityStatus = result.vulnerability_status.trim();
                                let vulnerabilityText = vulnerabilityStatus === 'V' ? '취약' : '안전';
                                let vulnerabilityColor = vulnerabilityStatus === 'V' ? 'red' : 'green';
                                let systemColor = getSystemColor(result.system);
                            
                                // 취약인 경우 배경색을 회색으로 설정
                                const row = `<tr style="background-color: ${vulnerabilityStatus === 'V' ? '#f8f8f8' : 'white'};">
                                    <td>VULN-${String(index + 1).padStart(3, '0')}</td>
                                    <td style="color: ${systemColor};" data-system="${result.system}">${result.system}</td>
                                    <td>${result.diagnostic_item}</td>
                                    <td style="color: ${vulnerabilityColor};" data-vulnerability="${vulnerabilityStatus}">${vulnerabilityText}</td>
                                    <td>${result.solution}</td>
                                </tr>`;
                                resultTableBody.append(row);
                            });
                        } else {
                            resultTableBody.append('<tr><td colspan="5">선택한 날짜에 해당하는 데이터가 없습니다.</td></tr>');
                        }

                        applyFilter(); // 필터 적용
                    },
                    error: function() {
                        alert("데이터를 불러오는 중 오류가 발생했습니다.");
                    }
                });
            }

            function fetchGraphData(selectedDate) {
                const previousDates = [];
                const dataPromises = [];
                        
                // 선택한 날짜 이전의 두 날짜를 가져오는 AJAX 요청
                for (let i = 1; i <= 2; i++) {
                    const previousDate = new Date(selectedDate);
                    previousDate.setDate(previousDate.getDate() - i);
                    previousDates.push(previousDate.toISOString().split('T')[0]); // yyyy-mm-dd 형식
                }
            
                // AJAX 요청을 통해 데이터 가져오기
                previousDates.forEach((date) => {
                    dataPromises.push($.ajax({
                        url: 'get_results_by_date.php',
                        method: 'GET',
                        data: { date: date },
                        dataType: 'json'
                    }));
                });
            
                // 선택한 날짜에 대한 데이터도 요청
                dataPromises.push($.ajax({
                    url: 'get_results_by_date.php',
                    method: 'GET',
                    data: { date: selectedDate },
                    dataType: 'json'
                }));
            
                $.when(...dataPromises).done(function(...responses) {
                    const safeCounts = [];
                    const vulnerableCounts = [];
                    const labels = [];
                
                    // 데이터 준비 및 라벨 순서 설정
                    // 첫 번째 날짜 (가장 빠른)
                    const firstResponse = responses[0][0];
                    if (firstResponse.results.length > 0) {
                        let safeCount = 0;
                        let vulnerableCount = 0;
                    
                        firstResponse.results.forEach((result) => {
                            if (result.vulnerability_status.trim() === 'S') {
                                safeCount++;
                            } else if (result.vulnerability_status.trim() === 'V') {
                                vulnerableCount++;
                            }
                        });
                    
                        safeCounts.push(safeCount);
                        vulnerableCounts.push(vulnerableCount);
                        labels.push(previousDates[1]); // 첫 번째 날짜 (가장 빠른)
                    }
                
                    // 두 번째 날짜 (그 다음 날짜)
                    const secondResponse = responses[1][0];
                    if (secondResponse.results.length > 0) {
                        let safeCount = 0;
                        let vulnerableCount = 0;
                    
                        secondResponse.results.forEach((result) => {
                            if (result.vulnerability_status.trim() === 'S') {
                                safeCount++;
                            } else if (result.vulnerability_status.trim() === 'V') {
                                vulnerableCount++;
                            }
                        });
                    
                        safeCounts.push(safeCount);
                        vulnerableCounts.push(vulnerableCount);
                        labels.push(previousDates[0]); // 두 번째 날짜 (그 다음)
                    }
                
                    // 마지막 날짜 (선택한 날짜)
                    const currentResponse = responses[2][0];
                    if (currentResponse.results.length > 0) {
                        let safeCount = 0;
                        let vulnerableCount = 0;
                    
                        currentResponse.results.forEach((result) => {
                            if (result.vulnerability_status.trim() === 'S') {
                                safeCount++;
                            } else if (result.vulnerability_status.trim() === 'V') {
                                vulnerableCount++;
                            }
                        });
                    
                        safeCounts.push(safeCount);
                        vulnerableCounts.push(vulnerableCount);
                        labels.push(selectedDate); // 마지막 날짜 (선택한 날짜)
                    }
                
                    drawChart(labels, safeCounts, vulnerableCounts);
                }).fail(function() {
                    alert('그래프 데이터를 불러오는 중 오류가 발생했습니다.');
                });
            }


            function fetchComparisonData(selectedDate) {
                const previousDates = [];
                const previousDataMap = {}; // 이전 데이터를 저장할 맵

                // 선택한 날짜 기준으로 7일 전부터 데이터 요청
                for (let i = 1; i <= 7; i++) {
                    const previousDate = new Date(selectedDate);
                    previousDate.setDate(previousDate.getDate() - i);
                    previousDates.push(previousDate.toISOString().split('T')[0]); // yyyy-mm-dd 형식
                }

                const comparisonTableBody = $("#comparisonTableBody");
                comparisonTableBody.empty(); // 기존 비교 데이터 초기화

                const previousPromises = previousDates.map(date => {
                    return $.ajax({
                        url: 'get_results_by_date.php',
                        method: 'GET',
                        data: { date: date },
                        dataType: 'json'
                    });
                });

                // 선택한 날짜의 데이터 가져오기
                previousPromises.push($.ajax({
                    url: 'get_results_by_date.php',
                    method: 'GET',
                    data: { date: selectedDate },
                    dataType: 'json'
                }));

                $.when(...previousPromises).done(function(...responses) {
                    const currentResults = responses[responses.length - 1][0].results; // 선택한 날짜의 데이터
                    const previousResults = responses.slice(0, -1).map(response => response[0].results); // 이전 날짜의 데이터들

                    // 진단 항목 별로 마지막 상태만 추출
                    previousResults.forEach(dayResults => {
                        dayResults.forEach(result => {
                            previousDataMap[result.diagnostic_item] = result; // 진단항목으로 상태를 맵에 저장
                        });
                    });

                    // 현재 데이터와 비교하여 변동이 있는 경우만 추가
                    currentResults.forEach(currentItem => {
                        const currentDiagnosticItem = currentItem.diagnostic_item;
                        const currentVulnerability = currentItem.vulnerability_status.trim();
                        const previousItem = previousDataMap[currentDiagnosticItem];

                        // 상태가 변동된 경우 추가
                        if (previousItem && previousItem.vulnerability_status.trim() !== currentVulnerability) {
                            const comparisonRow = `<tr>
                                <td style="color: ${getSystemColor(previousItem.system)};">${previousItem.system}</td>
                                <td>${currentDiagnosticItem}</td>
                                <td class="${currentVulnerability === 'V' ? 'vulnerability-status-vulnerable' : 'vulnerability-status-safe'}">${currentVulnerability === "V" ? "취약" : "안전"}</td>
                                <td class="${previousItem.vulnerability_status.trim() === 'V' ? 'vulnerability-status-vulnerable' : 'vulnerability-status-safe'}">${previousItem.vulnerability_status.trim() === "V" ? "취약" : "안전"}</td>
                            </tr>`;
                            comparisonTableBody.append(comparisonRow);
                        }
                    });

                    if (comparisonTableBody.children().length === 0) {
                        comparisonTableBody.append('<tr><td colspan="4">변동된 데이터가 없습니다.</td></tr>');
                    }
                }).fail(function() {
                    alert('비교 데이터를 불러오는 중 오류가 발생했습니다.');
                });
            }

            function drawChart(labels, safeData, vulnerableData) {
                const ctx = document.getElementById('myChart').getContext('2d');

                // 기존 그래프가 있다면 제거
                if (myChart) {
                    myChart.destroy();
                }

                // 새 그래프 생성
                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels, // 날짜 라벨들
                        datasets: [
                            {
                                label: '안전',
                                data: safeData, // 안전 데이터
                                backgroundColor: 'green'
                            },
                            {
                                label: '취약',
                                data: vulnerableData, // 취약 데이터
                                backgroundColor: 'red'
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            x: {
                                ticks: {
                                    color: 'black', // 글씨 색상
                                    font: {
                                        weight: 'bold', // 글씨 두께 설정
                                    }
                                }
                            }
                        }
                    }
                });

                $('.graph-container').show(); // 그래프를 표시
            }

            function getSystemColor(system) {
                system = system.toLowerCase().trim();
                if (system.includes('linux')) {
                    return '#63CC63'; // 초록색 (Server(Linux))
                } else if (system.includes('apache')) {
                    return '#CD426B'; // 빨간색 (Apache)
                } else if (system.includes('mysql')) {
                    return '#ffce56'; // 노란색 (MySQL)
                } else if (system.includes('php')) {
                    return '#6482B9'; // 파란색 (PHP)
                }
                return 'gray'; // 기본 색상
            }

            function applyFilter() {
                const systemFilter = $('#systemFilter').val();
                const vulnerabilityFilter = $('#vulnerabilityFilter').val();

                $('#resultTableBody tr').each(function() {
                    const system = $(this).find('td[data-system]').data('system').toLowerCase();
                    const vulnerability = $(this).find('td[data-vulnerability]').data('vulnerability');

                    let systemMatch = (systemFilter === 'all' || system.includes(systemFilter));
                    let vulnerabilityMatch = (vulnerabilityFilter === 'all' || vulnerability === vulnerabilityFilter);

                    if (systemMatch && vulnerabilityMatch) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            // 사이드바 토글 기능
            $('#toggleSidebar').click(function() {
                $('#sidebar').toggleClass('active');
                $('#toggleSidebar').css('right', $('#sidebar').hasClass('active') ? '260px' : '10px'); // 버튼 위치 조정
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body> 
</html>
