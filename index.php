<?php
    session_start();
    require_once("include/server/index.php");
    require_once("include/advertisment/index.php");
    require_once("function/delete-status/index.php");
    require_once("function/product-category/index.php");
    require_once("function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_GET['cart']) && $_GET['cart'] != '') {
        addCart($conn, $_GET['cart']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Welcome</title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main .search-form {
            box-sizing: border-box;
        }
        main .search-form input, main .status-updates h3 {
            margin: auto;
            padding: 8px;
        }
        main .search-form input, main .status-updates img, main .status-updates video {
            width: 100%;
        }
        main .search-form input[type="search"] {
            padding: 14px;
            border: none;
            outline: none;
            background-color: #ddd;
            color: #000000;
        }
        main .search-form input[type="search"]::placeholder {
            color: #808080;
        }
        main .status-updates img, main .status-updates video {
            display: block;
            text-align: left;
        }
        main .status-updates .status-des {
            padding: 8px;
            text-align: left;
            background-color: rgb(30, 30, 30);
            color: #fff;
        }
        main .status-updates {
            margin: auto;
            margin-bottom: 100px;
            text-align: center;
        }
        main .status-updates .change-status-btn {
            display: inline-block;
            margin: 4px;
            text-decoration: none;
            color: #fff;
        }
        main .status-updates h3 {
            width: max-content;
            margin: 20px 0 4px 0;
            border: 1px solid #ff0000;
            border-radius: 0 100px 100px 0;
            text-transform: uppercase;
            background-color: #ffd700;
            color: #ff0000;
            text-shadow: -1px 0 #fff, 0 1px #fff, 1px 0 #fff, 0 -1px #fff;
        }
        main .status-updates marquee {
            display: block;
            padding: 8px;
            text-align: left;
            background-color:rgb(35, 35, 35);
            color: #fff;
        }
        main .show-category {
            box-sizing: border-box;
            margin: 20px;
            padding: 14px;
            border: 1px solid #fff;
            border-radius: 100px;
            text-transform: uppercase;
            background-color: rgb(25, 25, 25);
            color: #ffd700;
            text-shadow: -1px 0 #ff0000, 0 1px #ff0000, 1px 0 #ff0000, 0 -1px #ff0000;
        }
        main .show-category a {
            float: right;
            text-decoration: none;
            text-transform: capitalize;
            text-shadow: none;
            font-size: medium;
            color: #fff;
        }
        main .category-box-layout {
            box-sizing: border-box;
            width: 100%;
            overflow: auto;
        }
        main .category-box-layout .category-div {
            width: max-content;
            padding: 20px;
            overflow: auto;
        }
        main .category-box-layout .category-div div {
            box-sizing: content-box;
        }
        main .category-box-layout .category-div .show-item {
            display: inline-block;
            width: 250px;
            min-width: 250px;
            max-width: 250px;
            overflow: hidden;
            margin: 5px;
            padding: 8px;
            border-radius: 4px;
            box-shadow: 2px 2px 5px #808080;
            background-color: #fff;
        }
        main .category-box-layout .category-div .show-item:hover {
            transform: scale(102%);
        }
        main .category-box-layout .category-div .show-item a {
            text-decoration: none;
            display: inline-block;
            color: #000000;
        }
        main .category-box-layout .category-div .show-item p {
            display: inline-block;
            margin: 4px auto;
            padding: 8px;
            border-radius: 4px;
            font-weight: bold;
            color: #fff;
        }
        main .category-box-layout .category-div .show-item #show-discount, main .category-box-layout .category-div .show-item #out-stock {
            background-color: #ff0000;
        }
        main .category-box-layout .category-div .show-item #show-stock {
            background: linear-gradient(to right, #0000ff, royalblue);
        }
        main .category-box-layout .category-div .show-item .item-image {
            width: 100%;
            max-width: 100%;
            height: 250px;
            max-height: 250px;
            overflow: hidden;
            background-color: #ddd;
        }
        main .category-box-layout .category-div .show-item .item-image img {
            width: 100%;
            min-height: 250px;
            object-fit: cover;
        }
        main .category-box-layout .category-div .show-item .item-content {
            width: 250px;
            max-width: 250px;
            height: 150px;
            max-height: 150px;
            overflow: hidden;
        }
        main .category-box-layout .category-div .show-item .item-price p {
            padding: 0 8px;
            font-size: 15px;
            color: #808080;
        }
        main .category-box-layout .category-div .show-item .item-price h2 {
            padding: 0 8px 8px 8px;
            color: #ff0000;
        }
        main .category-box-layout .category-div .show-item .cart-item, main .category-box-layout .category-div .show-item .cart-item a {
            min-width: 100%;
            max-width: 100%;
        }
        main .category-box-layout .category-div .show-item .cart-item {
            overflow: hidden;
            text-align: center;
            border-radius: 4px;
        }
        main .category-box-layout .category-div .show-item .cart-item a {
            padding: 4px;
            font-size: 18px;
            text-transform: uppercase;
            vertical-align: middle;
            font-weight: bold;
            background-color: #909090;
            color: #000000;
        }
        main .category-box-layout .category-div .show-item .cart-item a img {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #fff;
            vertical-align: middle;
            background-color: #000000;
        }
        main .category-box-layout .category-div .show-item .cart-item a:hover {
            background-color: #000000;
            color: #fff;
        }
        main .category-box-layout .category-div .show-item .cart-item a:hover img {
            border: 1px solid #000000;
        }
    </style>
</head>
<body>
    <header>
        <div class="main-nav-btn">
            <a href="/" id="home-nav" title="Home"><img src="/image/logo.jpg" alt=""></a>
            <a href="/product-category/laptop/"><img src="/image/laptop.png" alt=""> Laptop</a>
            <a href="/product-category/desktop/"><img src="/image/desktop.png" alt=""> Desktop</a>
            <a href="/product-category/monitor/"><img src="/image/monitor.jpg" alt=""> Monitor</a>
            <a href="/product-category/computer-accessories/"><img src="/image/accessories.png" alt=""> Accessories</a>
            <a href="/member/message-center/"><img src="/image/log-in.png" alt=""> Log in</a>
            <a href="/cart/" id="cart-nav"><img src="/image/cart.png" alt=""><?php echo countCart(); ?></a>
        </div>
    </header>
    <main>
        <form action="/search/" method="get" autocomplete="off" class="search-form">
            <input type="search" name="result" placeholder="Enter anything computer products related.. 🔍" maxlength="100" required autofocus>
        </form>
        <div class="status-updates">
            <?php
            /* advertisment */
                deleteStatus($conn, "status"); # call function
                if (isset($_GET['status'])) {
                    $page = $_GET['status'];
                } else {
                    $page = 1;
                }
                $records = 1;
                $startFrom = ($page * $records) - $records;
                $sql = "SELECT
                    `pathName`,
                    `type`,
                    `description`
                FROM
                    `Status`
                WHERE
                    `trash` = 0
                LIMIT $startFrom, $records";
            /* prepare the query */
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true) {
                    while ($row = mysqli_fetch_assoc($stmt)) {
                        if (substr($row['type'], 0, 5) == "image") {
                            echo "<img src='/status/$row[pathName]' alt='status image' id='selected'>";
                        } else if (substr($row['type'], 0, 5) == "video") {
                            echo "<video loop='' autoPlay='' muted='' id='selected'><source src='/status/$row[pathName]'>Your browser does not support the video tag.</video>";
                        }
                        if ($row['description'] != null) {
                            echo "<p class='status-des'>$row[description]</p>";
                        }
                    }
                }
                $sql = "SELECT `statusId` FROM `Status` WHERE `trash` = 0";
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true) {
                    $numPages = ceil(mysqli_num_rows($stmt) / $records);
                    if (1 < $page) {
                        echo "<a href='/?status=" .$page -1  ."&#selected' title='Go to previous status' class='change-status-btn'>◄</a>";
                    }
                    for ($i=1; $i < $numPages; $i++) {
                        echo "<a href='/?status=$i&#selected' class='change-status-btn'>•</a>";
                    }
                    if ($page < $i) {
                        echo "<a href='/?status=" .$page + 1 ."&#selected' title='Go to next status' class='change-status-btn'>►</a>";
                    }
                /* empty records - display default status */
                    if (mysqli_num_rows($stmt) == 0) {
                        echo "<video loop='' autoPlay='' muted=''><source src='/status/default.mp4'>Your browser does not support the video tag.</video>";
                    }
                }
            /* new updates */
                $sql = "SELECT `description` FROM `Update` WHERE `trash` = 0";
                $stmt = mysqli_query($conn, $sql);
                $records = 0;
                if ($stmt == true && mysqli_num_rows($stmt) > 0) {
                    echo "<h3>Daily News Update ▼</h3>";
                    echo "<marquee>";
                    while ($row = mysqli_fetch_assoc($stmt)) {
                        $records = $records + 1;
                        echo $row['description'];
                        echo (mysqli_num_rows($stmt) != $records) ? " | " : '';
                    }
                    echo "</marquee>";
                }
            ?>
        </div>
    <!-- Laptops -->
        <h3 id="laptop" class="show-category">Laptop ▼<a href="product-category/laptop/">View All ►►</a></h3>
        <?php displayBasicProduct($conn, "Laptop"); ?>
    <!-- desktops -->
        <h3 id="desktop" class="show-category">Desktop ▼<a href="product-category/desktop/">View All ►►</a></h3>
        <?php displayBasicProduct($conn, "Desktop"); ?>
    <!-- monitors -->
        <h3 id="monitor" class="show-category">Monitor ▼<a href="product-category/monitor/">View All ►►</a></h3>
        <?php displayBasicProduct($conn, "Monitor"); ?>
    <!-- accessories -->
        <h3 id="computer-accessories" class="show-category">Accessories ▼<a href="product-category/computer-accessories/">View All ►►</a></h3>
        <?php displayBasicProduct($conn, "Accessories"); ?>
    </main>
    <footer>
        <div class="footer-about"><hr>
            <div class="about-title">
                <div class="footer-img"><img src="/image/logo.jpg" alt="dilla's pc"></div>
                <h1>DILLA'S PC</h1>
                <h6>Your trusted partner for all PC solution</h6>
            </div>
            <div class="footer-btn">
                <a href="/">Home</a> |
                <a href="/about-us/">About Us</a> |
                <a href="/contact-us/">Contact Us</a> |
                <a href="/admin/dashboard/">Admin</a>
            </div>
            <h4>&copy; 2025 Dilla's PC, Inc. All rights reserved. Design & Maintain By <a href="https://api.whatsapp.com/send/?phone=%2B94762059126&text&type=phone_number&app_absent=0" target="_blank">PASINDU AKALANKA.</a></h4>
        </div>
    </footer>
<!-- chatbox -->
    <span class="chatbox">
        <a href="https://api.whatsapp.com/send/?phone=%2B94750855492&text&type=phone_number&app_absent=0" target="_blank">
            <img src="/contact-us/whatsapp.png" alt="" title="WhatsApp">
        </a>
    </span>
</body>
</html>