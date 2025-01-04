<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .close { margin:20px 0 0 120px; cursor:pointer; }
        .warning { color: red; margin-top: 5px; }
        .highlight { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h3>아이디 중복 체크</h3>
    <div>
        <?php
        // 클라이언트에서 전달된 아이디 값 가져오기
        $config = require '../config.php';

        $db_host = $config['DB_HOST'];
        $db_user = $config['DB_USER'];
        $db_password = $config['DB_PASSWORD'];
        $db_name = $config['DB_NAME'];
        $id = isset($_GET["id"]) ? trim($_GET["id"]) : '';

        // 아이디 유효성 검사
        $id_pattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()-_=+]{4,20}$/'; // 영어와 숫자가 포함되어야 하며, 특수기호 허용
        if (empty($id)) {
            echo "<p class='warning'>아이디를 입력해 주세요!</p>";
        } elseif (!preg_match($id_pattern, $id)) {
            echo "<p class='warning'>아이디는 4자리 이상 20자리 미만이어야 하며, 영어와 숫자를 반드시 포함해야 합니다.</p>";
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
            $escaped_id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
            if ($result->num_rows > 0) {
                echo "<p class='warning'>{$escaped_id} 아이디는 중복됩니다.</p>";
                echo "<p>다른 아이디를 사용해 주세요!</p>";
                echo "<script>
                    window.opener.isIdChecked = false;
                    window.success = false;
                </script>";
            } else {
                echo "<p>{$escaped_id} 아이디는 사용 가능합니다.</p>";
                echo "<script>
                    window.opener.isIdChecked = true;
                    window.success = true;
                </script>";
            }

            $stmt->close();
            mysqli_close($con);
        }
        ?>
        <div class="close">
            <button type="button" onclick="window.close()" class="btn">창 닫기</button>
        </div>
    </div>
</body>
</html>
