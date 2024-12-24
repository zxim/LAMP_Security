<header class="header" style="background-color: #f8f9fa; padding: 10px 20px; border-bottom: 1px solid #ddd;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
        <!-- 로고 -->
        <a href="/project/login/index.php" class="logo">
            <img src="/project/login/images/logo.png" alt="Logo" style="height: 40px;">
        </a>

        <!-- 메뉴 -->
        <nav class="menu">
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; gap: 60px; align-items: center;">
                <li>
                    <a href="/project/login/index.php" style="text-decoration: none; color: #333; font-size: 20px; padding: 10px 15px; display: block;">Home</a>
                </li>
                <li class="dropdown" style="position: relative;">
                    <a href="#" style="text-decoration: none; color: #333; font-size: 20px; padding: 10px 15px; display: block;">Community</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/project/memberboard/list.php">게시판</a>
                        </li>
                        <li>
                            <a href="#">공지사항 (임시)</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" style="position: relative;">
                    <a href="#" style="text-decoration: none; color: #333; font-size: 20px; padding: 10px 15px; display: block;">Shop</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#">상품 목록 (임시)</a>
                        </li>
                        <li>
                            <a href="#">장바구니 (임시)</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" style="position: relative;">
                    <a href="#" style="text-decoration: none; color: #333; font-size: 20px; padding: 10px 15px; display: block;">Setting</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#">프로필 수정 (임시)</a>
                        </li>
                        <li>
                            <a href="#">보안 설정 (임시)</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" style="position: relative;">
                    <a href="#" style="text-decoration: none; color: #333; font-size: 20px; padding: 10px 15px; display: block;">Account</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/project/login/form.php">회원가입</a>
                        </li>
                        <li>
                            <a href="/project/login/login_form.php">로그인</a>
                        </li>
                        <li>
                            <a href="/project/login/find_id.php">아이디 찾기</a>
                        </li>
                        <li>
                            <a href="/project/login/find_pw.php">비밀번호 찾기</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</header>

<style>
    /* 드롭다운 메뉴 */
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        display: none;
        list-style: none;
        margin: 0;
        padding: 0;
        min-width: 220px; /* 드롭다운 너비 확장 */
    }

    /* 드롭다운 메뉴 항목 */
    .dropdown-menu li a {
        text-decoration: none;
        color: #333;
        font-size: 14px;
        padding: 10px 20px;
        display: block;
        white-space: nowrap; /* 줄바꿈 방지 */
    }

    /* 호버 효과 */
    .dropdown-menu li a:hover {
        background-color: rgba(53, 53, 53, 0.99);
        color: #fff;
    }

    /* 드롭다운 메뉴 표시 */
    .dropdown:hover .dropdown-menu {
        display: block;
    }

    /* 메뉴 간 간격 조정 */
    .menu ul {
        gap: 30px; /* 메뉴 간격 확장 */
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const dropdowns = document.querySelectorAll(".dropdown");

        dropdowns.forEach(dropdown => {
            const menu = dropdown.querySelector(".dropdown-menu");

            dropdown.addEventListener("mouseenter", () => {
                menu.style.display = "block";
            });

            dropdown.addEventListener("mouseleave", () => {
                menu.style.display = "none";
            });
        });
    });
</script>
