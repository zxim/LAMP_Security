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
    <title>진단결과</title> 
    <link rel="stylesheet" href="../login/css/header.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <style>
        /* 두 개의 그래프를 가로로 나란히 배치하기 위한 스타일 */
        .graph-container {
            display: none; /* 기본적으로 그래프는 숨김 */
            justify-content: space-around; /* 그래프 사이 간격 조절 */
            align-items: center; /* 세로 정렬 */
            width: 100%; /* 부모 요소의 너비에 맞춤 */
            margin-top: 20px; /* 그래프와 표 사이 간격 */
        }

        .canvas-container {
            width: 45%; /* 각 그래프 너비를 45%로 줄여 두 개가 나란히 배치되도록 설정 */
        }

        canvas {
            max-width: 100%; /* 캔버스가 부모 요소의 너비에 맞도록 설정 */
            height: auto; /* 높이는 자동으로 조정 */
        }

        /* 진단 결과와 버튼 사이 기본 간격 */
        .results-hidden .graph-button {
            margin-bottom: 10px; /* 결과 보기 버튼과 세부 진단 결과 간격을 좁게 설정 */
        }

        /* 그래프가 표시될 때 간격 조정 */
        .results-visible .graph-container {
            display: flex; /* 그래프를 표시 */
        }

        .results-visible .graph-button {
            margin-bottom: 40px; /* 그래프가 표시된 후에는 간격을 넓힘 */
        }

        /* 취약 행의 배경색을 연한 회색으로 설정 */
        .vulnerable-row {
            background-color: #f0f0f0; /* 연한 회색 */
        }
    </style>
</head> 
<body> 
    <?php 
    include '../login/header.php'; // 헤더 파일 포함

    ?>

    <div class="content results-hidden" id="contentArea">
        <h1>진단결과</h1>

        <!-- 결과보기 버튼 -->
        <div class="text-center my-3">
            <button class="graph-button" id="startTestBtn">결과보기</button>
            <button class="custom-button" id="changeGraphBtn">막대 그래프로 변경</button>
            <button class="custom-button" id="generateReportBtn">보안 진단 보고서 생성</button>
        </div>

        <!-- 그래프를 가로로 나란히 배치 -->
        <div class="graph-container" id="graphContainer">
            <!-- LAMP 구성요소 취약 여부 분포를 나타내는 그래프 -->
            <div class="canvas-container">
                <canvas id="lampChart"></canvas>
            </div>

            <!-- 새로운 안전 vs 취약 비율 그래프 -->
            <div class="canvas-container">
                <canvas id="vulnerabilityChart"></canvas>
            </div>  
        </div>

        <!-- 세부 진단 결과를 표로 표시 -->
        <h3>세부진단 결과</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>취약점 ID</th>
                    <th>
                        영향받는 시스템<br>
                        <select id="systemFilter" class="filter-dropdown">
                            <option value="all">전체</option>
                            <option value="Server(Linux)">Server(Linux)</option>
                            <option value="Apache">Apache</option>
                            <option value="Mysql">MY-SQL</option>
                            <option value="PHP">PHP</option>
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
                <!-- 여기서 PHP를 통해 CSV 데이터를 불러와 삽입 -->
            </tbody>
        </table>
    </div>

    <!-- Chart.js 라이브러리를 이용한 그래프 생성 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.onload = function() {
            window.onload = function() {
            document.getElementById('startTestBtn').addEventListener('click', function() {
            // Ajax 요청을 통해 read_csv.php로 CSV 데이터를 요청
            fetch('read_csv.php')
                .then(response => response.json())
                .then(data => {
                    if (data.results) {
                        allData = data.results.map(row => row.map(cell => cell.trim()));
                        updateGraphData(allData); // 차트 데이터를 업데이트
                        applyFilter(); // 필터 적용 후 테이블에 데이터 표시
                        drawCharts(); // 차트를 그리는 함수 호출
                    }
                })
                .catch(error => console.error('Error fetching CSV data:', error));
            });
        };





            let chartType = 'pie'; // 기본 그래프 타입은 원형 그래프
            let lampChart; // 차트 변수
            let vulnerabilityChart; // 안전 vs 취약 차트 변수
            let allData = []; // 전체 데이터를 저장할 배열

            // LAMP 스택의 각 항목에 대한 카운트
            let lampData = {
                'Server(Linux)': 0,
                Apache: 0,
                'MY-SQL': 0,
                PHP: 0
            };

            // 안전 vs 취약 데이터
            let vulnerabilityData = {
                safe: 0,
                vulnerable: 0
            };

            // LAMP 항목별 색상 매칭
            const lampColors = {
                'Server(Linux)': '#63CC63', // Server(Linux) 색상
                Apache: '#CD426B',          // Apache 색상
                'MY-SQL': '#ffce56',        // MySQL 색상
                PHP: '#6482B9'              // PHP 색상
            };

            // 취약/안전 항목 색상
            const vulnerabilityColors = {
                safe: 'rgb(75, 192, 75)',    // 초록색
                vulnerable: 'rgb(255, 0, 0)' // 빨간색
            };

            // '진단하기' 버튼 클릭 이벤트
            document.getElementById('startTestBtn').addEventListener('click', function() {
                // 그래프를 표시하고 간격을 넓힘
                const contentArea = document.getElementById('contentArea');
                contentArea.classList.remove('results-hidden');
                contentArea.classList.add('results-visible');
                document.getElementById('graphContainer').style.display = 'flex'; // 그래프 컨테이너 표시

                // Ajax 요청을 통해 read_csv.php로 CSV 데이터를 요청
                fetch('read_csv.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.results) {
                            allData = data.results.map(row => row.map(cell => cell.trim())); // 전체 데이터를 저장하고 공백 제거
                            updateGraphData(allData);
                            applyFilter(); // 필터 적용 후 테이블에 데이터 표시
                            drawCharts(); // 차트 그리기 함수 호출
                        }
                    })
                    .catch(error => console.error('Error fetching CSV data:', error));
            });

            // 필터에 맞는 데이터를 테이블에 적용하는 함수
            function applyFilter() {
                const systemFilter = document.getElementById('systemFilter').value;
                const vulnerabilityFilter = document.getElementById('vulnerabilityFilter').value;

                const resultTableBody = document.getElementById('resultTableBody');
                resultTableBody.innerHTML = ''; // 기존 데이터를 지움

                let filteredData = allData.filter(row => {
                    const system = row[0].trim(); // 영향받는 시스템
                    const vulnerabilityStatus = row[2].trim().toUpperCase(); // 취약 여부를 대문자로 변환

                    // 필터 조건에 맞는 데이터만 남김
                    return (systemFilter === 'all' || system === systemFilter) &&
                           (vulnerabilityFilter === 'all' || vulnerabilityStatus === vulnerabilityFilter);
                });

                // 필터 적용 후에도 취약점 ID 순서대로 번호를 부여
                filteredData.forEach((row, index) => {
                    const tr = document.createElement('tr');

                    // 취약점 ID 열 생성 (VULN-001 형식으로 자동 증가)
                    const tdIndex = document.createElement('td');
                    tdIndex.textContent = 'VULN-' + String(index + 1).padStart(3, '0');
                    tr.appendChild(tdIndex);

                    // 나머지 데이터 추가
                    row.forEach((cell, cellIndex) => {
                        const td = document.createElement('td');
                        td.textContent = cell;

                        // 시스템에 따른 색상 적용 (첫 번째 열)
                        if (cellIndex === 0) {
                            const system = cell.trim().toLowerCase();
                            if (system.includes('linux')) {
                                td.style.color = lampColors['Server(Linux)']; // Server(Linux) 색상
                            } else if (system.includes('apache')) {
                                td.style.color = lampColors['Apache']; // Apache 색상
                            } else if (system.includes('mysql')) {
                                td.style.color = lampColors['MY-SQL']; // MySQL 색상
                            } else if (system.includes('php')) {
                                td.style.color = lampColors['PHP']; // PHP 색상
                            }
                        }

                        // 취약 여부에 따라 색상 변경
                        if (cellIndex === 2) {
                            const status = cell.trim().toUpperCase(); // 취약 여부를 대문자로 변환
                            if (status === 'V') {
                                td.textContent = '취약'; // 취약을 표시
                                td.style.color = 'red'; // 취약한 경우 빨간색
                                tr.classList.add('vulnerable-row'); // 취약한 행 강조
                            } else if (status === 'S') {
                                td.textContent = '안전'; // 안전을 표시
                                td.style.color = 'green'; // 안전한 경우 초록색
                            }
                        }

                        tr.appendChild(td);
                    });

                    resultTableBody.appendChild(tr);
                });
            }

            // 필터 변경 시 적용되도록 이벤트 추가
            document.getElementById('systemFilter').addEventListener('change', applyFilter);
            document.getElementById('vulnerabilityFilter').addEventListener('change', applyFilter);

            // 그래프 데이터 업데이트 함수
            function updateGraphData(data) {
                // LAMP 항목 초기화
                lampData = { 'Server(Linux)': 0, Apache: 0, 'MY-SQL': 0, PHP: 0 };
                vulnerabilityData = { safe: 0, vulnerable: 0 };

                data.forEach(row => {
                    const system = row[0].trim().toLowerCase();
                    const status = row[2].trim().toLowerCase();

                    if (system.includes('linux')) {
                        lampData['Server(Linux)']++;
                    } else if (system.includes('mysql')) {
                        lampData['MY-SQL']++;
                    } else if (system.includes('apache')) {
                        lampData['Apache']++;
                    } else if (system.includes('php')) {
                        lampData['PHP']++;
                    }

                    if (status === 'v') {
                        vulnerabilityData.vulnerable++;
                    } else if (status === 's') {
                        vulnerabilityData.safe++;
                    }
                });
            }

            // 그래프 그리기 함수
            function drawCharts() {
                drawLampChart(lampData, chartType);
                drawVulnerabilityChart(vulnerabilityData, chartType);
            }

            function drawLampChart(lampData, type = 'pie') {
                const ctx = document.getElementById('lampChart').getContext('2d');
                if (lampChart) lampChart.destroy(); // 기존 차트 삭제
                lampChart = new Chart(ctx, {
                    type: type, // 전달받은 타입으로 그래프 그리기
                    data: {
                        labels: ['Server(Linux)', 'Apache', 'MY-SQL', 'PHP'],
                        datasets: [{
                            label: 'LAMP 구성요소 취약점 분포',
                            data: Object.values(lampData), // LAMP 데이터가 여기에 들어감
                            backgroundColor: Object.values(lampColors), // 각 항목에 대한 색상
                        }]
                    }
                });
            }

            function drawVulnerabilityChart(vulnerabilityData, type = 'pie') {
                const ctx = document.getElementById('vulnerabilityChart').getContext('2d');
                if (vulnerabilityChart) vulnerabilityChart.destroy(); // 기존 차트 삭제
                vulnerabilityChart = new Chart(ctx, {
                    type: type,
                    data: {
                        labels: ['안전', '취약'],
                        datasets: [{
                            label: '안전 vs 취약 비율',
                            data: [vulnerabilityData.safe, vulnerabilityData.vulnerable],
                            backgroundColor: [vulnerabilityColors.safe, vulnerabilityColors.vulnerable]
                        }]
                    }
                });
            }

            // 그래프 타입 변경 버튼 클릭 이벤트
            document.getElementById('changeGraphBtn').addEventListener('click', function() {
                chartType = (chartType === 'pie') ? 'bar' : 'pie';
                drawCharts(); // 변경된 차트를 다시 그리기
                this.textContent = (chartType === 'pie') ? '막대 그래프로 변경' : '원형 그래프로 변경';
            });

            // '보안 진단 보고서 생성' 버튼 클릭 이벤트
            document.getElementById('generateReportBtn').addEventListener('click', function() {
                // 'word.php'로 요청을 보내 보고서 생성 및 다운로드
                window.location.href = 'word.php';
            });
        };
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body> 
</html>
