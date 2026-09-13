<?php
    require_once("../../include/server/index.php");
    require_once("../../include/delete-attachment/index.php");
/* check session */
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/');
        exit();
    }
/* check if click member name */
    if (isset($_GET['memberId']) && $_GET['memberId'] != null) {
        $_SESSION['memberId'] = $_GET['memberId'];
        header('Location: /admin/message-center/'); # redirect to message center page
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Chat List</title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main .search-form, main .about-layout p, main .about-layout h3 {
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
        main .about-layout {
            width: 80%;
            margin: auto;
            margin-top: 100px;
            overflow: auto;
        }
        main .about-layout .team-header {
            margin: auto;
            padding: 14px;
            border: 1px solid #fff;
            border-radius: 4px 4px 0 0;
            background-color: #fff;
            color: #000000;
        }
        main .about-layout .list-layout {
            max-height: 300px;
            margin: auto;
            overflow: auto;
        }
        main .about-layout .list-layout table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        main .about-layout .list-layout table td {
            padding: 14px;
            border-bottom: 1px dotted #808080;
        }
        main .about-layout .list-layout table td a {
            display: block;
            text-decoration: none;
        }
        main .about-layout .list-layout table td a img {
            width: 100%;
            height: 65px;
            border: 1px solid #fff;
            border-radius: 50%;
            object-fit: cover;
        }
        main .about-layout .list-layout table td a span {
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
        main .about-layout .list-layout table td .dp-layout {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background-color: #808080;
            color: #fff;
        }
        main .about-layout .list-layout table td .sender-name {
            font-size: large;
            color: #fff;
        }
    /* back btn style */
        main .about-layout button {
            margin: auto;
            margin-top: 20px;
            margin-left: 8px;
            padding: 8px;
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
    <!-- chat list -->
        <div class="about-layout">
            <h3 class="team-header">Inbox Messages <a href="/admin/chat-list/" title="Reload page" style="text-decoration: none;">🔃</a></h3>
            <div class="list-layout">
                <table>
                    <?php
                    /* check unread member's messages */
                        $sql = "SELECT
                            `memberId`,
                            `image`,
                            `firstName`,
                            `lastName`,
                            COUNT(`seen`) AS `unread`,
                            `sender`
                        FROM
                            `Member`,
                            `Message`
                        WHERE
                            `memberId` = `sender` AND
                            `sender` != 'Admin' AND
                            `seen` = 0 AND
                            `Message`.`trash` = 0
                        GROUP BY
                            `sender`
                        ORDER BY
                            `unread` DESC
                        ";
                        $stmt = mysqli_query($conn, $sql);
                        if ($stmt == true) {
                            if (mysqli_num_rows($stmt) == 0) {
                            /* load opened member's chat */
                                $sql = "SELECT
                                    DISTINCT `memberId`,
                                    `image`,
                                    `firstName`,
                                    `lastName`
                                FROM
                                    `Member`,
                                    `Message`
                                WHERE
                                    `memberId` = `sender` AND
                                    `sender` != 'Admin' AND
                                    `Message`.`trash` = 0
                                ORDER BY
                                    `firstName` ASC
                                ";
                                $stmt = mysqli_query($conn, $sql);
                                if ($stmt == true) {
                                /* process data */
                                    while ($row = mysqli_fetch_assoc($stmt)) {
                                        echo "<tr>";
                                        echo "<td><a href='/admin/chat-list/?memberId=$row[memberId]' class='dp-layout'><img src='data:image; base64, $row[image]' alt='profile photo'></a></td>";
                                        echo "<td><a href='/admin/chat-list/?memberId=$row[memberId]' class='sender-name'>$row[firstName] $row[lastName]</a></td>";
                                        echo "</tr>";
                                    }
                                }
                            } else {
                            /* process data */
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td><a href='/admin/chat-list/?memberId=$row[memberId]' class='dp-layout'><img src='data:image; base64, $row[image]' alt='profile photo'></a></td>";
                                    echo "<td><a href='/admin/chat-list/?memberId=$row[memberId]' class='sender-name'>$row[firstName] $row[lastName]";
                                    if ($row['unread'] > 0) {
                                        echo "<span>$row[unread]</span>"; # display count of unread messages
                                    }
                                    echo "</a></td>";
                                    echo "</tr>";
                                }
                            }
                        }
                    ?>
                </table>
            </div>
        <!-- back to home -->
            <a href="/admin/dashboard/"><button type="button">Cancel</button></a>
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