<?php
    session_start();
    require_once("../../../include/server/index.php");
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
    if (isset($_POST['psw1'])) {
        $psw1 = $_POST['psw1'];
        $psw2 = $_POST['psw2'];
        $psw3 = $_POST['psw3'];
    /* check old password */
        $encPsw1 = mysqli_real_escape_string($conn, sha1($psw1));
        $sql = "SELECT * FROM `Member` WHERE `password` = '$encPsw1' AND `memberId` = '$_SESSION[memberId]' AND `trash` = 0 LIMIT 1";
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == true && mysqli_num_rows($stmt) == 1) {
        /* check new passwords */
            if ($psw2 != $psw3) {
                $info[] = "Passwords do not match. Please try again."; // defference passwords error
            } else {
                $inChar = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
                $foundChar = 0;
                for ($i=0; $i < strlen($psw2); $i++) {
                    for ($x=0; $x < count($inChar); $x++) {
                        if ($psw2[$i] == $inChar[$x]) {
                            $foundChar = 1;
                            break;
                        }
                    }
                    if ($foundChar == 1) {
                        break;
                    }
                }
                if ($foundChar == 0) {
                    $info[] = "Please include at least one letter"; // no included letters error
                } else {
                    if ($psw1 == $psw2) {
                        $info[] = "New password can't be the same as the old password."; // old and new passwords are same error
                    }
                }
            }
        } else {
            $info[] = "Incorrect password."; // incorrect old password
        }
    /* no errors */
        if (empty($info)) {
            $encPsw2 = mysqli_real_escape_string($conn, sha1($psw2)); // encryption new password
            $sql = "UPDATE `Member` SET `password` = '$encPsw2' WHERE `memberId`= '$_SESSION[memberId]'"; // update new password
            $stmt = mysqli_query($conn, $sql);
        /* prepare the query */
            if (mysqli_affected_rows($conn) == 1) {
                header("location: /member/message-center/?info=Successfully changed.");
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Change Password</title>
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
            <h3 class="title-header">Change Password</h3>
            <form action="/member/message-center/change-password/" method="post" autocomplete="off">
                <?php
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note' style='color: #808080;'>$value</p>";
                    }
                ?>
                <input type="password" name="psw1" placeholder="Old password" required>
                <input type="password" name="psw2" placeholder="New password" minlength="6" maxlength="16" required>
                <input type="password" name="psw3" placeholder="Confirm new password" minlength="6" maxlength="16" required>
                <p class="custom-note">Password must consist of 6 to 16 characters, and include at least one letter</p>
                <a href="/member/message-center/"><button type="button">Cancel</button></a>
                <button type="submit">Done</button>
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