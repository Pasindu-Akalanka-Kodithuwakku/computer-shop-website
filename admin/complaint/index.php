<?php
    require_once("../../include/server/index.php");
    $info = array();
/* check session */
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/');
        exit();
    }
/* delete message */
    if (isset($_GET['return']) && $_GET['return'] != null && $_GET['delete'] == "yes") {
        $sql = "DELETE FROM `Complaint` WHERE `complaintId` = '$_GET[return]'";
        $stmt = mysqli_query($conn, $sql);
        if (mysqli_affected_rows($conn) == 1) {
            $info[] = "Successfully deleted.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - View Complaints</title>
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
        main .about-layout .list-layout table th {
            padding: 14px;
            background-color: #808080;
            text-align: justify;
            text-transform: uppercase;
            position: sticky;
            top: 0;
        }
        main .about-layout .list-layout table td {
            padding: 14px;
            text-align: justify;
            vertical-align: top;
            border-bottom: 1px solid #808080;
        }
        main .about-layout .list-layout table td a {
            display: block;
            text-decoration: none;
        }
    /* information style */
        main .about-layout .list-layout .custom-note {
            margin: 4px auto;
            padding: 8px;
            border: 1px solid #fff;
            background-color: #ff0000;
            color: #fff;
        }
    /* back btn style */
        main .about-layout .list-layout button {
            margin: 4px auto;
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
    <!-- complaint list -->
        <div class="about-layout">
            <h3 class="team-header">Inbox Complaints <a href="/admin/complaint/" title="Reload page" style="text-decoration: none;">🔃</a></h3>
            <div class="list-layout">
                <?php
                /* display informations */
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note'>$value</p>";
                    }
                ?>
                <table>
                    <tr>
                        <th>About</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                    <?php
                    /* check unread complaints */
                        $sql = "SELECT
                            `complaintId`,
                            `description`,
                            `about`,
                            `category`,
                            `uploadDate`
                        FROM
                            `Complaint`
                        ORDER BY
                            `about` ASC,
                            `category` ASC,
                            `uploadDate` ASC
                        ";
                        $stmt = mysqli_query($conn, $sql);
                        if ($stmt == true) {
                        /* process data */
                            while ($row = mysqli_fetch_assoc($stmt)) {
                                echo "<tr>";
                                echo "<td>$row[about]</td>";
                                echo "<td>$row[category]</td>";
                                echo "<td>$row[description]</td>";
                                echo "<td>$row[uploadDate]</td>";
                                echo "<td>";
                                ?><a href="/admin/complaint/?return=<?php echo $row['complaintId']; ?>&delete=yes" title="Delete message"
                                    onclick="return confirm('PERMANENTLY DELETE 🚮\n\n\tAre you sure you want to permanently delete the selected message?')"
                                    style="text-transform: uppercase; font-weight: bold; color: #008000;">Delete 🗑️</a><?php
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                    /* update the trash */
                        $sql = "UPDATE `Complaint` SET `trash` = 1";
                        $stmt = mysqli_query($conn, $sql);
                        if ($stmt == false) {
                            $info[] = "Query executing failed.";
                        }
                    ?>
                </table>
            <!-- back to home -->
                <a href="/admin/dashboard/"><button type="button">Cancel</button></a>
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