<?php
    require_once("../../include/server/index.php");
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/');
        exit();
    }
    $info = array();
/* check if click delete button */
    if (isset($_GET['productId']) && isset($_GET['delete']) && $_GET['delete'] == "yes") {
        $sql = "DELETE FROM `Product` WHERE `productId` = '$_GET[productId]'";
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == true && mysqli_affected_rows($conn) == 1) {
            $info[] = "The record was successfully deleted.";
        }
    }
    if (isset($_GET['productId']) && isset($_GET['updateQty'])) {
        $sql = "UPDATE `Product` SET `quantityInStock` = '$_GET[updateQty]' WHERE `productId` = '$_GET[productId]'";
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == true && mysqli_affected_rows($conn) == 1) {
            $info[] = "The stock was successfully updated.";
        }
    }
/* check if click search button */
    if (isset($_GET['search'])) {
    /* filter data */
        $search = trim($_GET['search']);
        $secSearch = mysqli_real_escape_string($conn, $search);
        $sql = "SELECT
            `productId`,
            `productName`,
            `category`,
            `brand`,
            `model`,
            `price`,
            `discount`,
            `quantityInStock`,
            `warranty`,
            `usedType`,
            `specification`,
            `description`,
            `image`,
            `uploadDate`,
            `lastUpdateDate`
        FROM
            `Product`
        WHERE
            (`productName` LIKE '%$secSearch%' OR
            `category` LIKE '%$secSearch%' OR
            `brand` LIKE '%$secSearch%' OR
            `model` LIKE '%$secSearch%' OR
            `price` LIKE '%$secSearch%' OR
            `discount` LIKE '%$secSearch%' OR
            `quantityInStock` LIKE '%$secSearch%' OR
            `warranty` LIKE '%$secSearch%' OR
            `usedType` LIKE '%$secSearch%' OR
            `specification` LIKE '%$secSearch%' OR
            `description` LIKE '%$secSearch%' OR
            `uploadDate` LIKE '%$secSearch%' OR
            `lastUpdateDate` LIKE '%$secSearch%') AND
            `trash` = 0
        ORDER BY
            `productId` DESC,
            `productName` ASC,
            `quantityInStock` ASC,
            `category` ASC
        ";
    } else {
    /* defalut load data */
        $search = '';
        $sql = "SELECT
            `productId`,
            `productName`,
            `category`,
            `brand`,
            `model`,
            `price`,
            `discount`,
            `quantityInStock`,
            `warranty`,
            `usedType`,
            `specification`,
            `description`,
            `image`,
            `uploadDate`,
            `lastUpdateDate`
        FROM
            `Product`
        WHERE
            `trash` = 0
        ORDER BY
            `productId` DESC,
            `productName` ASC,
            `quantityInStock` ASC,
            `category` ASC
        ";
    }
/* prepare the query */
    $stmt = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Manage Items</title>
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
            width: 80%;
            margin: auto;
            margin-top: 100px;
            padding: 14px;
            border: 1px solid #fff;
            box-sizing: border-box;
            border-radius: 4px 4px 0 0;
            background-color: #fff;
            color: #000000;
        }
    /* search result styles */
        main .search-div {
            width: 80%;
            max-height: 300px;
            margin: auto;
            overflow: auto;
        }
        main .search-div table {
            min-width: max-content;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
        }
        main .search-div table th, main .search-div table td {
            width: 200px;
            min-width: min-content;
        }
        main .search-div table th {
            padding: 14px;
            font-size: large;
            text-transform: uppercase;
            background-color: #fff;
            color: #000000;
        }
        main .search-div table td {
            text-align: center;
            border-bottom: 1px solid #808080;
        }
        main .search-div table td a {
            display: block;
            padding: 8px;
            text-decoration: none;
            color: #fff;
        }
        main .search-div table td input[type="number"] {
            padding: 8px;
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
            width: 80%;
            margin: auto;
            overflow: auto;
        }
        main .bottom-div button {
            margin: auto;
            margin-top: 20px;
            margin-left: 8px;
            padding: 8px;
            cursor: pointer;
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
        <h3 class="result-header">Manage Items 🛠️</h3>
        <div class="search-div">
        <!-- filter items -->
            <form action="/admin/manage-item/" method="get" autocomplete="off" class="search-form">
                <input type="search" name="search" value="<?php echo $search; ?>" placeholder="Type here anything computer products related for filter records.. 🔍" maxlength="100">
            </form>
            <table>
                <?php
                /* display informations */
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note'>$value</p>";
                    }
                ?>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Quantity in Stock</th>
                    <th>Warranty</th>
                    <th>Used Type</th>
                    <th>Specification</th>
                    <th>Description</th>
                    <th>Are there images of the product?</th>
                    <th>Views</th>
                    <th>Upload Date</th>
                    <th>Last Edited Date</th>
                    <th></th>
                </tr>
                <?php
                /* display load data */
                    if ($stmt == true) {
                        while ($row = mysqli_fetch_assoc($stmt)) {
                            echo "<tr>";
                            echo "<td><a href='/admin/manage-item/?search=$row[productName]'>$row[productName]</a></td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[category]'>$row[category]</a></td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[brand]'>$row[brand]</a></td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[model]'>$row[model]</a></td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[price]'>$row[price]</a></td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[discount]'>$row[discount]</a></td>";
                            ?>
                            <td>
                                <form action="./" method="get">
                                    <input type="number" name="updateQty" min="0" max="50" placeholder="Qty" value="<?php echo $row['quantityInStock']; ?>">
                                    <input type="hidden" name="productId" value="<?php echo $row['productId']; ?>"> <!-- passed value -->
                                    <input type="hidden" name="search" value="<?php echo $search; ?>"> <!-- passed value -->
                                </form>
                            </td>
                            <?php
                            echo "<td><a href='/admin/manage-item/?search=$row[warranty]'>$row[warranty]</a></td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[usedType]'>$row[usedType]</a></td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[specification]'>$row[specification]</a></td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[description]'>$row[description]</a></td>";
                            if (explode(", ", $row['image'])[0] != null) {
                                echo "<td>" .count(explode(", ", $row['image'])) ." product image(s)</td>";
                            } else {
                                echo "<td>No product image(s)</td>";
                            }
                        /* process of product views */
                            echo "<td>";
                            $sql2 = "SELECT
                                COUNT(*) AS `views`
                            FROM
                                `ViewItem`
                            WHERE
                                `productId` = '$row[productId]' AND
                                `trash` = 0
                            ";
                            $stmt2 = mysqli_query($conn, $sql2);
                            $row2 = mysqli_fetch_assoc($stmt2);
                            echo $row2['views'];
                            echo "</td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[uploadDate]'>$row[uploadDate]</a></td>";
                            echo "<td><a href='/admin/manage-item/?search=$row[lastUpdateDate]'>$row[lastUpdateDate]</a></td>";
                            echo "<td>";
                            echo "<a href='/admin/edit-item/?productId=$row[productId]' style='text-transform: uppercase; font-weight: bold; color: #9acd32;'>Edit ☑️</a>";
                            ?><a href="/admin/manage-item/?productId=<?php echo $row['productId']; ?>&delete=yes&search=<?php echo $search; ?>"
                                onclick="return confirm('PERMANENTLY DELETE 🚮\n\n\tAre you sure you want to permanently delete the selected item?')"
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