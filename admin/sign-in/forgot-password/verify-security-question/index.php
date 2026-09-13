<?php
    require_once("../../../../include/server/index.php"); 
    if (!(isset($_REQUEST['contact']) && isset($_REQUEST['nic']))) {
        header("location: /admin/sign-in/forgot-password/");
        exit();
    }
    $info = array();
/* check mobile number */
    $tel = mysqli_real_escape_string($conn, $_REQUEST['contact']); // secure mobile number
    $nic = mysqli_real_escape_string($conn, $_REQUEST['nic']); // secure mobile number
    $sql = "SELECT * FROM `Admin` WHERE `contact` = '$tel' AND `nic` = '$nic' AND `trash` = 0 LIMIT 1";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt == true && mysqli_num_rows($stmt) == 1) {
    /* load data */
        $row = mysqli_fetch_assoc($stmt); # load data
        $tel = $row['contact'];
        $nic = $row['nic'];
    } else {
        header("location: /admin/sign-in/forgot-password/?error=Incorrect mobile number or NIC number."); # redirect to back page
        exit();
    }
/* check errors */
    if (isset($_GET['error'])) {
        $info[] = "Incorrect answer."; # set an error
    }
/* set question 1 */
    $ques1 = array(
        0 => $row['customOne'],
        1 => "What is your father's name?",
        2 => "What is your partner's name?",
        3 => "What middle school did you go to?",
        4 => "What is the name of the street where you grew up?",
        5 => "What is the name of your favorite singer or band?"
    );
    foreach ($ques1 as $key => $value) {
        if ($row['optionOne'] == $key) {
            $label1 = $value;
        }
    }
/* set question 2 */
    $ques2 = array(
        0 => $row['customTwo'],
        1 => "What is your mother's name?",
        2 => "What elementary school did you go to?",
        3 => "Where did you go the first time you flew on a plane?",
        4 => "What is your favorite sports team?",
        5 => "What is your dream job?"
    );
    foreach ($ques2 as $key => $value) {
        if ($row['optionTwo'] == $key) {
            $label2 = $value;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Admin - Forgot Password</title>
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
        main .box-layout form label {
            display: block;
            margin: 4px auto;
            padding: 8px;
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
        </div>
    </header>
    <main>
        <form action="/search/" method="get" autocomplete="off" class="search-form">
            <input type="search" name="result" placeholder="Enter anything computer products related.. 🔍" maxlength="100" required autofocus>
        </form>
    <!-- sign in -->
        <div class="box-layout">
            <h3 class="title-header">Admin - Verify security question</h3>
            <form action="/admin/sign-in/forgot-password/reset-password/" method="post" autocomplete="off">
                <?php
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note' style='color: #808080;'>$value</p>";
                    }
                ?>
                <input type="hidden" name="contact" value="<?php echo $tel; ?>"> <!-- pass value -->
                <input type="hidden" name="nic" value="<?php echo $nic; ?>"> <!-- pass value -->
                <label for="ques1"><?php echo $label1; ?></label>
                <input type="text" name="ans1" id="ques1" placeholder="Answer" required>
                <label for="ques2"><?php echo $label2; ?></label>
                <input type="text" name="ans2" id="ques2" placeholder="Answer" required>
                <a href="/admin/sign-in/forgot-password/"><button type="button">Cancel</button></a>
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
</body>
</html>