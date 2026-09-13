<?php
    session_start();
    require_once("../include/server/index.php");
    require_once("../function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_GET['cart']) && $_GET['cart'] != '') {
        addCart($conn, $_GET['cart']);
    }
    if (!isset($_GET['result']) || (isset($_GET['result']) && $_GET['result'] == '')) {
        header("location: /");
        exit();
    } else {
        $search = trim($_GET['result']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's Pc - Search Product</title>
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
        main .search-text {
            box-sizing: border-box;
            width: max-content;
            margin: 20px;
            padding: 8px;
            border-radius: 0 100px 100px 0;
            background-color: rgb(25, 25, 25);
            color: #fff;
        }
    /* search result styles */
        main .category-div {
            box-sizing: border-box;
            width: 100%;
            margin: auto;
            padding: 20px;
            overflow: hidden;
            text-align: center;
        }
        main .category-div div {
            box-sizing: content-box;
            text-align: justify;
        }
        main .category-div .show-item {
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
        main .category-div .show-item:hover {
            transform: scale(102%);
        }
        main .category-div .show-item a {
            text-decoration: none;
            display: inline-block;
            color: #000000;
        }
        main .category-div .show-item p {
            display: inline-block;
            margin: 4px auto;
            padding: 8px;
            border-radius: 4px;
            font-weight: bold;
            color: #fff;
        }
        main .category-div .show-item #show-discount, main .category-div .show-item #out-stock {
            background-color: #ff0000;
        }
        main .category-div .show-item #show-stock {
            background: linear-gradient(to right, #0000ff, royalblue);
        }
        main .category-div .show-item .item-image {
            width: 100%;
            max-width: 100%;
            height: 250px;
            max-height: 250px;
            overflow: hidden;
            background-color: #ddd;
        }
        main .category-div .show-item .item-image img {
            width: 100%;
            min-height: 250px;
            object-fit: cover;
        }
        main .category-div .show-item .item-content {
            width: 250px;
            max-width: 250px;
            height: 150px;
            max-height: 150px;
            overflow: hidden;
        }
        main .category-div .show-item .item-price p {
            padding: 0 8px;
            font-size: 15px;
            color: #808080;
        }
        main .category-div .show-item .item-price h2 {
            padding: 0 8px 8px 8px;
            color: #ff0000;
        }
        main .category-div .show-item .cart-item, main .category-div .show-item .cart-item a {
            min-width: 100%;
            max-width: 100%;
        }
        main .category-div .show-item .cart-item {
            overflow: hidden;
            text-align: center;
            border-radius: 4px;
        }
        main .category-div .show-item .cart-item a {
            padding: 4px;
            font-size: 18px;
            text-transform: uppercase;
            vertical-align: middle;
            font-weight: bold;
            background-color: #909090;
            color: #000000;
        }
        main .category-div .show-item .cart-item a img {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #fff;
            vertical-align: middle;
            background-color: #000000;
        }
        main .category-div .show-item .cart-item a:hover {
            background-color: #000000;
            color: #fff;
        }
        main .category-div .show-item .cart-item a:hover img {
            border: 1px solid #000000;
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
        <?php
            if (strlen($search) > 0) {
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
                    `image`
                FROM
                    `Product`
                WHERE
                    `trash` = 0 AND (
                    `productName` LIKE '%$secSearch%' OR
                    `category` LIKE '%$secSearch%' OR
                    `brand` LIKE '%$secSearch%' OR
                    `model` LIKE '%$secSearch%' OR
                    `price` LIKE '%$secSearch%' OR
                    `discount` LIKE '%$secSearch%' OR
                    `warranty` LIKE '%$secSearch%' OR
                    `usedType` LIKE '%$secSearch%' OR
                    `specification` LIKE '%$secSearch%' OR
                    `description` LIKE '%$secSearch%')
                ORDER BY
                    `brand` ASC,
                    `price` ASC,
                    `productId` DESC";
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true) {
                    $numRows = mysqli_num_rows($stmt);
                    $records = 0;
                }
            } else {
                $numRows = 0;
            }
        ?>
        <h3 class="result-header">Search Results ▼</h3>
        <p class="search-text">Related "<?php echo $search; ?>" (<?php echo $numRows; ?> results found) ✔️</p>
        <div class="category-div">
            <?php
                $count = 0;
                while ($row = mysqli_fetch_assoc($stmt)) {
                    $count++;
                    ?>
                    <div class="show-item" id="<?php echo $count; ?>">
                        <a href="/product/?index=<?php echo $row['productId'] ."&result=" .$search; ?>"> <!-- item-click -->
                            <?php
                                if ($row['discount'] > 0.00) {
                                    ?><p id="show-discount"><?php echo (int)((100 * ($row['price'] - $row['discount'])) / $row['price']); ?>% OFF</p><?php # display item discount
                                }
                            ?>
                            <div class="item-image">
                                <?php
                                    if ($row['image'] != null) {
                                        foreach (explode(", ", $row['image']) as $key => $value) {
                                            ?><img src="data:image; base64, <?php echo  $value; ?>" alt=""><?php # display first image of item
                                            break;
                                        }
                                    } else {
                                        ?><img src="" alt="item-image"><?php # no item image
                                    }
                                ?>
                            </div>
                            <?php
                                if ($row['quantityInStock'] > 0) {
                                    ?><p id="show-stock"><?php echo array($row['usedType'] /* store first data */, "In Stock" /* store second data */)[rand(0, 1)]; ?></p><?php # display brand new and in stock randomly
                                } else {
                                    ?><p id="out-stock">Out of Stock</p><?php # display out of stock
                                }
                            ?>
                            <div class="item-content">
                                <?php
                                    $impDetails = array();
                                    $impDetails[] = $row['productName']; # store item name
                                    if ($row['brand'] != "") {
                                        $impDetails[] = $row['brand']; # store brand name
                                    }
                                    if ($row['model'] != "") {
                                        $impDetails[] = $row['model']; # store model name
                                    }
                                    if ($row['warranty'] != "No warrant") {
                                        if (strtolower(substr($row['warranty'], -8)) == "warranty") {
                                            $impDetails[] = $row['warranty']; # store warrant period
                                        } else {
                                            $impDetails[] = $row['warranty'] ." Warranty"; # store warrant period
                                        }
                                    }
                                    if ($row['specification'] != null) {
                                        foreach (explode(", ", $row['specification']) as $key => $value) {
                                            $impDetails[] = $value; # store item specification
                                        }
                                    }
                                    if ($row['description']!= null) {
                                        foreach (explode(", ", $row['description']) as $key => $value) {
                                            $impDetails[] = $value; # store item description
                                        }
                                    }
                                ?>
                                <h3><?php echo implode(" | ", $impDetails); ?></h3> <!-- display item content -->
                            </div>
                            <div class="item-price">
                                <?php
                                    if ($row['discount'] == 0.0) {
                                        $price = number_format($row['price'], 2);
                                        ?><h2><?php echo $price; ?>LKR</h2><?php # display normal price
                                    } else {
                                        ?><p><s><?php echo $row['price']; ?> LKR</s></p><?php
                                        $price = number_format($row['discount'], 2);
                                        ?><h2><?php echo $price; ?> LKR</h2><?php # display discount price
                                    }
                                ?>
                            </div>
                        </a>
                        <div class="cart-item">
                            <?php
                                if (!empty($_REQUEST)) {
                                    $passValue = array();
                                    foreach ($_REQUEST as $key => $value) {
                                        if ($key != "cart") {
                                            $passValue[$key] = $value;
                                        }
                                    }
                                    $getValues = (!empty($passValue) ? '&' : '') .http_build_query($passValue);
                                } else {
                                    $getValues = '';
                                }
                                ?><a href="/search/?cart=<?php echo $row['productId'] .$getValues .'#' .$count; ?>">Add To Cart <img src="/image/cart.png" alt=""></a><?php # display add to cart option
                            ?>
                        </div>
                    </div>
                    <?php
                }
            ?>
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