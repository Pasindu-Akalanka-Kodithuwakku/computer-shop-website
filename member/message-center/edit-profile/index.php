<?php
    session_start();
    require_once("../../../include/server/index.php"); 
    require_once("../../../function/create-primary-key/index.php");
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
/* check if click submit */
    if (isset($_POST['contact'])) {
        $fName = trim($_POST['fName']);
        $lName = trim($_POST['lName']);
        $tel = $_POST['contact'];
        $email = trim($_POST['mail']);
        $image = $_FILES['picture']['tmp_name'];
    /* check contact number */
        $sql = "SELECT * FROM `Member` WHERE `contact` = '$tel' AND `memberId` != '$_SESSION[memberId]' LIMIT 1";
        $stmt = mysqli_query($conn, $sql);
    /* prepare the query */
        if ($stmt == true && mysqli_num_rows($stmt) == 1) {
            $info[] = "Incorrect mobile number. Please try again.";
        }
    /* check email */
        $sql = "SELECT * FROM `Member` WHERE `email` = '$email' AND `memberId` != '$_SESSION[memberId]' LIMIT 1";
        $stmt = mysqli_query($conn, $sql);
    /* prepare the query */
        if ($email != null && $stmt == true && mysqli_num_rows($stmt) == 1) {
            $info[] = "Incorrect email address. Please try again.";
        }
    /* encrypt image */
        if ($image != null) {
            if (substr($_FILES['picture']['type'], 0, 5) == "image") {
                $image = base64_encode(file_get_contents($image));
            } else {
                $info[] = "Please select only images that fall into the image category.";
            }
        }
    /* check member's names */
        $inChar = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $checkField = array($fName, $lName);
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
                } else {
                    if ($lName != null) {
                        $info[] = "Last name is not valid. Please try again."; // no included letters error
                    }
                }
            } else {
                $foundChar = 0;
            }
        }
    /* empty errors */
        if (empty($info)) {
            $secFName = mysqli_real_escape_string($conn, $fName); # secure first name
            $secLName = mysqli_real_escape_string($conn, $lName); # secure last name
            $secEmail = mysqli_real_escape_string($conn, $email); # secure email
        /* update new details */
            $sql = "UPDATE
                `Member`
            SET
                `firstName` = '$secFName',
                `lastName` = '$secLName',
                `contact` = '$tel',
                `email` = '$secEmail',
                `image` = '$image'
            WHERE
                `memberId` = '$_SESSION[memberId]'
            ";
            $stmt = mysqli_query($conn, $sql);
        /* prepare the query */
            if (mysqli_affected_rows($conn) == 1) {
                header("location: /member/message-center/?info=Successfully edited."); // redirect to back page
                exit();
            } else {
                $info[] = "<font style='color: #0000ff;'>You're not entered new detail(s) for update.</font>";
            }
        }
    } else {
    /* create a query for display stored data */
        $sql = "SELECT
            `firstName`,
            `lastName`,
            `contact`,
            `email`,
            `image`
        FROM
            `Member`
        WHERE
            `memberId` = '$_SESSION[memberId]' AND
            `trash` = 0
        LIMIT 1";
    /* prepare the query */
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == true && mysqli_num_rows($stmt) == 1) {
            $row = mysqli_fetch_assoc($stmt); // load data
            $fName = $row['firstName'];
            $lName = $row['lastName'];
            $tel = $row['contact'];
            $email = $row['email'];
            $image = $row['image'];
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Edit Profile</title>
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
        main .box-layout form label {
            display: inline-block;
            margin: 4px auto;
            padding: 8px;
            color: #fff;
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
        main .box-layout form input[type="file"] {
            border-width: 1px;
            border-style: solid;
            cursor: pointer;
            border-color: #909090;
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
    /* show-image */
        main .box-layout form .show-image {
            width: 100px;
            height: 100px;
            margin: 8px;
            border-radius: 4px;
            background-color: #909090;
            color: #fff;
        }
        main .box-layout form .show-image img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
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
            <h3 class="title-header">Edit Profile</h3>
            <form action="/member/message-center/edit-profile/" method="post" enctype="multipart/form-data" autocomplete="off">
                <?php
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note' style='color: #808080;'>$value</p>";
                    }
                ?>
            <!-- infomation fields -->
                <input type="text" name="fName" placeholder="First name" value="<?php echo $fName; ?>" maxlength="16" required>
                <input type="text" name="lName" placeholder="Last name (Optional)" value="<?php echo $lName; ?>" maxlength="16">
                <input type="tel" name="contact" placeholder="Contact number (Ex:- 0XXXXXXXXX)" value="<?php echo $tel; ?>" pattern="[0][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]" maxlength="10" required>
                <input type="email" name="mail" placeholder="E-mail address (Optional)" value="<?php echo $email; ?>" maxlength="150">
                <label for="picture">Browse and select your image for the profile photo (Optional)</label>
                <input type="file" name="picture" id="picture" accept="image/*">
                <div class="show-image"><img src="data:image; base64, <?php echo $image; ?>" alt="profile-photo"></div>
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