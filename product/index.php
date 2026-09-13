<?php
    session_start();
    require_once("../include/server/index.php");
    require_once("../function/product-category/index.php");
    require_once("../function/create-primary-key/index.php");
    require_once("../function/item-chat/index.php");
    require_once("../function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_GET['cart']) && $_GET['cart'] != '') {
        addCart($conn, $_GET['cart']);
    }
    if (!isset($_GET['index']) || (isset($_GET['index']) && $_GET['index'] == '')) {
        header("location: /");
        exit();
    }
    $productId = $_GET['index'];
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
        `image`
    FROM
        `Product`
    WHERE
        `productId` = '$productId' AND
        `trash` = 0
    LIMIT 1";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt == true) {
        if (mysqli_num_rows($stmt) == 1) {
            $row = mysqli_fetch_assoc($stmt);
            $productId = $row['productId'];
            $productName = $row['productName'];
            $category = $row['category'];
            $brand = $row['brand'];
            $model = $row['model'];
            $defaultPrice = $row['price'];
            $discount = $row['discount'];
            $quantityInStock = $row['quantityInStock'];
            $warranty = $row['warranty'];
            $usedType = $row['usedType'];
            $specification = $row['specification'];
            $description = $row['description'];
            $image = $row['image'];
        /* check product views */
            $sql = "SELECT
                `viewId`,
                `deviceName`
            FROM
                `ViewItem`
            WHERE
                `productId` = '$productId' AND
                `deviceName` = '$_SERVER[HTTP_USER_AGENT]' AND
                `trash` = 0
            LIMIT 1";
            $stmt = mysqli_query($conn, $sql);
            if (mysqli_num_rows($stmt) == 1) {
                $row = mysqli_fetch_assoc($stmt);
                $sql = "UPDATE
                    `ViewItem`
                SET
                    `viewDate` = NOW()
                WHERE
                    `viewId` = '$row[viewId]'";
                $stmt = mysqli_query($conn, $sql);
            } else {
                $id = createPrimaryKey($conn, "ViewItem", "viewId", "VW");
                $sql = "INSERT INTO `ViewItem`(
                    `viewId`,
                    `productId`,
                    `deviceName`
                ) VALUES (
                    '$id',
                    '$productId',
                    '$_SERVER[HTTP_USER_AGENT]'
                )";
                $stmt = mysqli_query($conn, $sql);
            }
            $sql = "SELECT
                COUNT(*) AS `views`
            FROM
                `ViewItem`
            WHERE
                `productId` = '$productId' AND
                `trash` = 0";
            $stmt = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($stmt);
            $views = $row['views'];
        } else {
            header("location: /");
            exit();
        }
    }
    if (isset($_GET['result'])) {
        $search = $_GET['result'];
    } else {
        $search = '';
    }
/* chat process */
    switch (true) {
        case isset($_SESSION['memberId']) && $_SESSION['memberId'] != '':
            $sender = $_SESSION['memberId'];
            break;
        case isset($_SESSION['adminId']) && $_SESSION['adminId'] != '':
            $sender = $_SESSION['adminId'];
            break;
        default:
            $sender = '';
            break;
    }
    if (isset($_REQUEST['replyId']) && $_REQUEST['replyId'] != '') {
        $replyId = $_REQUEST['replyId'];
    } else {
        $replyId = '';
    }
/* sent a message */
    if (isset($_POST['message']) && trim($_POST['message']) != '') {
        $message = mysqli_real_escape_string($conn, base64_encode(trim($_POST['message'])));
        $id = createPrimaryKey($conn, "ItemChat", "chatId", "CH");
    /* check if reply chat */
        if ($replyId != '') {
            $sql = "INSERT INTO `ItemChat`(
                `chatId`,
                `description`,
                `sender`,
                `reply`,
                `itemId`
            ) VALUES (
                '$id',
                '$message',
                '$sender',
                '$replyId',
                '$productId'
            )";
        } else {
            $sql = "INSERT INTO `ItemChat`(
                `chatId`,
                `description`,
                `sender`,
                `itemId`
            ) VALUES (
                '$id',
                '$message',
                '$sender',
                '$productId'
            )";
        }
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == false) {
            die("Something went wrong!");
            exit();
        }
    }
/* delete message */
    if (isset($_GET['deleteId']) && $_GET['deleteId'] != '') {
        $sql = "DELETE FROM
            `ItemChat`
        WHERE
            `chatId` = '$_GET[deleteId]'
        ";
        $stmt = mysqli_query($conn, $sql);
        if (mysqli_affected_rows($conn) == 1) {
            $sql = "UPDATE
                `ItemChat`
            SET
                `reply` = ''
            WHERE
                `reply` = '$_GET[deleteId]'
            ";
            $stmt = mysqli_query($conn, $sql);
            if ($stmt == false) {
                die("Something went wrong!");
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $productName ." - " .$model; ?></title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main .search-form {
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
        main .product-title {
            box-sizing: border-box;
            margin: 20px;
            margin-top: 100px;
            padding: 14px;
            border: 1px solid #fff;
            border-radius: 100px;
            text-transform: uppercase;
            background: rgb(30, 30, 30);
            color: #fff;
            text-shadow: -1px 0 #ff0000, 0 1px #ff0000, 1px 0 #ff0000, 0 -1px #ff0000;
        }
        main .image-layout table, main .details-layout table, main .category-div table {
            min-width: max-content;
            table-layout: fixed;
            border-spacing: 20px;
        }
        main .image-layout table td {
            width: 250px;
            vertical-align: top;
            border-radius: 4px;
            overflow: hidden;
            background-color: #909090;
            color: #fff;
        }
        main .image-layout table td img {
            width: 100%;
            height: 250px;
            display: block;
            border-style: solid;
            border-radius: 4px;
            object-fit: cover;
        }
        main .image-layout table td img:hover {
            transform: scale(1.4) rotate(10deg);
            transition: all .5s;
        }
        main .details-layout table {
            width: 100%;
            min-width: fit-content;
            margin-top: 20px;
            box-sizing: border-box;
            background-color: #ddd;
            color: #000000;
        }
        main .details-layout table th, main .details-layout table td {
            width: 50%;
            padding: 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: large;
        }
        main .details-layout table td {
            color: #0000ff;
        }
        main .details-layout table td a {
            display: block;
            padding: 8px;
            text-decoration: none;
            color: #0000ff;
        }
        main .details-layout table td a:hover {
            font-weight: bold;
            background-color: #ddd;
        }
        main .details-layout .product-description {
            margin: auto;
            margin-top: 20px;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #fff;
            text-align: center;
            background-color: #fff;
            color: #000000;
        }
        main .details-layout .description-details {
            display: block;
            margin: 20px;
            padding: 8px;
            border-bottom: 1px solid #909090;
        }
        main #notice {
            margin: auto;
            margin-top: 20px;
            padding: 8px;
            font-size: large;
            text-transform: uppercase;
            color: #808080;
            text-align: center;
        }
    /* related product table style */
        main .show-category {
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
        main .category-box-layout {
            box-sizing: border-box;
            width: 100%;
            overflow: auto;
        }
        main .category-box-layout .category-div {
            width: max-content;
            padding: 20px;
            overflow: auto;
        }
        main .category-box-layout .category-div div {
            box-sizing: content-box;
        }
        main .category-box-layout .category-div .show-item {
            display: inline-block;
            width: 250px;
            min-width: 250px;
            max-width: 250px;
            overflow: hidden;
            margin: 5px;
            padding: 8px;
            border-radius: 4px;
            box-shadow: 2px 2px 5px #808080;
            background-color: #fff;
        }
        main .category-box-layout .category-div .show-item:hover {
            transform: scale(102%);
        }
        main .category-box-layout .category-div .show-item a {
            text-decoration: none;
            display: inline-block;
            color: #000000;
        }
        main .category-box-layout .category-div .show-item p {
            display: inline-block;
            margin: 4px auto;
            padding: 8px;
            border-radius: 4px;
            font-weight: bold;
            color: #fff;
        }
        main .category-box-layout .category-div .show-item #show-discount, main .category-box-layout .category-div .show-item #out-stock {
            background-color: #ff0000;
        }
        main .category-box-layout .category-div .show-item #show-stock {
            background: linear-gradient(to right, #0000ff, royalblue);
        }
        main .category-box-layout .category-div .show-item .item-image {
            width: 100%;
            max-width: 100%;
            height: 250px;
            max-height: 250px;
            overflow: hidden;
            background-color: #ddd;
        }
        main .category-box-layout .category-div .show-item .item-image img {
            width: 100%;
            min-height: 250px;
            object-fit: cover;
        }
        main .category-box-layout .category-div .show-item .item-content {
            width: 250px;
            max-width: 250px;
            height: 150px;
            max-height: 150px;
            overflow: hidden;
        }
        main .category-box-layout .category-div .show-item .item-price p {
            padding: 0 8px;
            font-size: 15px;
            color: #808080;
        }
        main .category-box-layout .category-div .show-item .item-price h2 {
            padding: 0 8px 8px 8px;
            color: #ff0000;
        }
        main .category-box-layout .category-div .show-item .cart-item, main .category-box-layout .category-div .show-item .cart-item a {
            min-width: 100%;
            max-width: 100%;
        }
        main .category-box-layout .category-div .show-item .cart-item {
            overflow: hidden;
            text-align: center;
            border-radius: 4px;
        }
        main .category-box-layout .category-div .show-item .cart-item a {
            padding: 4px;
            font-size: 18px;
            text-transform: uppercase;
            vertical-align: middle;
            font-weight: bold;
            background-color: #909090;
            color: #000000;
        }
        main .category-box-layout .category-div .show-item .cart-item a img {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #fff;
            vertical-align: middle;
            background-color: #000000;
        }
        main .category-box-layout .category-div .show-item .cart-item a:hover {
            background-color: #000000;
            color: #fff;
        }
        main .category-box-layout .category-div .show-item .cart-item a:hover img {
            border: 1px solid #000000;
        }
    /* chat style */
        main .chat-layout {
            box-sizing: border-box;
            height: 300px;
            margin: 20px;
            padding: 8px;
            overflow: auto;
            background-color: #ddd;
        }
        main .chat-layout .display-chat {
            box-sizing: border-box;
            padding: 8px;
            color: #000000;
        }
        main .chat-layout .display-chat .msg-border {
            display: block;
            box-sizing: border-box;
            min-width: max-content;
            margin: 4px auto;
            padding: 8px;
        }
        main .chat-layout .display-chat .msg-border .pro-pic {
            display: inline-block;
            width: 65px;
            height: 65px;
            border-left: 5px solid #ff0000;
        }
        main .chat-layout .display-chat .msg-border .pro-pic img {
            display: block;
            width: 100%;
            height: 65px;
            border-radius: 50%;
            object-fit: cover;
        }
        main .chat-layout .display-chat .msg-border .msg-cont {
            display: inline-block;
            vertical-align: top;
            margin-left: 8px;
            max-width: 1000px;
        }
        main .chat-layout .display-chat .msg-border .msg-cont p {
            margin: 8px auto;
        }
        main .chat-layout .display-chat .msg-border .msg-cont h5 a {
            display: none;
            margin-right: 4px;
            text-decoration: none;
            color: #0000ff;
        }
        main .chat-layout .display-chat .msg-border .msg-cont:hover h5 a {
            display: unset;
        }
        main .chat-layout .display-chat .msg-border .msg-cont h5 #del-btn:hover {
            color: #ff0000;
        }
        main .frm-chat {
            box-sizing: border-box;
            margin: 20px;
        }
        main .frm-chat textarea {
            box-sizing: border-box;
            width: 100%;
            height: 100px;
            margin: 4px auto;
            padding: 8px;
            border-radius: 4px;
        }
        main .frm-chat button {
            box-sizing: border-box;
            margin: 4px auto;
            padding: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        @keyframes blink {
            50% {
                opacity: 0;
            }
        }
        #sel-item-cart {
            display: inline;
            max-width: fit-content;
            margin-top: 0;
            color: #000000;
        }
        #sel-item-cart:hover {
            background-color: #000000;
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
            <a href="/member/message-center/"><img src="/image/log-in.png" alt=""> Log in</a>
            <a href="/cart/" id="cart-nav"><img src="/image/cart.png" alt=""><?php echo countCart(); ?></a>
        </div>
    </header>
    <main>
        <form action="/search/" method="get" autocomplete="off" class="search-form">
            <input type="search" name="result" value="<?php echo $search; ?>" placeholder="Enter anything computer products related.. 🔍" maxlength="100" required autofocus>
        </form>
    <!-- product images -->
        <h3 class="product-title"><?php echo $productName ." - " .$model; ?></h3>
        <div class="image-layout">
            <table>
                <tr>
                    <?php
                        if ($image != null) {
                            foreach (explode(", ", $image) as $key => $value) {
                                echo "<td><img src='data:image; base64, $value' alt='product image'></td>";
                            }
                        } else {
                            echo "<td><img src='' alt='product image'></td>";
                        }
                    ?>
                </tr>
            </table>
        </div>
    <!-- product detalis -->
        <div class="details-layout">
            <table>
                <tr>
                    <th>► Product Name</th>
                    <td title="Product Name">
                        <?php echo $productName; ?>
                        <?php
                            if ($usedType == "Brand New") {
                                echo "<a href='/search/?result=$usedType' title='Product Type' style='display: inline-block;
                                    margin: 4px auto; background: linear-gradient(to right, red, blue); color: #fff; animation: blink 1s infinite;'>$usedType</a>";
                            } else {
                                echo "<a href='/search/?result=$usedType' title='Product Type' style='display: inline-block;
                                    margin: 4px auto; background-color:rgb(200, 70, 70); color: #fff; border-style: dotted;'>$usedType</a>";
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>► Model</th>
                    <td title="Model"><?php echo $model; ?></td>
                </tr>
                <tr>
                    <th>► Brand</th>
                    <td title="Brand" style="padding-left: 0;"><?php echo "<a href='/search/?result=$brand'>$brand</a>"; ?></td>
                </tr>
                <tr>
                    <th>► Category</th>
                    <td title="Category" style="padding-left: 0;"><?php echo "<a href='/search/?result=$category'>$category</a>"; ?></td>
                </tr>
                <?php
                    if ($discount > 0) {
                        # having discount
                        ?>
                        <tr>
                            <th>► Selling Price</th>
                            <td title="Selling Price"><?php echo $defaultPrice ." LKR"; ?></td>
                        </tr>
                        <tr>
                            <th>► Discount</th>
                            <td title="Discount">
                                <?php echo (int) ($defaultPrice - $discount) ." LKR"; ?>
                                <?php echo "(<font style='color: #0000ff; text-shadow: -1px 0 #ff0000, 0 1px #ff0000, 1px 0 #ff0000, 0 -1px #ff0000;'>" .(int) ((100 * ($defaultPrice - $discount)) / $defaultPrice) ."% Discount</font>)"; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>► Our Price</th>
                            <td title="Our Price" style="font-size: x-large; font-weight: bold; color: #ff0000;">
                                <?php
                                    $price = number_format($discount, 2);
                                    echo "$price LKR";
                                ?>
                            </td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <th>► Price</th>
                            <td title="Our Price" style="font-size: x-large; font-weight: bold; color: #ff0000;">
                                <?php
                                    $price = number_format($defaultPrice, 2);
                                    echo "$price LKR";
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
                <tr>
                    <th>► Warranty</th>
                    <td><?php echo strtolower(substr($warranty, -8)) == "warranty" ? $warranty : $warranty ." warranty" ; ?></td>
                </tr>
                <tr>
                    <th>► Stock</th>
                    <td><?php echo ($quantityInStock > 0) ? "<font style='text-shadow: -1px 0 #ff0000, 0 1px #ff0000, 1px 0 #ff0000, 0 -1px #ff0000;'>Available in stock</font>" : "<font style='color: #ff0000;'>Sold out</font>"; ?></td>
                </tr>
                <tr><th></th><td><?php echo "<a href='/product/?cart=$productId&index=$productId' class='cart-btn' id='sel-item-cart'>Add To Cart <img src='/image/cart.png'></a>"; ?></td></tr>
            </table>
        <!-- product description -->
            <h3 class="product-description">Product Specification & Description :</h3>
            <?php
                if ($specification != null) {
                    foreach (explode(", ", $specification) as $key => $value) {
                        echo "<p class='description-details'>► $value</p>";
                    }
                }
                if ($description != null) {
                    foreach (explode(", ", $description) as $key => $value) {
                        echo "<p class='description-details'>► $value</p>";
                    }
                }
            ?>
            <p id="notice">Call us if you want to buy the product. (Hotline - +94 75 085 5492)</p>
            <h3 style="margin: 20px; color: #808080;">Views: <?php echo $views; ?></h3>
            <h3 style="margin: 4px 20px; margin-top: 14px; color: #808080;">Share on:</h3>
            <?php $pageAddress = $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI']; ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $pageAddress; ?>" target="_blank" style="margin: 0 0  0 20px; text-decoration: none; color: #fff;">Facebook</a> |
            <a href="https://api.whatsapp.com/send/?text=<?php echo $pageAddress; ?>&type=custom_url&app_absent=0" target="_blank" style="text-decoration: none; color: #fff;">WhatsApp</a>
        </div>
    <!-- chat option --->
        <?php
            $sql = "SELECT COUNT(*) AS `numRows` FROM `ItemChat` WHERE `itemId` = '$_REQUEST[index]'";
            $stmt = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($stmt);
        ?>
        <h2 style="margin: 20px; margin-top: 30px; color: #fff;"><?php echo $row['numRows']; ?> Comments</h2>
        <div class="chat-layout">
            <div class="display-chat">
                <?php
                    $sql = "SELECT
                        `chatId`,
                        `description`,
                        `sender`,
                        `sentDate`
                    FROM
                        `ItemChat`
                    WHERE
                        `trash` = 0 AND
                        `itemId` = '$_REQUEST[index]' AND
                        `reply` IS NULL
                    ORDER BY
                        `chatId` ASC
                    ";
                    $stmt = mysqli_query($conn, $sql);
                    $count = 0;
                    if ($stmt == true) {
                        while ($row = mysqli_fetch_assoc($stmt)) {
                            $count++;
                            echo "<span class='msg-border' id='$count'>";
                            echo "<span class='pro-pic'>";
                            if (substr($row['sender'], 0, 2) == "AM") {
                                $sql2 = "SELECT
                                    `adminId`,
                                    `firstName`,
                                    `lastName`,
                                    `image`
                                FROM
                                    `Admin`
                                WHERE
                                    `adminId` = '$row[sender]'
                                LIMIT 1
                                ";
                            } else {
                                $sql2 = "SELECT
                                    `memberId`,
                                    `firstName`,
                                    `lastName`,
                                    `image`
                                FROM
                                    `Member`
                                WHERE
                                    `memberId` = '$row[sender]'
                                LIMIT 1
                                ";
                            }
                            $stmt2 = mysqli_query($conn, $sql2);
                            if ($stmt2 == true) {
                            /* check is sender a member */
                                if ($row['sender'] != '') {
                                /* sender is a member */
                                    $row2 = mysqli_fetch_assoc($stmt2);
                                    if ($row2['image'] != '') {
                                        echo "<img src='data:image; base64, $row2[image]' alt=''>";
                                    } else {
                                        echo "<img src='../image/logo.jpg' alt=''>";
                                    }
                                } else {
                                /* sender is not a member */
                                    echo "<img src='../image/logo.jpg' alt=''>";
                                }
                            }
                            echo "</span>";
                            echo "<span class='msg-cont'>";
                        /* check is sender a member */
                            if ($row['sender'] != '') {
                            /* sender is a member */
                                echo "<h3>";
                                echo $row2['firstName'] .' ' .$row2['lastName'];
                                if (substr($row['sender'], 0, 2) == "AM") {
                                    echo " (<font style='color: #808080;'>Admin</font>)";
                                }
                                echo "</h3>";
                            } else {
                            /* sender is not a member */
                                echo "<h3>Anonymous</h3>";
                            }
                            echo "<p>" .base64_decode($row['description']) ."</p>";
                            echo "<h5>";
                            echo "<a href='/product/?index=$_REQUEST[index]&replyId=$row[chatId]#$count'>Reply ↩️</a>"; # set reply button
                            switch (true) {
                                case isset($_SESSION['memberId']) && $row['sender'] != '' &&  $_SESSION['memberId'] == $row2['memberId']:
                                case isset($_SESSION['adminId']):
                                    echo "<a href='/product/?index=$_REQUEST[index]&deleteId=$row[chatId]#$count' id='del-btn'>Delete 🗑️</a>"; # set delete button
                                    break;
                            }
                            echo $row['sentDate'];
                            echo "</h5>";
                            echo "</span>";
                            echo "</span>";
                            chatReply($conn, $row['chatId'], $_REQUEST['index'], 1); # call function
                        }
                    }
                ?>
            </div>
        </div>
        <form action="/product/?index=<?php echo $_REQUEST['index'] .'#' .($count + 1); ?>" method="post" class="frm-chat">
            <input type="hidden" name="replyId" value="<?php echo $replyId; ?>"> <!-- passed hidden value -->
            <textarea name="message" placeholder="Add a comment..." required></textarea>
            <button type="submit">Send Comment ✔️</button>
        </form>
    <!-- show products related to category -->
        <h3 class="show-category">Related Products ▼</h3>
        <?php displayBasicProduct($conn, $category); ?>
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