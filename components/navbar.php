<?php

$active_page = $active_page ?? "";

?>

<header class="home-navbar">

    <div class="logo">
        <span>Flo</span>Test
    </div>


    <div class="navbar-wrapper">

        <nav class="navbar-menu">

            <a
                href="home.php"
                class="nav-link <?= $active_page === "home" ? "active" : "" ?>"
            >
                Home
            </a>


            <a
                href="test.php"
                class="nav-link <?= $active_page === "test" ? "active" : "" ?>"
            >
                Test
            </a>


            <a
                href="jawab-test.php"
                class="nav-link <?= $active_page === "jawab-test" ? "active" : "" ?>"
            >
                Jawab Test
            </a>


            <a
                href="index.php?logout=1"
                class="nav-link"
            >
                Logout
            </a>

        </nav>


        <button
            type="button"
            class="menu-toggle"
            onclick="toggleMenu()"
            aria-label="Buka menu"
        >
            ☰
        </button>

    </div>

</header>