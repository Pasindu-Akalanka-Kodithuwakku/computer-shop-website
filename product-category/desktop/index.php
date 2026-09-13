<?php
    session_start();
    require_once("../../include/server/index.php");
    require_once("../../function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_GET['cart']) && $_GET['cart'] != '') {
        addCart($conn, $_GET['cart']);
    }
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
        `category` = 'Desktop' AND
        `trash` = 0
    ";
    if (!empty($_REQUEST)) {
        $sql .= "AND ";
        foreach ($_REQUEST as $key => $value) {
            for ($i=0; $i < is_array($value) ? count($_REQUEST[$key]) : 0; $i++) {
                if ($i == 0) {
                /* start bracket */
                    if ($key == "quantityInStock") {
                        if ($value[$i] == "inStock") {
                            $sql .= "(`" .$key ."` > " .(0) ." OR ";
                        } else if ($value[$i] == "outStock") {
                            $sql .= "(`" .$key ."` = " .(0) ." OR ";
                        }
                    } else if ($key == "specification") {
                        $sql .= "(`" .$key ."` LIKE '%" .$value[$i] ."%' OR ";
                    } else if ($key == "discount") {
                        $firstPrice = '';
                        for ($x=0; $x < strlen($value[$i]); $x++) {
                            if ($value[$i][$x] != 'L') {
                                $firstPrice = $firstPrice .$value[$i][$x];
                            } else {
                                break;
                            }
                        }
                        $firstPrice = trim($firstPrice);
                        if (count($_REQUEST[$key]) == 1) {
                        /* one checkbox checked */
                            $lastPrice = '';
                            for ($x=(strlen($value[$i]) - 1); $x >= 0; $x--) {
                                $break = false;
                                switch ($value[$i][$x]) {
                                    case 'L':
                                    case 'K':
                                    case 'R':
                                    case ' ':
                                        break;
                                    case '-':
                                        $break = true;
                                        break;
                                    default:
                                        $lastPrice = $lastPrice .$value[$i][$x];
                                        break;
                                }
                                if ($break == true) {
                                    break;
                                }
                            }
                            $lastPrice = trim(strrev($lastPrice));
                            $sql .= "(`" .$key ."` >= " .$firstPrice ." AND ";
                            $sql .= '`' .$key ."` <= " .$lastPrice ." OR ";
                        } else {
                        /* multi checkbox checked */
                            $sql .= "(`" .$key ."` >= " .$firstPrice ." AND ";
                        }
                    } else {
                        $sql .= "(`" .$key ."` = '" .$value[$i] ."' OR ";
                    }
                } else {
                /* not first word */
                    if ($key == "quantityInStock") {
                        if ($value[$i] == "inStock") {
                            $sql .= '`' .$key ."` > " .(0) ." OR ";
                        } else if ($value[$i] == "outStock") {
                            $sql .= '`' .$key ."` = " .(0) ." OR ";
                        }
                    } else if ($key == "specification") {
                        $sql .= '`' .$key ."` LIKE '%" .$value[$i] ."%' OR ";
                    } else if ($key == "discount") {
                        if ($i == count($_REQUEST[$key]) - 1) {
                            $lastPrice = '';
                            for ($x=(strlen($value[$i]) - 1); $x >= 0; $x--) {
                                $break = false;
                                switch ($value[$i][$x]) {
                                    case 'L':
                                    case 'K':
                                    case 'R':
                                    case ' ':
                                        break;
                                    case '-':
                                        $break = true;
                                        break;
                                    default:
                                        $lastPrice = $lastPrice .$value[$i][$x];
                                        break;
                                }
                                if ($break == true) {
                                    break;
                                }
                            }
                            $lastPrice = trim(strrev($lastPrice));
                            $sql .= '`' .$key ."` <= " .$lastPrice ." OR ";
                        }
                    } else {
                        $sql .= '`' .$key ."` = '" .$value[$i] ."' OR ";
                    }
                }
            }
            if (is_array($value)) {
                $sql = substr($sql, 0, -4) .") AND ";
            }
        }
        $sql = substr($sql, 0, -5);
    }
    try {
        $stmt = mysqli_query($conn, $sql);
    } catch (\Throwable $th) {
        header('Location: /product-category/desktop/');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Desktop Stock</title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main form {
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
        main .category-header {
            min-width: 35%;
            max-width: fit-content;
            margin: 4px;
            margin-top: 100px;
            padding: 14px;
            box-sizing: border-box;
            border: 1px solid #ff0000;
            border-radius: 0 100px 100px 0;
            text-align: center;
            vertical-align: top;
            background: linear-gradient(to right, #ffa500, #ffff00);
            color: #000000;
        }
        main .category-header span {
            margin: 4px auto;
            padding: 8px;
            text-transform: uppercase;
            font-size: 18px;
            font-weight: bold;
            vertical-align: middle;
            text-shadow: -1px 0 #fff, 0 1px #fff, 1px 0 #fff, 0 -1px #fff;
        }
        main .category-header img {
            display: inline-block;
            max-height: 35px;
            vertical-align: middle;
        }
        main .category-div, main .filter-div {
            width: 100%;
            box-sizing: border-box;
            margin: auto;
            overflow: hidden;
        }
        main .category-div {
            padding: 20px;
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
    /* filter panel style */
        main .filter-div {
            display: none;
            position: absolute;
            padding: 20px;
            transition: 0.5s;
            background-color: #909090;
            color: #000000;
        }
        main .filter-div form {
            max-width: fit-content;
            padding: 8px;
            background-color: #fff;
        }
        main .filter-div form fieldset {
            margin: 4px auto;
            padding: 8px;
        }
        main .filter-div form fieldset details summary {
            margin: 4px auto;
            font-size: 14px;
            font-weight: bold;
        }
        main .filter-div form fieldset details input[type="checkbox"] {
            margin: 4px 0 4px 14px;
        }
        main .filter-div form fieldset details label {
            font-size: 14px;
        }
        main .filter-div form button {
            width: 100%;
            margin: 4px auto;
            padding: 8px;
            border: 1px solid #fff;
            border-radius: 4px;
            box-shadow: 2px 2px 2px #000000;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            background: linear-gradient(180deg, orange, red);
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
            <input type="search" name="result" placeholder="Enter anything computer products related.. 🔍" maxlength="100" required autofocus>
        </form>
        <div class="category-header" onclick="control_sidebar()"><span>&#9776; Desktop </span><img src="/image/desktop.png" alt=""></div>
        <div class="filter-div" id="filter_sidebar">
            <form action="/product-category/desktop/" method="post">
                <fieldset>
                    <details <?php echo isset($_REQUEST['productName']) ? "open" : '' ; ?>>
                        <summary>Filter by name</summary>
                        <?php
                        /* filter by product name */
                            $subSql = "SELECT DISTINCT
                                `productName`
                            FROM
                                `Product`
                            WHERE
                                `category` = 'Desktop' AND
                                `trash` = 0
                            ";
                            $subStmt = mysqli_query($conn, $subSql);
                            if ($subStmt == true) {
                                if (isset($_REQUEST['productName'])) {
                                    for ($i=0; $i < count($_REQUEST['productName']); $i++) {
                                        $productName[] = $_REQUEST['productName'][$i];
                                    }
                                } else {
                                    for ($i=0; $i < mysqli_num_rows($subStmt); $i++) {
                                        $productName[] = '';
                                    }
                                }
                                $foundData = '';
                                while ($subRow = mysqli_fetch_assoc($subStmt)) {
                                    foreach ($productName as $key => $value) {
                                        if ($value == $subRow['productName']) {
                                            $foundData = $value;
                                            break;
                                        }
                                    }
                                    if ($foundData != '') {
                                        echo "<input type='checkbox' name='productName[]' id='$subRow[productName]' value='$subRow[productName]' " .(($foundData == $subRow['productName']) ? "checked" : '') ."> ";
                                    } else {
                                        echo "<input type='checkbox' name='productName[]' id='$subRow[productName]' value='$subRow[productName]'> ";
                                    }
                                    echo "<label for='$subRow[productName]'>$subRow[productName]</label><br>";
                                }
                            }
                        ?>
                    </details>
                </fieldset>
                <fieldset>
                    <details <?php echo isset($_REQUEST['brand']) ? "open" : '' ; ?>>
                        <summary>Filter by brand</summary>
                        <?php
                        /* filter by brand */
                            $subSql = "SELECT DISTINCT
                                `brand`
                            FROM
                                `Product`
                            WHERE
                                `category` = 'Desktop' AND
                                `brand` != '' AND
                                `trash` = 0
                            ";
                            $subStmt = mysqli_query($conn, $subSql);
                            if ($subStmt == true) {
                                if (isset($_REQUEST['brand'])) {
                                    for ($i=0; $i < count($_REQUEST['brand']); $i++) {
                                        $brand[] = $_REQUEST['brand'][$i];
                                    }
                                } else {
                                    for ($i=0; $i < mysqli_num_rows($subStmt); $i++) {
                                        $brand[] = '';
                                    }
                                }
                                $foundData = '';
                                while ($subRow = mysqli_fetch_assoc($subStmt)) {
                                    foreach ($brand as $key => $value) {
                                        if ($value == $subRow['brand']) {
                                            $foundData = $value;
                                            break;
                                        }
                                    }
                                    if ($foundData != '') {
                                        echo "<input type='checkbox' name='brand[]' id='$subRow[brand]' value='$subRow[brand]' " .(($foundData == $subRow['brand']) ? "checked" : '') ."> ";
                                    } else {
                                        echo "<input type='checkbox' name='brand[]' id='$subRow[brand]' value='$subRow[brand]'> ";
                                    }
                                    echo "<label for='$subRow[brand]'>$subRow[brand]</label><br>";
                                }
                            }
                        ?>
                    </details>
                </fieldset>
                <fieldset>
                    <details <?php echo isset($_REQUEST['model']) ? "open" : '' ; ?>>
                        <summary>Filter by model</summary>
                        <?php
                        /* filter by model */
                            $subSql = "SELECT DISTINCT
                                `model`
                            FROM
                                `Product`
                            WHERE
                                `category` = 'Desktop' AND
                                `model` != '' AND
                                `trash` = 0
                            ";
                            $subStmt = mysqli_query($conn, $subSql);
                            if ($subStmt == true) {
                                if (isset($_REQUEST['model'])) {
                                    for ($i=0; $i < count($_REQUEST['model']); $i++) {
                                        $model[] = $_REQUEST['model'][$i];
                                    }
                                } else {
                                    for ($i=0; $i < mysqli_num_rows($subStmt); $i++) {
                                        $model[] = '';
                                    }
                                }
                                $foundData = '';
                                while ($subRow = mysqli_fetch_assoc($subStmt)) {
                                    foreach ($model as $key => $value) {
                                        if ($value == $subRow['model']) {
                                            $foundData = $value;
                                            break;
                                        }
                                    }
                                    if ($foundData != '') {
                                        echo "<input type='checkbox' name='model[]' id='$subRow[model]' value='$subRow[model]' " .(($foundData == $subRow['model']) ? "checked" : '') ."> ";
                                    } else {
                                        echo "<input type='checkbox' name='model[]' id='$subRow[model]' value='$subRow[model]'> ";
                                    }
                                    echo "<label for='$subRow[model]'>$subRow[model]</label><br>";
                                }
                            }
                        ?>
                    </details>
                </fieldset>
                <fieldset>
                    <details <?php echo isset($_REQUEST['warranty']) ? "open" : '' ; ?>>
                        <summary>Filter by warranty</summary>
                        <?php
                        /* filter by warranty */
                            $subSql = "SELECT DISTINCT
                                `warranty`
                            FROM
                                `Product`
                            WHERE
                                `category` = 'Desktop' AND
                                `trash` = 0
                            ORDER BY
                                `warranty` DESC
                            ";
                            $subStmt = mysqli_query($conn, $subSql);
                            if ($subStmt == true) {
                                if (isset($_REQUEST['warranty'])) {
                                    for ($i=0; $i < count($_REQUEST['warranty']); $i++) {
                                        $warranty[] = $_REQUEST['warranty'][$i];
                                    }
                                } else {
                                    for ($i=0; $i < mysqli_num_rows($subStmt); $i++) {
                                        $warranty[] = '';
                                    }
                                }
                                $foundData = '';
                                while ($subRow = mysqli_fetch_assoc($subStmt)) {
                                    foreach ($warranty as $key => $value) {
                                        if ($value == $subRow['warranty']) {
                                            $foundData = $value;
                                            break;
                                        }
                                    }
                                    if ($foundData != '') {
                                        echo "<input type='checkbox' name='warranty[]' id='$subRow[warranty]' value='$subRow[warranty]' " .(($foundData == $subRow['warranty']) ? "checked" : '') ."> ";
                                    } else {
                                        echo "<input type='checkbox' name='warranty[]' id='$subRow[warranty]' value='$subRow[warranty]'> ";
                                    }
                                    echo "<label for='$subRow[warranty]'>$subRow[warranty]</label><br>";
                                }
                            }
                        ?>
                    </details>
                </fieldset>
                <fieldset>
                    <details <?php echo isset($_REQUEST['usedType']) ? "open" : '' ; ?>>
                        <summary>Filter by use type</summary>
                        <?php
                        /* filter by used type */
                            $subSql = "SELECT DISTINCT
                                `usedType`
                            FROM
                                `Product`
                            WHERE
                                `category` = 'Desktop' AND
                                `trash` = 0
                            ";
                            $subStmt = mysqli_query($conn, $subSql);
                            if ($subStmt == true) {
                                if (isset($_REQUEST['usedType'])) {
                                    for ($i=0; $i < count($_REQUEST['usedType']); $i++) {
                                        $usedType[] = $_REQUEST['usedType'][$i];
                                    }
                                } else {
                                    for ($i=0; $i < mysqli_num_rows($subStmt); $i++) {
                                        $usedType[] = '';
                                    }
                                }
                                $foundData = '';
                                while ($subRow = mysqli_fetch_assoc($subStmt)) {
                                    foreach ($usedType as $key => $value) {
                                        if ($value == $subRow['usedType']) {
                                            $foundData = $value;
                                            break;
                                        }
                                    }
                                    if ($foundData != '') {
                                        echo "<input type='checkbox' name='usedType[]' id='$subRow[usedType]' value='$subRow[usedType]' " .(($foundData == $subRow['usedType']) ? "checked" : '') ."> ";
                                    } else {
                                        echo "<input type='checkbox' name='usedType[]' id='$subRow[usedType]' value='$subRow[usedType]'> ";
                                    }
                                    echo "<label for='$subRow[usedType]'>$subRow[usedType]</label><br>";
                                }
                            }
                        ?>
                    </details>
                </fieldset>
                <fieldset>
                    <details <?php echo isset($_REQUEST['quantityInStock']) ? "open" : '' ; ?>>
                        <summary>Filter by availability</summary>
                        <?php
                        /* filter by availability */
                            $subSql = "SELECT
                                `quantityInStock`
                            FROM
                                `Product`
                            WHERE
                                `category` = 'Desktop' AND
                                `trash` = 0
                            ";
                            $subStmt = mysqli_query($conn, $subSql);
                            if ($subStmt == true) {
                                if (mysqli_num_rows($subStmt) > 0) {
                                /* check in stock */
                                    $subSql = "SELECT DISTINCT
                                        `quantityInStock`
                                    FROM
                                        `Product`
                                    WHERE
                                        `category` = 'Desktop' AND
                                        `quantityInStock` > 0 AND
                                        `trash` = 0
                                    LIMIT 1
                                    ";
                                    $subStmt = mysqli_query($conn, $subSql);
                                    if ($subStmt == true) {
                                        if (isset($_REQUEST['quantityInStock'])) {
                                            for ($i=0; $i < count($_REQUEST['quantityInStock']); $i++) {
                                                $quantityInStock[] = $_REQUEST['quantityInStock'][$i];
                                            }
                                        } else {
                                            for ($i=0; $i < mysqli_num_rows($subStmt); $i++) {
                                                $quantityInStock[] = '';
                                            }
                                        }
                                        $foundData = '';
                                        if (mysqli_num_rows($subStmt) == 1) {
                                            foreach ($quantityInStock as $key => $value) {
                                                if ($value == "inStock") {
                                                    $foundData = $value;
                                                    break;
                                                }
                                            }
                                            if ($foundData != '') {
                                                echo "<input type='checkbox' name='quantityInStock[]' id='inStock' value='inStock' " .(($foundData == "inStock") ? "checked" : '') ."> ";
                                            } else {
                                                echo "<input type='checkbox' name='quantityInStock[]' id='inStock' value='inStock'> ";
                                            }
                                            echo "<label for='inStock'>In stock</label><br>";
                                        }
                                    }
                                /* check out of stock */
                                    $subSql = "SELECT DISTINCT
                                        `quantityInStock`
                                    FROM
                                        `Product`
                                    WHERE
                                        `category` = 'Desktop' AND
                                        `quantityInStock` = 0 AND
                                        `trash` = 0
                                    LIMIT 1
                                    ";
                                    $subStmt = mysqli_query($conn, $subSql);
                                    if ($subStmt == true) {
                                        if (isset($_REQUEST['quantityInStock'])) {
                                            for ($i=0; $i < count($_REQUEST['quantityInStock']); $i++) {
                                                $quantityInStock[] = $_REQUEST['quantityInStock'][$i];
                                            }
                                        } else {
                                            for ($i=0; $i < mysqli_num_rows($subStmt); $i++) {
                                                $quantityInStock[] = '';
                                            }
                                        }
                                        $foundData = '';
                                        if (mysqli_num_rows($subStmt) == 1) {
                                            foreach ($quantityInStock as $key => $value) {
                                                if ($value == "outStock") {
                                                    $foundData = $value;
                                                    break;
                                                }
                                            }
                                            if ($foundData != '') {
                                                echo "<input type='checkbox' name='quantityInStock[]' id='outStock' value='outStock' " .(($foundData == "outStock") ? "checked" : '') ."> ";
                                            } else {
                                                echo "<input type='checkbox' name='quantityInStock[]' id='outStock' value='outStock'> ";
                                            }
                                            echo "<label for='outStock'>Out stock</label><br>";
                                        }
                                    }
                                }
                            }
                        ?>
                    </details>
                </fieldset>
                <fieldset>
                    <details <?php echo isset($_REQUEST['specification']) ? "open" : '' ; ?>>
                        <summary>Filter by specification</summary>
                        <?php
                        /* filter by specification */
                            $subSql = "SELECT
                                `specification`
                            FROM
                                `Product`
                            WHERE
                                `category` = 'Desktop' AND
                                `trash` = 0
                            ";
                            $subStmt = mysqli_query($conn, $subSql);
                            if ($subStmt == true) {
                                $savedData = array();
                                if (isset($_REQUEST['specification'])) {
                                    for ($i=0; $i < count($_REQUEST['specification']); $i++) {
                                        $specification[] = $_REQUEST['specification'][$i];
                                    }
                                } else {
                                    for ($i=0; $i < mysqli_num_rows($subStmt); $i++) {
                                        $specification[] = '';
                                    }
                                }
                                $foundData2 = '';
                                while ($subRow = mysqli_fetch_assoc($subStmt)) {
                                    if ($subRow['specification'] != null) {
                                        foreach (explode(", ", $subRow['specification']) as $key => $value) {
                                            $foundData = 0;
                                            for ($i=0; $i < count($savedData); $i++) {
                                                if (strtolower($value) == strtolower($savedData[$i])) {
                                                    $foundData = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundData == 0) {
                                                foreach ($specification as $key2 => $value2) {
                                                    if ($value2 == $value) {
                                                        $foundData2 = $value2;
                                                        break;
                                                    }
                                                }
                                                if ($foundData2 != '') {
                                                    echo "<input type='checkbox' name='specification[]' id='$value' value='$value' " .(($foundData2 == $value) ? "checked" : '') ."> ";
                                                } else {
                                                    echo "<input type='checkbox' name='specification[]' id='$value' value='$value'> ";
                                                }
                                                echo "<label for='$value'>$value</label><br>";
                                            }
                                        }
                                    }
                                }
                            }
                        ?>
                    </details>
                </fieldset>
                <fieldset>
                    <details <?php echo isset($_REQUEST['discount']) ? "open" : '' ; ?>>
                        <summary>Filter by price</summary>
                        <?php
                        /* filter by price */
                            $subSql = "SELECT
                                MAX(`discount`) AS `maxPrice`
                            FROM
                                `Product`
                            WHERE
                                `category` = 'Desktop' AND
                                `trash` = 0
                            ";
                            $subStmt = mysqli_query($conn, $subSql);
                            if ($subStmt == true) {
                                if (isset($_REQUEST['discount'])) {
                                    for ($i=0; $i < count($_REQUEST['discount']); $i++) {
                                        $discount[] = $_REQUEST['discount'][$i];
                                    }
                                } else {
                                    for ($i=0; $i < mysqli_num_rows($subStmt); $i++) {
                                        $discount[] = '';
                                    }
                                }
                                $foundData = '';
                                $subRow = mysqli_fetch_assoc($subStmt);
                                for ($i=0; $i < 5; $i++) {
                                    $label = ceil(($subRow['maxPrice'] / 5) * $i) ." LKR - " .ceil(($subRow['maxPrice'] / 5) * ($i + 1)) ." LKR";
                                    foreach ($discount as $key => $value) {
                                        if ($value == $label) {
                                            $foundData = $value;
                                            break;
                                        }
                                    }
                                    if ($foundData != '') {
                                        echo "<input type='checkbox' name='discount[]' id='$label' value='$label' " .(($foundData == $label) ? "checked" : '') ."> ";
                                    } else {
                                        echo "<input type='checkbox' name='discount[]' id='$label' value='$label'> ";
                                    }
                                    echo "<label for='$label'>$label</label><br>";
                                }
                            }
                        ?>
                    </details>
                </fieldset>
                <button type="submit">Filter Products</button>
            </form>
        </div>
        <div class="category-div">
            <?php
                $count = 0;
                while ($row = mysqli_fetch_assoc($stmt)) {
                    $count++;
                    ?>
                    <div class="show-item" id="<?php echo $count; ?>">
                        <a href="/product/?index=<?php echo $row['productId']; ?>"> <!-- item-click -->
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
                                ?><a href="/product-category/desktop/?cart=<?php echo $row['productId'] .$getValues .'#' .$count; ?>">Add To Cart <img src="/image/cart.png" alt=""></a><?php # display add to cart option
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
    <script>
        function control_sidebar() {
            if (document.getElementById("filter_sidebar").style.display == "block") {
                document.getElementById("filter_sidebar").style.display = "none";
            } else {
                document.getElementById("filter_sidebar").style.display = "block";
            }
        }
    </script>
</body>
</html>