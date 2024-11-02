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

        <!-- 그래프를 표시할 영역 -->
        <div class="graph-container">
            <canvas id="myChart"></canvas>
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
                            // 시스템 순서 정의
                            const systemOrder = {
                                'linux': 1,   // Server(Linux)
                                'apache': 2,  // Apache
                                'mysql': 3,   // MySQL
                                'php': 4      // PHP
                            };

                            // 결과를 시스템 순서에 따라 정렬
                            response.results.sort((a, b) => {
                                const systemA = a.system.toLowerCase();
                                const systemB = b.system.toLowerCase();
                                return (systemOrder[systemA] || 5) - (systemOrder[systemB] || 5); // 미정의 시스템은 마지막으로 이동
                            });

                            response.results.forEach(function(result, index) {
                                let vulnerabilityStatus = result.vulnerability_status.trim();
                                let vulnerabilityText = vulnerabilityStatus === 'V' ? '취약' : '안전';
                                let vulnerabilityColor = vulnerabilityStatus === 'V' ? 'red' : 'green';
                                let systemColor = getSystemColor(result.system);

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

                    // 그래프에 사용할 데이터 준비
                    responses.forEach((response, index) => {
                        if (response[0].results.length > 0) {
                            let safeCount = 0;
                            let vulnerableCount = 0;

                            response[0].results.forEach((result) => {
                                if (result.vulnerability_status.trim() === 'S') {
                                    safeCount++;
                                } else if (result.vulnerability_status.trim() === 'V') {
                                    vulnerableCount++;
                                }
                            });

                            safeCounts.push(safeCount);
                            vulnerableCounts.push(vulnerableCount);
                            labels.push(index < 2 ? previousDates[index] : selectedDate); // 두 날짜와 선택한 날짜 추가
                        }
                    });

                    drawChart(labels, safeCounts, vulnerableCounts);
                }).fail(function() {
                    alert('그래프 데이터를 불러오는 중 오류가 발생했습니다.');
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
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body> 
</html>
