<?php
    session_start();
    require_once("../../include/server/index.php");
    require_once("../../function/create-primary-key/index.php");
    require_once("../../function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (!isset($_SESSION['memberId'])) {
        header("location: /member/sign-in/");
        exit();
    }
    $info = array();
/* check sent message */
    if (isset($_POST['message'])) {
        if (($_FILES['attach']['tmp_name'][0] != null) || ($_POST['message'] != null)) {
            $impAttach = '';
        /* attachment */
            if ($_FILES['attach']['tmp_name'][0] != null) {
            /* check total size of uploads files */
                $totalFileSize = 0;
                for ($i=0; $i < count($_FILES['attach']['tmp_name']); $i++) {
                    $totalFileSize = $totalFileSize + $_FILES['attach']['size'][$i];
                }
                if ((($totalFileSize / 1024) / 1024) < 100) {
                    if (!is_dir("../../attachment/$_SESSION[memberId]")) {
                        mkdir("../../attachment/$_SESSION[memberId]", 0777, true); # create new attachment folder
                    }
                /* create new file for invalid inputs */
                    $createFile = fopen("../../attachment/index.php", 'w'); # file pointer
                    fwrite($createFile, "<?php\n/* redirect main page */\n\theader('Location: /');\n\texit();\n?>"); # write into this file
                    fclose($createFile); # close the file
                /* create new file for invalid inputs */
                    $createFile = fopen("../../attachment/$_SESSION[memberId]/index.php", 'w'); # file pointer
                    fwrite($createFile, "<?php\n/* redirect main page */\n\theader('Location: /');\n\texit();\n?>"); # write into this file
                    fclose($createFile); # close the file
                /* move uploaded files */
                    for ($i=0; $i < count($_FILES['attach']['tmp_name']); $i++) {
                        move_uploaded_file($_FILES['attach']['tmp_name'][$i], "../../attachment/$_SESSION[memberId]/" .$_FILES['attach']['name'][$i]);
                    }
                    $impAttach = implode("/ ", $_FILES['attach']['name']);
                } else {
                    $info[] = "Oops! The files you sent cannot be uploaded because the combined size is too large.";
                }
            }
        /* send message */
            $id = createPrimaryKey($conn, "Message", "messageId", "MG");
            $encMessage = mysqli_real_escape_string($conn, base64_encode($_POST['message']));
            $encAttachment = mysqli_real_escape_string($conn, $impAttach);
        /* execute query */
            if ($_POST['reply'] != '') {
                $sql = "INSERT INTO `Message`(
                    `messageId`,
                    `description`,
                    `attachment`,
                    `sender`,
                    `receiver`,
                    `reply`
                ) VALUES (
                    '$id',
                    '$encMessage',
                    '$encAttachment',
                    '$_SESSION[memberId]',
                    'Admin',
                    '$_POST[reply]'
                )";
            } else {
                $sql = "INSERT INTO `Message`(
                    `messageId`,
                    `description`,
                    `attachment`,
                    `sender`,
                    `receiver`
                ) VALUES (
                    '$id',
                    '$encMessage',
                    '$encAttachment',
                    '$_SESSION[memberId]',
                    'Admin'
                )";
            }
            if (empty($info)) {
                $stmt = mysqli_query($conn, $sql);
            }
        } else {
            $info[] = "The message has no content. 🙄";
        }
    }
/* check if click additional buttons */
    if (isset($_GET['index']) && $_GET['index'] != '') {
        $sql = "SELECT
            `messageId`,
            `description`,
            `attachment`
        FROM
            `Message`
        WHERE
            `messageId` = '$_GET[index]' AND
            (`sender` = '$_SESSION[memberId]' OR
            `receiver` = '$_SESSION[memberId]') AND
            `clearBySender` = 0 AND
            `trash` = 0
        LIMIT 1
        ";
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == true && mysqli_num_rows($stmt) == 1) {
            $row = mysqli_fetch_assoc($stmt);
            if (isset($_GET['delete']) && $_GET['delete'] == "yes") {
                $messageId = '';
                $sql = "UPDATE `Message` SET `clearBySender` = 1 WHERE `messageId` = '$row[messageId]'";
                $stmt = mysqli_query($conn, $sql);
                if (mysqli_affected_rows($conn) == 1) {
                    $info[] = "The message was deleted.";
                }
            } else if (isset($_GET['reply']) && $_GET['reply'] == "yes") {
                $messageId = $row['messageId'];
                if ($row['attachment'] != null) {
                    $attachment = substr($row['attachment'], 0, 30) ." more..";
                }
                if ($row['description'] != null) {
                    $description = substr(base64_decode($row['description']), 0, 30) ." more..";
                }
            }
        } else {
            header("location: /member/message-center/");
            exit();
        }
    } else {
        $messageId = '';
    }
/* logout process */
    if (isset($_GET['logout']) && $_GET['logout'] == "yes") {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie($_COOKIE[session_name()], '', -time()+60*60*24, '/');
        }
        session_destroy();
        header("location: /member/sign-in/"); # redirect to login page
        exit();
    }
/* display passes information */
    if (isset($_GET['info'])) {
        $info[] = $_GET['info'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Message Center</title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main .search-form, main .box-layout p, main .box-layout h3 {
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
            margin-top: 80px;
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
        main .box-layout .sub-header {
            box-sizing: border-box;
            margin: auto;
            margin-top: 20px;
            padding: 8px;
            border: 1px solid #fff;
            border-radius: 100px;
            text-transform: capitalize;
            background-color: rgb(25, 25, 25);
            color: #fff;
            text-shadow: -1px 0 #ff0000, 0 1px #ff0000, 1px 0 #ff0000, 0 -1px #ff0000;
        }
    /* window-layout style */
        main .box-layout .window-layout {
            width: 100%;
            box-sizing: border-box;
            max-height: 300px;
            margin: 20px auto;
            padding: 20px;
            overflow: auto;
            scrollbar-width: none;
        }
        main .box-layout table, main .box-layout .window-layout table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        main .box-layout table td, main .box-layout .window-layout table td {
            padding: 8px;
        }
        main .box-layout .window-layout table td img, main .box-layout .window-layout table td audio, main .box-layout .window-layout table td video {
            display: block;
            width: 150px;
            margin-bottom: 4px;
            border-radius: 4px;
        }
        main .box-layout .window-layout table td p {
            display: inline-block;
            max-width: 80%;
            text-align: justify;
            padding: 4px 8px;
            color: #fff;
        }
        main .box-layout .window-layout table td a {
            display: inline-block;
            padding: 4px 8px;
            text-decoration: none;
            color: #808080;
        }
        main .box-layout .window-layout table td a:hover {
            color: #008000;
        }
    /* messagebox-layout style */
        main .box-layout .messagebox-layout form {
            width: 100%;
            box-sizing: border-box;
            padding: 20px;
        }
        main .box-layout .messagebox-layout form label {
            display: block;
            margin: auto;
            padding: 4px 8px;
            font-style: italic;
            background-color: #ddd;
            color: #000000;
            text-shadow: 2px 2px 5px #000000;
            border-radius: 4px;
            cursor: pointer;
            opacity: .8;
        }
        main .box-layout .messagebox-layout form textarea {
            width: 100%;
            box-sizing: border-box;
            height: 100px;
            margin: 4px auto;
            padding: 8px;
            outline: none;
            border-radius: 4px;
            border-style: solid;
            border-color: #808080;
        }
        main .box-layout .messagebox-layout form button, main .box-layout .messagebox-layout form input[type="file"] {
            box-sizing: border-box;
            margin: 4px auto;
            padding: 8px;
            color: #000000;
        }
        main .box-layout .messagebox-layout form input[type="file"] {
            width: 100%;
            border-width: 1px;
            border-style: solid;
            border-color: #909090;
            border-radius: 4px;
            margin-top: 20px;
            cursor: pointer;
            color: #fff;
        }
        main .box-layout .messagebox-layout form button {
            cursor: pointer;
            font-weight: bold;
            color: #0000ff;
        }
        main .box-layout .messagebox-layout form button[type="reset"] {
            color: #ff0000;
        }
    /* custom message style */
        main .box-layout .custom-note {
            margin: 4px auto;
            padding: 8px;
            border: 1px solid #fff;
            background-color: #ff0000;
            color: #fff;
        }
    /* profile details style */
        main .box-layout table .pro-pic-layout {
            text-align: center;
        }
        main .box-layout table .pro-pic-layout a {
            display: inline-block;
            width: 85px;
            height: 85px;
            border-radius: 50%;
            text-decoration: none;
            background: #909090;
            color: #fff;
        }
        main .box-layout table .pro-pic-layout img {
            display: block;
            width: 85px;
            height: 85px;
            border-radius: 50%;
            object-fit: cover;
        }
        main .box-layout table .mem-del-layout {
            overflow: hidden;
        }
        main .box-layout table .mem-del-layout p {
            margin: 4px auto;
        }
        main .box-layout table .mem-del-layout a {
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #fff;
            color: #ff0000;
        }
        main .box-layout table .acc-opt-layout a {
            display: inline-block;
            margin: 4px auto;
            padding: 8px;
            text-decoration: none;
            color: #808080;
        }
        main .box-layout table .acc-opt-layout a:hover {
            font-weight: bold;
            text-transform: uppercase;
            color: #909090;
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
    <!-- message center -->
        <div class="box-layout">
            <h3 id="Message-Center" class="title-header">Message Center 📶</h3>
            <?php
            /* set member details */
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
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true && mysqli_num_rows($stmt) == 1) {
                    $row = mysqli_fetch_assoc($stmt);
                } else {
                /* session destroy & redirect login page */
                    $_SESSION = array();
                    if (isset($_COOKIE[session_name()])) {
                        setcookie($_COOKIE[session_name()], '', -time()+60*60*24, '/');
                    }
                    session_destroy();
                    header("location: ../sign-in/"); // not valid member
                    exit();
                }
            ?>
            <table>
                <tr>
                    <td class="pro-pic-layout">
                        <a><img src="data:image; base64, <?php echo $row['image']; ?>" alt="profile-photo"></a> <!-- profile photo -->
                    </td>
                    <td class="mem-del-layout">
                        <?php echo "<p style='font-size: x-large; text-transform: uppercase; font-weight: bold;'>🪪 " .$row['firstName'] ." " .$row['lastName'] ."</p>"; ?>
                        <?php echo "<p style='font-size: large;'>📧 " .$row['email'] ."</p>"; ?>
                        <?php echo "<p style='font-size: large;'>📞 " .$row['contact'] ."</p>"; ?>
                        <?php echo "<p style='margin-top: 20px; margin-left: 30px;'><a href='/member/message-center/?logout=yes' title='Log out form your account' style='background-color: #ff0000; color: #fff; padding: 8px; border-radius: 4px; box-shadow: 2px 2px #000000;'>Sign out</a></p>"; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="acc-opt-layout">
                        <a href="/member/message-center/" title="Reload page">Refresh page 🔄️</a>
                        <a href="/member/message-center/edit-profile/">Edit profile 🛠️</a>
                        <a href="/member/message-center/change-password/">Change password 🛡️</a>
                        <a href="/member/message-center/change-security-question/">Change security question ⚙️</a>
                        <a href="/member/message-center/destroy-account/" title="Delete permanently" onclick="return confirm('Delete Account 💀\n\nThis will erase certain data from account, including: ⚡\n\n\tAll direct messages you sent\n\tYour personal account\n\nAre you sure you want to delete account permanently? ⚠️')">Destroy account 🪦</a>
                    </td>
                </tr>
            </table><hr color="#ffd700">
            <?php
                foreach ($info as $key => $value) {
                    echo "<p class='custom-note'>$value</p>";
                }
            ?>
            <div class="window-layout">
                <?php
                    $memberId = $_SESSION['memberId'];
                    $sql = "SELECT
                        `messageId`,
                        `description`,
                        `attachment`,
                        `sender`,
                        `reply`,
                        `sentDate`
                    FROM
                        `Message`
                    WHERE
                        (`sender` = '$memberId' OR
                        `receiver` = '$memberId') AND
                        `clearBySender` = 0 AND
                        `trash` = 0
                    ORDER BY
                        `sentDate` ASC
                    ";
                    $stmt = mysqli_query($conn, $sql);
                    $countMessage = 0;
                    if ($stmt == true) {
                        echo "<table>";
                        while ($row = mysqli_fetch_assoc($stmt)) {
                            if ($row['sender'] == $memberId) {
                                echo "<tr><td style='text-align: right;'>"; // sender messages
                            } else {
                                echo "<tr><td style='text-align: left;'>"; // receiver messages
                            }
                        /* check if is reply message */
                            if ($row['reply'] != null) {
                                $sql2 = "SELECT
                                    `description`,
                                    `attachment`
                                FROM
                                    `Message`
                                WHERE
                                    `trash` = 0 AND
                                    `messageId` = '$row[reply]'
                                LIMIT 1
                                ";
                                $stmt2 = mysqli_query($conn, $sql2);
                                if ($stmt2 == true) {
                                    $row2 = mysqli_fetch_assoc($stmt2);
                                    if ($row2['attachment'] != null) {
                                        echo "<p style='margin-right: 8px; border-radius: 4px; background-color: #909090; color: #fff;'>" .substr($row2['attachment'], 0, 30) ." more..</p><br>";
                                    }
                                    if ($row2['description'] != null) {
                                        echo "<p style='margin-right: 8px; border-radius: 4px; background-color: #909090; color: #fff;'>" .substr(base64_decode($row2['description']), 0, 30) ." more..</p><br>";
                                    }
                                }
                            }
                        /* inserted attachment */
                            if ($row['attachment'] != null) {
                                $picArray = array(".jpg", ".jpeg", ".png", ".bmp");
                                $audArray = array(".mp3", ".ogg", ".wav");
                                $vidArray = array(".mp4");
                                $foundExten = 0;
                            /* check if a image */
                                foreach (explode("/ ", $row['attachment']) as $key => $value) {
                                    for ($i=0; $i < count($picArray); $i++) {
                                        if (strtolower(substr($value, -4)) == $picArray[$i] || strtolower(substr($value, -5)) == $picArray[$i]) {
                                            echo "<a href='/attachment/$memberId/$value' title='Click image to view full size' target='_blank'>"; // click image to show full size
                                            echo "<img src='/attachment/$memberId/$value' alt='$value' id='$countMessage'>";
                                            echo "</a>";
                                            $foundExten = 1;
                                        }
                                    }
                                }
                                if ($foundExten == 1) {
                                    echo "<br>"; // set line break
                                    $foundExten = 2;
                                }
                            /* check if a audio */
                                foreach (explode("/ ", $row['attachment']) as $key => $value) {
                                    for ($i=0; $i < count($audArray); $i++) {
                                        if (strtolower(substr($value, -4)) == $audArray[$i]) {
                                            echo "<a href='/attachment/$memberId/$value' title='Click audio to view full size' target='_blank'>"; // click audio to show full size
                                            echo "<audio controls controlsList='nodownload' id='$countMessage'><source src='/attachment/$memberId/$value'></audio>";
                                            echo "</a>";
                                            $foundExten = 1;
                                        }
                                    }
                                }
                                if ($foundExten == 1) {
                                    echo "<br>"; // set line break
                                    $foundExten = 2;
                                }
                            /* check if a video */
                                foreach (explode("/ ", $row['attachment']) as $key => $value) {
                                    for ($i=0; $i < count($vidArray); $i++) {
                                        if (strtolower(substr($value, -4)) == $vidArray[$i]) {
                                            echo "<a href='/attachment/$memberId/$value' title='Click video to view full size' target='_blank'>"; // click video to show full size
                                            echo "<video controls controlsList='nodownload' id='$countMessage'><source src='/attachment/$memberId/$value'></video>";
                                            echo "</a>";
                                            $foundExten = 1;
                                        }
                                    }
                                }
                                if ($foundExten == 1) {
                                    echo "<br>"; // set line break
                                    $foundExten = 1;
                                }
                            /* no found readable extension file */
                                if ($foundExten == 0) {
                                    foreach (explode("/ ", $row['attachment']) as $key => $value) {
                                        echo "<a href='/attachment/$memberId/$value' id='$countMessage' title='Click file to open' target='_blank' style='color: #808080;'>$value</a>"; // click file to open
                                    }
                                    echo "<br>";
                                }
                            }
                        /* inserted message */
                            if ($row['description'] != null) {
                                echo "<p id='$countMessage'>" .base64_decode($row['description']) ."</p><br>";
                            }
                        /* display sent date */
                            echo "<p style='color: #fff;'>$row[sentDate]</p><br>";
                        /* additional btns */
                            ?><a href='<?php echo "/member/message-center/?index=$row[messageId]&reply=yes&msgId=$countMessage#Message-Center"; ?>' title="Reply">Reply ⤴️</a><?php // reply button
                            ?><a href='<?php echo "/member/message-center/?index=$row[messageId]&delete=yes"; ?>' onclick="return confirm('Delete message?\n\n\tDelete for me')" title="Delete for me">Delete 🗑️</a><?php // delete button
                            echo "<hr style='border-style: dashed;'></td></tr>";
                            $countMessage++;
                        }
                        echo "</table>";
                    }
                ?>
            </div><hr color="#ffd700">
            <div class="messagebox-layout">
                <form action="<?php echo "/member/message-center/#" .($countMessage); ?>" method="post" enctype="multipart/form-data" autocomplete="off">
                    <?php
                        if (isset($attachment) || isset($description)) {
                            echo "<a href='#$_GET[msgId]' style='text-decoration: none;'>";
                            echo "<label style='margin-bottom: 4px; font-style: normal; background-color: #fff;'>You're replying now ↩️</label>";
                            if (isset($attachment)) {
                                echo "<label>$attachment</label>";
                            }
                            if (isset($description)) {
                                echo "<label>$description</label>";
                            }
                            echo "</a>";
                        }
                    ?>
                    <input type="hidden" name="reply" value="<?php echo $messageId; ?>">
                    <textarea name="message" placeholder="Type message..." maxlength="1500"></textarea>
                    <button type="reset" onclick="return confirm('Are you sure you want to clear all data?')">Clear ❌</button>
                    <button type="submit">Send Message ✔️</button>
                <!-- attachment -->
                    <h3 class="sub-header">Attachment 🔗</h3>
                    <input type="file" name="attach[]" multiple>
                </form>
            </div>
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