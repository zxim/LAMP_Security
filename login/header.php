<header class="header" style="background-color: black; padding: 10px 20px; border-bottom: 1px solid #555;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
        <!-- 로고 -->
        <a href="/project/login/index.php" class="logo">
            <img src="/project/login/images/logo.png" alt="Logo" style="height: 40px;">
        </a>

        <!-- 메뉴 -->
        <nav class="menu">
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; gap: 60px; align-items: center;">
                <li>
                    <a href="/project/login/index.php" style="text-decoration: none; color: #fff; font-size: 20px; padding: 10px 15px; display: block;">Home</a>
                </li>
                <li class="dropdown" style="position: relative;">
                    <a href="#" style="text-decoration: none; color: #fff; font-size: 20px; padding: 10px 15px; display: block;">Shop</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/project/shop/categories.php" style="color: #fff;">상품 목록</a>
                        </li>
                        <li>
                            <a href="/project/shop/cart.php" style="color: #fff;">장바구니</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" style="position: relative;">
                    <a href="#" style="text-decoration: none; color: #fff; font-size: 20px; padding: 10px 15px; display: block;">Community</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/project/memberboard/list.php" style="color: #fff;">게시판</a>
                        </li>
                        <li>
                            <a href="/project/memberboard/notices.php" style="color: #fff;">공지사항</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" style="position: relative;">
                    <a href="#" style="text-decoration: none; color: #fff; font-size: 20px; padding: 10px 15px; display: block;">Account</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/project/shop/orders.php" style="color: #fff;">구매 목록</a>
                        </li>
                        <li>
                            <a href="/project/login/find.php" style="color: #fff;">계정 정보 찾기</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" style="position: relative;">
                    <a href="#" style="text-decoration: none; color: #fff; font-size: 20px; padding: 10px 15px; display: block;">Setting</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/project/login/modify_form.php" style="color: #fff;">계정 설정</a>
                        </li>
                        <li>
                            <a href="#" style="color: #fff;">보안 설정 (임시)</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown" style="position: relative;">
                    <a href="#" style="text-decoration: none; color: #fff; font-size: 20px; padding: 10px 15px; display: block;">Login</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/project/login/login_form.php" style="color: #fff;">로그인</a>
                        </li>
                        <li>
                            <a href="/project/login/form.php" style="color: #fff;">회원가입</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/project/login/logout.php" style="text-decoration: none; color: #fff; font-size: 20px; padding: 10px 15px; display: block;">Logout</a>
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
        background-color: rgb(27, 27, 27); /* 어두운 배경색 */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
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
        color: #fff; /* 밝은 글씨색 */
        font-size: 14px;
        padding: 10px 20px;
        display: block;
        white-space: nowrap; /* 줄바꿈 방지 */
        transition: all 0.3s ease; /* 부드러운 효과 */
    }

    /* 호버 효과 */
    .dropdown-menu li a:hover {
        background-color: #fff; /* 밝은 배경색 */
        color: #333 !important; /* 어두운 글씨색 */
        border: 1px solid #333; /* 검은 테두리 추가 */
        border-radius: 6px; /* 테두리를 둥글게 */
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
