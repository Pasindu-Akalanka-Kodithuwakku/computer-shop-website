<?php
    session_start();
    require_once("../../../include/server/index.php");
    require_once("../../../function/delete-attachment/index.php");
    require_once("../../../function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
/* check session */
    if (!isset($_SESSION['memberId'])) {
        header("location: /member/sign-in/");
        exit();
    }
    $info = array();
    if (isset($_POST['psw'])) {
        $psw = $_POST['psw'];
    /* check password */
        $encPsw = mysqli_real_escape_string($conn, sha1($psw));
        $sql = "SELECT * FROM `Member` WHERE `password` = '$encPsw' AND `memberId` = '$_SESSION[memberId]' AND `trash` = 0 LIMIT 1";
    /* prepare the query */
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == true && mysqli_num_rows($stmt) == 0) {
            $info[] = "Incorrect password."; // incorrect password
        }
    /* no errors */
        if (empty($info)) {
            $sql = "SELECT `messageId` FROM `Message` WHERE `sender` = '$_SESSION[memberId]' OR `receiver` = '$_SESSION[memberId]'";
            $stmt = mysqli_query($conn, $sql);
            if ($stmt == true) {
                while ($row = mysqli_fetch_assoc($stmt)) {
                    $sql2 = "DELETE FROM `Message` WHERE `messageId` = '$row[messageId]'"; # delete record one by one
                    $stmt2 = mysqli_query($conn, $sql2);
                /* query failed */
                    if ($stmt2 == false) {
                        $info[] = "The one record could not be deleted.";
                    }
                }
            }
        /* attachment's folder delete function */
            deleteFolder("../../../attachment/$_SESSION[memberId]"); # remove the member's attachment folder
        /* finaly member's account delete */
            $sql = "DELETE FROM `Member` WHERE `memberId` = '$_SESSION[memberId]'";
            $stmt = mysqli_query($conn, $sql);
            if ($stmt == true) {
            /* check session cookies */
                if (isset($_COOKIE[session_name()])) {
                    setcookie($_COOKIE[session_name()], '', -time()+60*60*24, '/');
                }
                session_destroy();
                header("location: /"); # redirect to main page
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Destroy Account</title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main .search-form, main .box-layout h3, main .box-layout form p {
            box-sizing: border-box;
        }
        main .search-form input[type="search"] {
            width: 100%;
            margin: auto;
            padding: 14px;
            border: none;
            outline: none;
            background-color: #ddd;
            color: #000000;
        }
        main .search-form input[type="search"]::placeholder {
            color: #808080;
        }
        main .box-layout {
            margin: 20px auto;
            margin-top: 100px;
            overflow: auto;
        }
        main .box-layout .title-header {
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
        main .box-layout form {
            width: 100%;
            margin: auto;
            padding: 20px;
            overflow: auto;
        }
        main .box-layout form, main .box-layout form input, main .box-layout form button {
            box-sizing: border-box;
        }
        main .box-layout form input {
            width: 100%;
            height: 40px;
            margin: 4px auto;
            padding: 8px;
            outline: none;
            border-radius: 4px;
            border-style: solid;
            border-color: #808080;
        }
        main .box-layout form .custom-note, main .box-layout form button {
            margin: 4px auto;
            padding: 8px;
        }
        main .box-layout form button {
            margin-top: 20px;
            margin-left: 8px;
            color: #000000;
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
    <!-- sign up -->
        <div class="box-layout">
            <h3 class="title-header">Destroy account</h3>
            <form action="/member/message-center/destroy-account/" method="post" autocomplete="off">
                <?php
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note' style='color: #808080;'>$value</p>";
                    }
                ?>
                <input type="password" name="psw" placeholder="Enter password" required>
                <a href="/member/message-center/"><button type="button">Cancel</button></a>
                <button type="submit" onclick="return confirm('Delete Account 💀\n\nThis will erase certain data from account, including: ⚡\n\n\tAll direct messages you sent\n\tYour personal account\n\nAre you sure you want to delete account permanently? ⚠️')">Done</button>
            </form>
        </div>
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