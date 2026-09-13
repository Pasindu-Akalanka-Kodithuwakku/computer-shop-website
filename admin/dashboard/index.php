<?php
    require_once("../../include/server/index.php");
    require_once("../../include/delete-attachment/index.php");
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header("location: /admin/sign-in/");
        exit();
    }
    $info = array();
/* logout process */
    if (isset($_GET['logout']) && $_GET['logout'] == "yes") {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie($_COOKIE[session_name()], '', -time()+60*60*24, '/');
        }
        session_destroy();
        header("location: /admin/sign-in/"); # redirect to login page
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
    <title>Dilla's PC - Dashboard</title>
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
        main .box-layout table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        main .box-layout table td {
            padding: 8px;
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
        /* window layout design */
        main .box-layout .window-layout {
            margin: auto;
            overflow: auto;
            scrollbar-width: none;
        }
        main .box-layout .window-layout table {
            box-sizing: border-box;
            min-width: fit-content;
            margin-top: 20px;
            border-collapse: collapse;
            table-layout: fixed;
        }
        main .box-layout .window-layout table th, main .box-layout .window-layout table td {
            width: 200px;
            min-width: fit-content;
            padding: 8px;
        }
        main .box-layout .window-layout table th {
            padding: 14px;
            font-size: x-large;
            text-transform: uppercase;
            border-radius: 4px 4px 0 0;
            background-color: #fff;
            color: #000000;
        }
        main .box-layout .window-layout table td {
            text-align: center;
            vertical-align: top;
        }
        main .box-layout .window-layout table td a {
            display: block;
            padding: 8px;
            text-decoration: none;
            text-transform: capitalize;
            font-size: large;
            color: #fff;
        }
        main .box-layout .window-layout table td a:hover {
            border-radius: 4px;
            background-color: #808080;
            color: #000000;
            transition: all .5s;
        }
        main .box-layout .window-layout table td a span {
            display: inline-block;
            margin: auto;
            margin-left: 14px;
            margin-top: 4px;
            padding: 8px;
            border: 1px solid #fff;
            border-radius: 4px;
            font-weight: bold;
            background-color: #008000;
            color: #fff;
        }
    /* custom message style */
        main .box-layout .custom-note {
            margin: 4px auto;
            padding: 8px;
            border: 1px solid #fff;
            background-color: #ff0000;
            color: #fff;
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
    <!-- dashboard -->
        <div class="box-layout">
            <h3 id="Message-Center" class="title-header">Dashboard 🖥️</h3>
            <?php
            /* set admin details */
                $sql = "SELECT
                    `firstName`,
                    `lastName`,
                    `contact`,
                    `nic`,
                    `email`,
                    `image`
                FROM
                    `Admin`
                WHERE
                    `adminId` = '$_SESSION[adminId]' AND
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
                    header("location: /admin/sign-in/"); // not valid admin
                    exit();
                }
            ?>
            <table>
                <tr>
                    <td class="pro-pic-layout">
                        <a><img src="data:image; base64, <?php echo $row['image']; ?>" alt="profile-photo"></a> <!-- profile photo -->
                    </td>
                    <td class="mem-del-layout">
                        <?php echo "<p style='font-size: x-large; text-transform: uppercase; font-weight: bold;'>👨‍💻 " .$row['firstName'] ." " .$row['lastName'] ."</p>"; ?>
                        <?php echo "<p style='font-size: large;'>📧 " .$row['email'] ."</p>"; ?>
                        <?php echo "<p style='font-size: large;'>🪪 " .$row['nic'] ."</p>"; ?>
                        <?php echo "<p style='font-size: large;'>📞 " .$row['contact'] ."</p>"; ?>
                        <?php echo "<p style='margin-top: 20px; margin-left: 30px;'><a href='/admin/dashboard/?logout=yes' title='Log out form your account' style='background-color: #ff0000; color: #fff; padding: 8px; border-radius: 4px; box-shadow: 2px 2px #000000;'>Sign out</a></p>"; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="acc-opt-layout">
                        <a href="/admin/dashboard/" title="Reload page">Refresh page 🔄️</a>
                        <a href="/admin/dashboard/edit-profile/">Edit profile 🛠️</a>
                        <a href="/admin/dashboard/change-password/">Change password 🛡️</a>
                        <a href="/admin/dashboard/change-security-question/">Change security question ⚙️</a>
                        <a href="/admin/dashboard/destroy-account/" title="Delete permanently" onclick="return confirm('Delete Account 💀\n\nThis will erase certain data from account, including: ⚡\n\tYour personal account\n\nAre you sure you want to delete account permanently? ⚠️')">Destroy account 🪦</a>
                    </td>
                </tr>
            </table>
            <hr color="#808080">
            <?php
            /* display informations */
                foreach ($info as $key => $value) {
                    echo "<p class='custom-note'>$value</p>"; # information display
                }
            /* process unread messages */
                $unreadMessages = 0;
                $sql = "SELECT COUNT(*) AS `unread` FROM `Message` WHERE `seen` = 0 AND `trash` = 0";
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true) {
                    $unreadMessages =  mysqli_fetch_assoc($stmt)['unread']; # assign num of unread messages
                }
            /* process unread complaints */
                $unreadComplaints = 0;
                $sql = "SELECT COUNT(*) AS `unread` FROM `Complaint` WHERE `trash` = 0";
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true) {
                    $unreadComplaints =  mysqli_fetch_assoc($stmt)['unread']; # assign num of unread complaints
                }
            ?>
            <div class="window-layout">
                <table>
                    <tr>
                        <th>Manage Advertisments 📰</th>
                        <th>Manage Products ⌨️</th>
                        <th>Manage FAQ 📩</th>
                        <th>Manage Accounts ➕</th>
                        <th>Manage Complaint 🔔</th>
                    </tr>
                    <tr>
                        <td><a href="/admin/add-status/">Add new status</a></td>
                        <td><a href="/admin/add-item/">Add new items</a></td>
                        <td rowspan="4"><a href="/admin/chat-list/">View inbox messages <?php echo $unreadMessages > 0 ? "<span>" .$unreadMessages ."</span>" : '' ; ?></a></td>
                        <td rowspan="4"><a href="/admin/sign-up/">Add new admin account</a></td>
                        <td rowspan="4"><a href="/admin/complaint/">View inbox complaints <?php echo $unreadComplaints > 0 ? "<span>" .$unreadComplaints ."</span>" : '' ; ?></a></td>
                    </tr>
                    <tr>
                        <td><a href="/admin/manage-status/">Manage status</a></td>
                        <td><a href="/admin/manage-item/">Manage items</a></td>
                    </tr>
                    <tr>
                        <td><a href="/admin/add-news/">Add new daily news</a></td>
                        <td><a href="/admin/saved-item/">View all saved items</a></td>
                    </tr>
                    <tr>
                        <td><a href="/admin/manage-news/">Manage daily news</a></td>
                        <td><a href="/admin/supply-dealer/">View item supply dealers</a></td>
                    </tr>
                </table>
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
            <h4>&copy; 2025 Dilla's PC, Inc. All rights reserved.</h4>
        </div>
    </footer>
</body>
</html>