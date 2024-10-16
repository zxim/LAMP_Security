<?php
    $num   = $_GET["num"];
    $page   = $_GET["page"];

    $con = mysqli_connect("localhost", "min", "guseodhxhdpqj@", "project");  
    $sql = "delete from memberboard where num=$num"; // 레코드 삭제 명령
    mysqli_query($con, $sql);     // SQL 명령 실행

    mysqli_close($con);           // DB 접속 해제

    // 목록보기 이동
    echo "<script>
	         location.href = 'list.php?page=$page';      
	     </script>";
?>