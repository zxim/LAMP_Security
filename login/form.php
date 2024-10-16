<!DOCTYPE html>
<html>
<head> 
<meta charset="utf-8">
<title>오토에버 불여조</title>
<link rel="stylesheet" href="./css/style.css">
<script>
   function check_input() {
      if (!document.member.id.value) {
          alert("아이디를 입력하세요!");    
          document.member.id.focus();
          return;
      }
      if (!document.member.pass.value) {
          alert("비밀번호를 입력하세요!");    
          document.member.pass.focus();
          return;
      }
      if (!document.member.pass_confirm.value) {
          alert("비밀번호확인을 입력하세요!");    
          document.member.pass_confirm.focus();
          return;
      }
      if (!document.member.name.value) {
          alert("이름을 입력하세요!");    
          document.member.name.focus();
          return;
      }
      if (!document.member.email.value) {
          alert("이메일 주소를 입력하세요!");    
          document.member.email.focus();
          return;
      }
      if (document.member.pass.value != document.member.pass_confirm.value) {
          alert("비밀번호가 일치하지 않습니다.\n다시 입력해 주세요!");
          document.member.pass.focus();
          document.member.pass.select();
          return;
      }
      document.member.submit();
   }
   function reset_form() {
      document.member.id.value = "";  
      document.member.pass.value = "";
      document.member.pass_confirm.value = "";
      document.member.name.value = "";
      document.member.email.value = "";
      document.member.id.focus();
      return;
   }
   function check_id() {
     window.open("check_id.php?id=" + document.member.id.value,
         "IDcheck",
          "left=700,top=300,width=380,height=160,scrollbars=no,resizable=yes");
   }
   function go_back() {
     window.history.back(); // 이전 페이지로 돌아가기
   }
</script>
</head>
<body> 
    <form name="member" action="insert.php" method="post">
        <h2>회원 가입</h2>
        <ul class="join_form">
            <li>
                <span class="col1">아이디</span>
                <span class="col2"><input type="text" name="id"></span>
                <span class="col3"><button type="button" onclick="check_id()">중복체크</button></span>                    
            </li>
            <li>
                <span class="col1">비밀번호</span>
                <span class="col2"><input type="password" name="pass"></span>               
            </li>
            <li>
                <span class="col1">비밀번호 확인</span>
                <span class="col2"><input type="password" name="pass_confirm"></span>               
            </li>            
            <li>
                <span class="col1">이름</span>
                <span class="col2"><input type="text" name="name"></span>               
            </li>
            <li>
                <span class="col1">이메일</span>
                <span class="col2"><input type="text" name="email"></span>               
            </li>                        
        </ul>                       

        <ul class="buttons">
            <li><button type="button" onclick="check_input()">저장하기</button></li>
            <li><button type="button" onclick="reset_form()">지우기</button></li>
            <li><button type="button" onclick="go_back()">나가기</button></li>
        </ul>
    </form>
</body>
</html>
