<?php
    require_once("../../include/server/index.php");
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/');
        exit();
    }
    $info = array();
/* check if click delete button */
    if (isset($_GET['updateId']) && $_GET['delete'] == "yes") {
        $sql = "DELETE FROM `Update` WHERE `updateId` = '$_GET[updateId]'";
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == true && mysqli_affected_rows($conn) == 1) {
            $info[] = "Successfully deleted.";
        }
    }
/* check if click search button */
    if (isset($_GET['search'])) {
    /* filter data */
        $search = trim($_GET['search']);
        $secSearch = mysqli_real_escape_string($conn, $search);
        $sql = "SELECT
            `updateId`,
            `description`,
            `lastUpdateDate`
        FROM
            `Update`
        WHERE
            (`description` LIKE '%$secSearch%' OR
            `lastUpdateDate` LIKE '%$secSearch%') AND
            `trash` = 0
        ORDER BY
            `updateId` ASC
        ";
    } else {
    /* defalut load data */
        $search = '';
        $sql = "SELECT
            `updateId`,
            `description`,
            `lastUpdateDate`
        FROM
            `Update`
        WHERE
            `trash` = 0
        ORDER BY
            `updateId` ASC
        ";
    }
/* prepare the query */
    $stmt = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's Pc - Manage News</title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main .search-form {
            box-sizing: border-box;
            position: sticky;
            top: 0;
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
        main .result-header {
            box-sizing: border-box;
            margin: 20px;
            margin-top: 100px;
            padding: 14px;
            border: 1px solid #fff;
            border-radius: 100px;
            text-transform: uppercase;
            background-color: rgb(25, 25, 25);
            color: #ffd700;
            text-shadow: -1px 0 #ff0000, 0 1px #ff0000, 1px 0 #ff0000, 0 -1px #ff0000;
        }
    /* search result styles */
        main .search-div {
            max-height: 300px;
            margin: auto;
            overflow: auto;
            scrollbar-width: none;
        }
        main .search-div table {
            width: 100%;
            min-width: fit-content;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
        }
        main .search-div table th, main .search-div table td {
            width: 200px;
            min-width: fit-content;
        }
        main .search-div table th {
            padding: 14px;
            font-size: large;
            text-transform: uppercase;
            background-color: #fff;
            color: #000000;
        }
        main .search-div table td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px dotted #808080;
        }
        main .search-div table td img {
            width: 100%;
            border-radius: 4px;
        }
        main .search-div table td a {
            display: block;
            padding: 8px;
            text-decoration: none;
            color: #fff;
        }
    /* information style */
        main .search-div .custom-note {
            margin: 4px auto;
            padding: 8px;
            background-color: #808080;
            color: #fff;
        }
    /* back btn style */
        main .bottom-div {
            margin: auto;
            overflow: auto;
        }
        main .bottom-div button {
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
        <h3 class="result-header">Manage News 🛠️</h3>
        <div class="search-div">
        <!-- filter items -->
            <form action="/admin/manage-news/" method="get" autocomplete="off" class="search-form">
                <input type="search" name="search" value="<?php echo $search; ?>" placeholder="Type here anything uploaded news related for filter records.. 🔍" maxlength="100">
            </form>
            <table>
                <?php
                /* display informations */
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note'>$value</p>";
                    }
                ?>
                <tr>
                    <th>Description</th>
                    <th>Upload Date</th>
                    <th></th>
                </tr>
                <?php
                /* display load data */
                    if ($stmt == true) {
                        while ($row = mysqli_fetch_assoc($stmt)) {
                            echo "<tr>";
                            echo "<td><a href='/admin/manage-news/?search=$row[description]'>$row[description]</a></td>";
                            echo "<td><a href='/admin/manage-news/?search=$row[lastUpdateDate]'>$row[lastUpdateDate]</a></td>";
                            echo "<td>";
                            ?><a href="/admin/manage-news/?updateId=<?php echo $row['updateId']; ?>&delete=yes&search=<?php echo $search; ?>"
                                onclick="return confirm('PERMANENTLY DELETE 🚮\n\n\tAre you sure you want to permanently delete the selected news?')"
                                style="text-transform: uppercase; font-weight: bold; color: #008000;">Delete 🗑️</a><?php
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </table>
        </div>
        <div class="bottom-div">
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