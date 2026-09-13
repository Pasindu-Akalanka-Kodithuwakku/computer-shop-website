<?php
    session_start();
    require_once("../../../include/server/index.php"); 
    require_once("../../../function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
/* check session */
    if (!isset($_SESSION['memberId'])) {
        header("location: /member/sign-in/"); # redirect to back page
        exit();
    }
    $info = array();
/* load custom fields */
    $sql = "SELECT
        `customOne`,
        `customTwo`
    FROM
        `Member`
    WHERE
        `memberId` = '$_SESSION[memberId]' AND
        `trash` = 0
    LIMIT 1";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt == true && mysqli_num_rows($stmt) == 0) {
    /* invalid member id */
        header("location: /member/sign-in/"); # redirect to back page
        exit();
    }
/* create variables */
    $row = mysqli_fetch_assoc($stmt); # load data
    $psw = '';
    $select1 = null;
    $custom1 = '';
    $answer1 = '';
    $select2 = null;
    $custom2 = '';
    $answer2 = '';
/* if click submit */
    if (isset($_POST['psw'])) {
        $psw = $_POST['psw'];
        $select1 = $_POST['sel1'];
    /* security option 1 */
        $custom1 = $_POST['sel1-custom'];
        if (strlen($custom1) > 0) {
            $select1 = 1;
        }
        $answer1 = $_POST['sel1-ans'];
    /* security option 2 */
        $select2 = $_POST['sel2'];
        $custom2 = $_POST['sel2-custom'];
        if (strlen($custom2) > 0) {
            $select2 = 1;
        }
        $answer2 = $_POST['sel2-ans'];
    /* check password */
        $encPsw = mysqli_real_escape_string($conn, sha1($psw)); // encryption password
        $sql = "SELECT * FROM `Member` WHERE `password` = '$encPsw' AND `memberId` = '$_SESSION[memberId]' AND `trash` = 0 LIMIT 1";
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == true && mysqli_num_rows($stmt) == 0) {
            $info[] = "Incorrect password."; # incorrect passwords error
        }
    /* no errors */
        if (empty($info)) {
            $secCustom1 = mysqli_real_escape_string($conn, $custom1); // secure custom field 1
            $encAnswer1 = mysqli_real_escape_string($conn, sha1($answer1)); // encryption answer 1;
            $secCustom2 = mysqli_real_escape_string($conn, $custom2); // secure custom field 2
            $encAnswer2 = mysqli_real_escape_string($conn, sha1($answer2)); // encryption answer 2;
            if (strlen($custom1) > 0) {
            /* set custom 1 */
                if (strlen($custom2) > 0) {
                /* set custom 2 */
                    $sql = "UPDATE
                        `Member`
                    SET
                        `optionOne` = 0,
                        `customOne` = '$secCustom1',
                        `answerOne` = '$encAnswer1',
                        `optionTwo` = 0,
                        `customTwo` = '$secCustom2',
                        `answerTwo` = '$encAnswer2'
                    WHERE
                        `memberId` = '$_SESSION[memberId]' AND
                        `trash` = 0
                    ";
                } else {
                /* not set custom 2 */
                    if ($select2 == 0) {
                    /* not change option 2, custom 2 fields */
                        $sql = "UPDATE
                            `Member`
                        SET
                            `optionOne` = 0,
                            `customOne` = '$secCustom1',
                            `answerOne` = '$encAnswer1',
                            `optionTwo` = 0,
                            `answerTwo` = '$encAnswer2'
                        WHERE
                            `memberId` = '$_SESSION[memberId]' AND
                            `trash` = 0
                        ";
                    } else {
                    /* change only option 2 field */
                        $sql = "UPDATE
                            `Member`
                        SET
                            `optionOne` = 0,
                            `customOne` = '$secCustom1',
                            `answerOne` = '$encAnswer1',
                            `optionTwo` = $select2,
                            `answerTwo` = '$encAnswer2'
                        WHERE
                            `memberId` = '$_SESSION[memberId]' AND
                            `trash` = 0
                        ";
                    }
                }
            } else {
            /* not set custom 1 */
                if (strlen($custom2) > 0) {
                /* set custom 2 */
                    if ($select1 == 0) {
                    /* not change option 1, custom 1 fields */
                        $sql = "UPDATE
                            `Member`
                        SET
                            `optionOne` = 0,
                            `answerOne` = '$encAnswer1',
                            `optionTwo` = 0,
                            `customTwo` = '$secCustom2',
                            `answerTwo` = '$encAnswer2'
                        WHERE
                            `memberId` = '$_SESSION[memberId]' AND
                            `trash` = 0
                        ";
                    } else {
                    /* change only option 1 field */
                        $sql = "UPDATE
                            `Member`
                        SET
                            `optionOne` = $select1,
                            `answerOne` = '$encAnswer1',
                            `optionTwo` = 0,
                            `customTwo` = '$secCustom2',
                            `answerTwo` = '$encAnswer2'
                        WHERE
                            `memberId` = '$_SESSION[memberId]' AND
                            `trash` = 0
                        ";
                    }
                } else {
                /* not set custom 2 */
                    if ($select1 == 0) {
                    /* not change option 1, custom 1 fields */
                        if ($select2 == 0) {
                        /* not change option 2, custom 2 fields */
                            $sql = "UPDATE
                                `Member`
                            SET
                                `optionOne` = 0,
                                `answerOne` = '$encAnswer1',
                                `optionTwo` = 0,
                                `answerTwo` = '$encAnswer2'
                            WHERE
                                `memberId` = '$_SESSION[memberId]' AND
                                `trash` = 0
                            ";
                        } else {
                        /* change only option 2 field */
                            $sql = "UPDATE
                                `Member`
                            SET
                                `optionOne` = 0,
                                `answerOne` = '$encAnswer1',
                                `optionTwo` = $select2,
                                `answerTwo` = '$encAnswer2'
                            WHERE
                                `memberId` = '$_SESSION[memberId]' AND
                                `trash` = 0
                            ";
                        }
                    } else {
                    /* change only option 1 field */
                        if ($select2 == 0) {
                        /* not change option 2, custom 2 fields */
                            $sql = "UPDATE
                                `Member`
                            SET
                                `optionOne` = $select1,
                                `answerOne` = '$encAnswer1',
                                `optionTwo` = 0,
                                `answerTwo` = '$encAnswer2'
                            WHERE
                                `memberId` = '$_SESSION[memberId]' AND
                                `trash` = 0
                            ";
                        } else {
                        /* change only option 2 field */
                            $sql = "UPDATE
                                `Member`
                            SET
                                `optionOne` = $select1,
                                `answerOne` = '$encAnswer1',
                                `optionTwo` = $select2,
                                `answerTwo` = '$encAnswer2'
                            WHERE
                                `memberId` = '$_SESSION[memberId]' AND
                                `trash` = 0
                            ";
                        }
                    }
                }
            }
        /* prepare the query */
            $stmt = mysqli_query($conn, $sql);
            if (mysqli_affected_rows($conn) == 1) {
                header("location: /member/message-center/?info=Successfully changed.");
                exit();
            } else {
                $info[] = "Already successfully.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Change Security Question</title>
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
        main .box-layout form fieldset {
            margin-bottom: 20px;
            padding: 14px;
            border-color: #fff;
        }
        main .box-layout form fieldset p {
            margin: 4px auto;
            padding: 8px;
            font-size: large;
            font-weight: bold;
            font-style: italic;
            text-align: center;
            color: #808080;
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
        main .box-layout form input[type="password"] {
            margin-bottom: 20px;
        }
        main .box-layout form select {
            width: 100%;
            height: 40px;
            margin: 4px auto;
            padding: 8px;
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
            <h3 class="title-header">Change security question</h3>
            <form action="/member/message-center/change-security-question/" method="post" autocomplete="off">
                <?php
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note' style='color: #808080;'>$value</p>";
                    }
                ?>
                <input type="password" name="psw" value="<?php echo $psw; ?>" placeholder="Enter password" required>
            <!-- second question -->
                <fieldset>
                <!-- default selection -->
                    <select name="sel1">
                        <?php
                        // load saved data
                            if ($custom1 == null && $row['customOne'] != null) {
                                echo "<option value='0'>$row[customOne]</option>";
                            }
                        ?>
                        <option value="1" <?php echo ($select1 == '1') ? "selected" : '' ; ?>>What is your father's name?</option>
                        <option value="2" <?php echo ($select1 == '2') ? "selected" : '' ; ?>>What is your partner's name?</option>
                        <option value="3" <?php echo ($select1 == '3') ? "selected" : '' ; ?>>What middle school did you go to?</option>
                        <option value="4" <?php echo ($select1 == '4') ? "selected" : '' ; ?>>What is the name of the street where you grew up?</option>
                        <option value="5" <?php echo ($select1 == '5') ? "selected" : '' ; ?>>What is the name of your favorite singer or band?</option>
                    </select>
                    <p>Or</p>
                <!-- custom input -->
                    <input type="text" name="sel1-custom" placeholder="Custom" value="<?php echo $custom1; ?>" maxlength="250">
                </fieldset>
            <!-- answer -->
                <input type="text" name="sel1-ans" placeholder="Answer" value="<?php echo $answer1; ?>" minlength="3" maxlength="16" required>
                <p class="custom-note">Please enter 3 to 16 characters</p>
            <!-- second question -->
                <fieldset style="margin-top: 20px;">
                <!-- default selection -->
                    <select name="sel2">
                        <?php
                        // load saved data
                            if ($custom2 == null && $row['customTwo'] != null) {
                                echo "<option value='0'>$row[customTwo]</option>";
                            }
                        ?>
                        <option value="1" <?php echo ($select2 == '1') ? "selected" : '' ; ?>>What is your mother's name?</option>
                        <option value="2" <?php echo ($select2 == '2') ? "selected" : '' ; ?>>What elementary school did you go to?</option>
                        <option value="3" <?php echo ($select2 == '3') ? "selected" : '' ; ?>>Where did you go the first time you flew on a plane?</option>
                        <option value="4" <?php echo ($select2 == '4') ? "selected" : '' ; ?>>What is your favorite sports team?</option>
                        <option value="5" <?php echo ($select2 == '5') ? "selected" : '' ; ?>>What is your dream job?</option>
                    </select>
                    <p>Or</p>
                <!-- custom input -->
                    <input type="text" name="sel2-custom" placeholder="Custom" value="<?php echo $custom2; ?>" maxlength="250">
                </fieldset>
            <!-- answer -->
                <input type="text" name="sel2-ans" placeholder="Answer" value="<?php echo $answer2; ?>" minlength="3" maxlength="16" required>
                <p class="custom-note" style="margin-bottom: 20px;">Please enter 3 to 16 characters</p>
            <!-- done & cancel btn -->
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