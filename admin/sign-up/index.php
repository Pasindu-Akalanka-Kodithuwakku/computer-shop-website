<?php
    require_once("../../include/server/index.php"); 
    require_once("../../function/create-primary-key/index.php");
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/'); # redirect to back page
        exit();
    }
    $info = array();
/* set variables */
    $fName = '';
    $lName = '';
    $tel = '';
    $nic = '';
    $psw1 = '';
    $psw2 = '';
    $select1 = '1';
    $custom1 = '';
    $answer1 = '';
    $select2 = '1';
    $custom2 = '';
    $answer2 = '';
/* set submit */
    if (isset($_POST['psw1'])) {
        $fName = trim($_POST['fName']);
        $lName = trim($_POST['lName']);
        $tel = $_POST['contact'];
        $nic = $_POST['nic'];
        $psw1 = $_POST['psw1'];
        $psw2 = $_POST['psw2'];
        $select1 = $_POST['sel1'];
        $custom1 = $_POST['sel1-custom'];
        if (strlen($custom1) > 0) {
            $select1 = '1';
        }
        $answer1 = $_POST['sel1-ans'];
        $select2 = $_POST['sel2'];
        $custom2 = $_POST['sel2-custom'];
        if (strlen($custom2) > 0) {
            $select2 = '1';
        }
        $answer2 = $_POST['sel2-ans'];
    /* coding... */
        if ($psw1 != $psw2) {
            $info[] = "Passwords do not match. Please try again."; // defference passwords error
        } else {
            $inChar = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
            $checkField = array($fName, $lName, $psw1);
            $foundChar = 0;
            for ($i=0; $i < count($checkField); $i++) {
                for ($x=0; $x < strlen($checkField[$i]); $x++) {
                    for ($y=0; $y < count($inChar); $y++) {
                        if ($inChar[$y] == $checkField[$i][$x]) {
                            $foundChar = 1;
                            break;
                        }
                    }
                    if ($foundChar == 1) {
                        break;
                    }
                }
                if ($foundChar == 0) {
                    if ($i == 0) {
                        $info[] = "First name is not valid. Please try again."; // no included letters error
                    } else if ($i == 1) {
                        if ($lName != null) {
                            $info[] = "Last name is not valid. Please try again."; // no included letters error
                        }
                    } else {
                        $info[] = "Please include at least one letter for password"; // no included letters error
                    }
                } else {
                    $foundChar = 0;
                }
            }
        /* check NIC number */
            $foundChar = 0;
            for ($i=0; $i < strlen($nic); $i++) {
                if (!($nic[$i] >= 0 && $nic[$i] <= 9)) {
                    $foundChar = 1;
                    $info[] = "NIC number is not valid. Please try again."; // NIC not valid error
                    break;
                }
            }
        /* check contact & NIC number */
            if ($foundChar == 0) {
                $sql = "SELECT * FROM `Admin` WHERE (`contact` = '$tel' OR `nic` = '$nic') AND `trash` = 0 LIMIT 1";
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true && mysqli_num_rows($stmt) == 1) {
                    $info[] = "Contact number or NIC number is already exists. Please try again.";
                }
            }
        }
    /* no errors */
        if (empty($info)) {
            $id = createPrimaryKey($conn, "Admin", "adminId", "AM"); // generate primary key
            $secFName = mysqli_real_escape_string($conn, $fName); // secure first name
            $secLName = mysqli_real_escape_string($conn, $lName); // secure last name
            $secNIC = mysqli_real_escape_string($conn, $nic); // secure NIC number
            $encPsw = mysqli_real_escape_string($conn, sha1($psw1)); // encryption password
            $secCustom1 = mysqli_real_escape_string($conn, $custom1); // secure custom field 1
            $encAnswer1 = mysqli_real_escape_string($conn, sha1($answer1)); // encryption answer 1;
            $secCustom2 = mysqli_real_escape_string($conn, $custom2); // secure custom field 2
            $encAnswer2 = mysqli_real_escape_string($conn, sha1($answer2)); // encryption answer 2;
            if (strlen($custom1) > 0) {
            /* set custom 1 */
                if (strlen($custom2) > 0) {
                /* set custom 2 */
                    $sql = "INSERT INTO `Admin` (
                        `adminId`,
                        `firstName`,
                        `lastName`,
                        `contact`,
                        `nic`,
                        `password`,
                        `optionOne`,
                        `customOne`,
                        `answerOne`,
                        `optionTwo`,
                        `customTwo`,
                        `answerTwo`
                    ) VALUES (
                        '$id',
                        '$secFName',
                        '$secLName',
                        '$tel',
                        '$secNIC',
                        '$encPsw',
                        0,
                        '$secCustom1',
                        '$encAnswer1',
                        0,
                        '$secCustom2',
                        '$encAnswer2'
                    )";
                } else {
                /* not set custom 2 */
                    $sql = "INSERT INTO `Admin` (
                        `adminId`,
                        `firstName`,
                        `lastName`,
                        `contact`,
                        `nic`,
                        `password`,
                        `optionOne`,
                        `customOne`,
                        `answerOne`,
                        `optionTwo`,
                        `answerTwo`
                    ) VALUES (
                        '$id',
                        '$secFName',
                        '$secLName',
                        '$tel',
                        '$secNIC',
                        '$encPsw',
                        0,
                        '$secCustom1',
                        '$encAnswer1',
                        '$select2',
                        '$encAnswer2'
                    )";
                }
            } else {
            /* not set custom 1 */
                if (strlen($custom2) > 0) {
                /* set custom 2 */
                    $sql = "INSERT INTO `Admin` (
                        `adminId`,
                        `firstName`,
                        `lastName`,
                        `contact`,
                        `nic`,
                        `password`,
                        `optionOne`,
                        `answerOne`,
                        `optionTwo`,
                        `customTwo`,
                        `answerTwo`
                    ) VALUES (
                        '$id',
                        '$secFName',
                        '$secLName',
                        '$tel',
                        '$secNIC',
                        '$encPsw',
                        '$select1',
                        '$encAnswer1',
                        0,
                        '$secCustom2',
                        '$encAnswer2'
                    )";
                } else {
                /* not set custom 2 */
                    $sql = "INSERT INTO `Admin` (
                        `adminId`,
                        `firstName`,
                        `lastName`,
                        `contact`,
                        `nic`,
                        `password`,
                        `optionOne`,
                        `answerOne`,
                        `optionTwo`,
                        `answerTwo`
                    ) VALUES (
                        '$id',
                        '$secFName',
                        '$secLName',
                        '$tel',
                        '$secNIC',
                        '$encPsw',
                        '$select1',
                        '$encAnswer1',
                        '$select2',
                        '$encAnswer2'
                    )";
                }
            }
        /* prepare the query */
            $stmt = mysqli_query($conn, $sql);
            if ($stmt == true) {
                header("location: /admin/dashboard/?info=Successfully added."); # successfully added
                exit();
            } else {
                $info[] = "Something went wrong. Please try again.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Add New Admin</title>
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
            width: 80%;
            margin: auto;
            margin-top: 100px;
            overflow: auto;
        }
        main .box-layout .title-header {
            margin: auto;
            padding: 14px;
            border: 1px solid #fff;
            border-radius: 4px 4px 0 0;
            background-color: #fff;
            color: #000000;
        }
        main .box-layout .sub-header {
            margin: auto;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 8px;
            border: 1px solid #fff;
            border-radius: 4px 4px 0 0;
            background-color: #008000;
            color: #fff;
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
        </div>
    </header>
    <main>
        <form action="/search/" method="get" autocomplete="off" class="search-form">
            <input type="search" name="result" placeholder="Enter anything computer products related.. 🔍" maxlength="100" required autofocus>
        </form>
    <!-- sign up -->
        <div class="box-layout">
            <h3 class="title-header">Create New Administrator Account</h3>
            <form action="/admin/sign-up/" method="post" autocomplete="off">
                <?php
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note' style='color: #808080;'>$value</p>";
                    }
                ?>
                <input type="text" name="fName" placeholder="First name *" value="<?php echo $fName; ?>" maxlength="16" required>
                <input type="text" name="lName" placeholder="Last name (Optional)" value="<?php echo $lName; ?>" maxlength="16">
                <input type="tel" name="contact" placeholder="Contact number (Ex:- 0XXXXXXXXX) *" value="<?php echo $tel; ?>" pattern="[0][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]" maxlength="10" required>
                <input type="text" name="nic" placeholder="NIC number *" value="<?php echo $nic; ?>" minlength="9" maxlength="12" required>
            <!-- set Safe password -->
                <h3 class="sub-header">Set Safe password</h3>
                <input type="password" name="psw1" placeholder="Enter password *" value="<?php echo $psw1; ?>" minlength="6" maxlength="16" required>
                <input type="password" name="psw2" placeholder="Confirm password *" value="<?php echo $psw2; ?>" minlength="6" maxlength="16" required>
                <p class="custom-note">Password must consist of 6 to 16 characters, and include at least one letter</p>
            <!-- password security question -->
                <h3 class="sub-header">Password security question</h3>
                <p class="custom-note" style="margin-bottom: 20px;">It's very important to remember the answers to your security questions, it will be the only way to reset your password.</p>
            <!-- second question -->
                <fieldset>
                <!-- default selection -->
                    <select name="sel1">
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
                <input type="text" name="sel1-ans" placeholder="Answer *" value="<?php echo $answer1; ?>" minlength="3" maxlength="16" required>
                <p class="custom-note">Please enter 3 to 16 characters</p>
            <!-- second question -->
                <fieldset style="margin-top: 20px;">
                <!-- default selection -->
                    <select name="sel2">
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
                <input type="text" name="sel2-ans" placeholder="Answer *" value="<?php echo $answer2; ?>" minlength="3" maxlength="16" required>
                <p class="custom-note" style="margin-bottom: 20px;">Please enter 3 to 16 characters</p>
            <!-- done & cancel btn -->
                <a href="/admin/dashboard/"><button type="button">Cancel</button></a>
                <button type="submit" onclick="return confirm('Are you sure you want to create a new account?')">Done</button>
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
            <h4>&copy; 2025 Dilla's PC, Inc. All rights reserved.</h4>
        </div>
    </footer>
</body>
</html>