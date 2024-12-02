<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .close { margin:20px 0 0 120px; cursor:pointer; }
        .warning { color: red; margin-top: 5px; }
        .highlight { color: red; font-weight: bold; } /* 강조 표시를 위한 스타일 */
    </style>
</head>
<body>
    <h3>아이디 중복 체크</h3>
    <div>
        <?php
        // 클라이언트에서 전달된 아이디 값 가져오기
        $config = require '../config.php';  // 루트에 있는 config.php 파일 불러옴

        // config.php에서 가져온 정보를 변수에 저장
        $db_host = $config['DB_HOST'];
        $db_user = $config['DB_USER'];
        $db_password = $config['DB_PASSWORD'];
        $db_name = $config['DB_NAME'];
        $id = isset($_GET["id"]) ? trim($_GET["id"]) : '';

        if (empty($id)) {
            echo "<p class='warning'>아이디를 입력해 주세요!</p>";
        } else {
            // DB 연결
            $con = mysqli_connect($db_host, $db_user, $db_password, $db_name);

            if (mysqli_connect_errno()) {
                echo "<p class='warning'>DB 연결에 실패했습니다. 나중에 다시 시도해 주세요.</p>";
                exit();
            }

            // Prepared Statement를 사용하여 SQL 인젝션 방지
            $stmt = $con->prepare("SELECT * FROM members WHERE id = ?");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            // 아이디 중복 확인 결과 처리
            if ($result->num_rows > 0) {
                echo "<p class='warning'>" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . " 아이디는 중복됩니다.</p>";
                echo "<p>다른 아이디를 사용해 주세요!</p>";
                echo "<script>window.opener.isIdChecked = false; window.success = false;</script>";
            } else {
                echo "<p>" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . " 아이디는 사용 가능합니다.</p>";
                echo "<script>window.opener.isIdChecked = true; window.success = true;</script>";
            }

            // 자원 해제 및 DB 연결 종료
            $stmt->close();
            mysqli_close($con);
        }
        ?>
        <div class="close">
            <button type="button" onclick="window.close()">창 닫기</button>
        </div>
    </div>
</body>
</html>

