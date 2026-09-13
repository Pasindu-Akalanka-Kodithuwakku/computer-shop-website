<?php
    require_once("../../include/server/index.php"); 
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/'); # redirect to back page
        exit();
    }
    $info = array();
/* set variables */
    $selProductName = '';
    $txtProductName = '';
    $selCategory = '';
    $txtCategory = '';
    $selBrand = '';
    $txtBrand = '';
    $selModel = '';
    $txtModel = '';
    $txtPrice = '';
    $txtDiscount = '';
    $numQuantity = '';
    $selWarranty = '';
    $txtWarranty = '';
    $selUsedType = '';
/* set previous page passes values */
    if (isset($_GET['productName'])) {
        $selProductName = $_GET['productName'];
        $txtProductName = $_GET['productName'];
        $selCategory = $_GET['category'];
        $txtCategory = $_GET['category'];
        $selBrand = $_GET['brand'];
        $txtBrand = $_GET['brand'];
        $selModel = $_GET['model'];
        $txtModel = $_GET['model'];
        $txtPrice = $_GET['price'];
        $txtDiscount = $_GET['discount'];
        $numQuantity = $_GET['quantityInStock'];
        $selWarranty = $_GET['warranty'];
        $txtWarranty = $_GET['warranty'];
        $selUsedType = $_GET['usedType'];
    }
/* click submit button */
    if (!empty($_POST)) {
        $selProductName = $_POST['selProductName'];
        $txtProductName = trim($_POST['txtProductName']);
        $selCategory = $_POST['selCategory'];
        $txtCategory = trim($_POST['txtCategory']);
        $selBrand = $_POST['selBrand'];
        $txtBrand = trim($_POST['txtBrand']);
        $selModel = $_POST['selModel'];
        $txtModel = trim($_POST['txtModel']);
        $txtPrice = trim($_POST['txtPrice']);
        $txtDiscount = trim($_POST['txtDiscount']);
        $numQuantity = $_POST['numQuantity'];
        $selWarranty = $_POST['selWarranty'];
        $txtWarranty = $_POST['txtWarranty'];
        $selUsedType = $_POST['selUsedType'];
    /* check missed fields or errors */
        if ($selProductName == "none" && strlen($txtProductName) < 1) {
            $info[] = "Product name field is required. ⁉️"; # product name field not fill error
        }
        if ($selCategory == "none" && strlen($txtCategory) < 1) {
            $info[] = "Category field is required. ⁉️"; # category field not fill error
        }
        if (!($txtPrice > 0 && $txtPrice < 100000000)) {
            $info[] = "The price you entered is invalid. ❌"; # invalid price error
        }
        if ($txtDiscount != null) {
            if (!($txtDiscount >= 0 && $txtDiscount < 100000000)) {
                $info[] = "The discount you entered is invalid. ❌"; # invalid discount error
            }
            if ($txtDiscount > $txtPrice) {
                $info[] = "The discount you entered cannot compare with price. ❌"; # invalid discount error
            }
        }
        if ($selWarranty == "none" && strlen($txtWarranty) < 1) {
            $info[] = "Warranty period field is required. ⁉️"; # warranty period field not fill error
        }
        if ($selUsedType == "none") {
            $info[] = "Used type field is required. ⁉️"; # used type field not fill error
        }
    /* no errors */
        if (empty($info)) {
            if ($txtProductName > 0) {
                $selProductName = "none"; # reset product names list
                $productName = mysqli_real_escape_string($conn, $txtProductName);
            } else {
                $productName = mysqli_real_escape_string($conn, $selProductName);
            }
            if ($txtCategory > 0) {
                $selCategory = "none"; # reset category names list
                $category = mysqli_real_escape_string($conn, $txtCategory);
            } else {
                $category = mysqli_real_escape_string($conn, $selCategory);
            }
            if ($txtBrand > 0) {
                $selBrand = "none"; # reset brand names list
                $brand = mysqli_real_escape_string($conn, $txtBrand);
            } else {
                $brand = mysqli_real_escape_string($conn, $selBrand);
            }
            if ($txtModel > 0) {
                $selModel = "none"; # reset model names list
                $model = mysqli_real_escape_string($conn, $txtModel);
            } else {
                $model = mysqli_real_escape_string($conn, $selModel);
            }
            if ($txtWarranty > 0) {
                $selWarranty = "none"; # reset warranty list
                $warranty = mysqli_real_escape_string($conn, $txtWarranty);
            } else {
                $warranty = mysqli_real_escape_string($conn, $selWarranty);
            }
            $price = mysqli_real_escape_string($conn, $txtPrice); # secure price
            $discount = mysqli_real_escape_string($conn, $txtDiscount); # secure discount
            $quantity = mysqli_real_escape_string($conn, $numQuantity); # secure quantity
            $usedType = mysqli_real_escape_string($conn, $selUsedType); # secure used type
        /* passes values array to next page */
            $info["productName"] = $productName;
            $info["category"] = $category;
            $info["brand"] = $brand;
            $info["model"] = $model;
            $info["price"] = $price;
            $info["discount"] = $discount;
            $info["quantityInStock"] = $quantity;
            $info["warranty"] = $warranty;
            $info["usedType"] = $usedType;
            $basicDetails = http_build_query($info); # all data saved in array
        /* progress to next page */
            switch (strtoupper($category)) {
                case "LAPTOP":
                    header("Location: /admin/add-item/Laptop/?$basicDetails");
                    exit();
                    break;
                case "DESKTOP":
                    header("Location: /admin/add-item/Desktop/?$basicDetails");
                    exit();
                    break;
                case "MONITOR":
                    header("Location: /admin/add-item/Monitor/?$basicDetails");
                    exit();
                    break;
                case "MOTHERBOARD":
                    header("Location: /admin/add-item/Motherboard/?$basicDetails");
                    exit();
                    break;
                case "RAM":
                    header("Location: /admin/add-item/RAM/?$basicDetails");
                    exit();
                    break;
                case "VGA":
                    header("Location: /admin/add-item/VGA/?$basicDetails");
                    exit();
                    break;
                case "HDD":
                case "SSD":
                    header("Location: /admin/add-item/Drive/?$basicDetails");
                    exit();
                    break;
                case "PROCESSOR":
                case "CPU":
                    header("Location: /admin/add-item/CPU/?$basicDetails");
                    exit();
                    break;
                case "POWER SUPPLY":
                    header("Location: /admin/add-item/PowerSupply/?$basicDetails");
                    exit();
                    break;
                case "DESKTOP CASE":
                case "CASE":
                    header("Location: /admin/add-item/DesktopCase/?$basicDetails");
                    exit();
                    break;
                default:
                    header("Location: /admin/add-item/General/?$basicDetails");
                    exit();
                    break;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Add New Products</title>
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
        main .box-layout .sub-header {
            box-sizing: border-box;
            margin: auto;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 8px;
            border: 1px solid #fff;
            border-radius: 100px;
            text-transform: capitalize;
            background-color: rgb(25, 25, 25);
            color: #fff;
            text-shadow: -1px 0 #ff0000, 0 1px #ff0000, 1px 0 #ff0000, 0 -1px #ff0000;
        }
        main .box-layout form {
            width: 100%;
            margin: auto;
            padding: 20px;
            overflow: auto;
        }
        main .box-layout form, main .box-layout form input, main .box-layout form button {
            box-sizing: border-box;
        }
        main .box-layout form fieldset {
            margin-bottom: 20px;
            padding: 14px;
            border-color: #808080;
        }
        main .box-layout form fieldset p {
            margin: 4px auto;
            padding: 8px;
            font-size: large;
            font-weight: bold;
            font-style: italic;
            text-align: center;
            color: #808080;
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
        main .box-layout form select {
            width: 100%;
            height: 40px;
            margin: 4px auto;
            padding: 8px;
            border-radius: 4px;
            border-style: solid;
            border-color: #808080;
        }
        main .box-layout form .custom-note, main .box-layout form button {
            margin: 4px auto;
            padding: 8px;
        }
        main .box-layout form button {
            margin-left: 8px;
            color: #000000;
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
    <!-- add new items -->
        <div class="box-layout">
            <h3 class="title-header">Add New Products +</h3>
            <form action="/admin/add-item/" method="post" enctype="multipart/form-data" autocomplete="off">
                <?php
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note' style='color: #808080;'>📪 $value</p>";
                    }
                ?>
            <!-- product name -->
                <h3 class="sub-header">Product Name *</h3>
                <p class="custom-note">The product names you have previously added are listed here,
                    and if the product name you are adding appears in the list, you can select it. Alternatively, you can enter your new product name in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the product name,
                    it will be considered the product name of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selProductName">
                        <option value="none">-- Select a previously entered name --</option>
                        <?php
                            $sql = "SELECT DISTINCT `productName` FROM `Product` WHERE `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    ?><option value="<?php echo $row['productName']; ?>" <?php echo $selProductName == $row['productName'] ? "selected" : '' ; ?>><?php echo $row['productName']; ?></option><?php
                                }
                            }
                        ?>
                    </select>
                    <p>Or</p>
                <!-- enter new product name -->
                    <input type="text" name="txtProductName" placeholder="Enter new product name (Ex: ExpertBook)" value="<?php echo $txtProductName; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- category -->
                <h3 class="sub-header">Category *</h3>
                <p class="custom-note">The category names you have previously added are listed here,
                    and if the category name you are adding appears in the list, you can select it. Alternatively, you can enter your new category name in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the category name,
                    it will be considered the category name of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selCategory">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Laptop" <?php echo $selCategory == "Laptop" ? "selected" : '' ; ?>>Laptop</option>
                        <option value="Desktop" <?php echo $selCategory == "Desktop" ? "selected" : '' ; ?>>Desktop Computer</option>
                        <option value="Monitor" <?php echo $selCategory == "Monitor" ? "selected" : '' ; ?>>Monitor</option>
                        <option value="RAM" <?php echo $selCategory == "RAM" ? "selected" : '' ; ?>>RAM</option>
                        <option value="Motherboard" <?php echo $selCategory == "Motherboard" ? "selected" : '' ; ?>>Motherboard</option>
                        <option value="VGA" <?php echo $selCategory == "VGA" ? "selected" : '' ; ?>>VGA (Graphic Card)</option>
                        <option value="HDD" <?php echo $selCategory == "HDD" ? "selected" : '' ; ?>>HDD</option>
                        <option value="SSD" <?php echo $selCategory == "SSD" ? "selected" : '' ; ?>>SSD</option>
                        <option value="Processor" <?php echo $selCategory == "Processor" ? "selected" : '' ; ?>>Processor (CPU)</option>
                        <option value="Power Supply" <?php echo $selCategory == "Power Supply" ? "selected" : '' ; ?>>Power Supply</option>
                        <option value="Desktop Case" <?php echo $selCategory == "Desktop Case" ? "selected" : '' ; ?>>Desktop Case</option>
                        <?php
                            $sql = "SELECT DISTINCT
                                `category`
                            FROM
                                `Product`
                            WHERE
                                (`category` NOT LIKE 'Laptop' AND
                                `category` NOT LIKE 'Desktop' AND
                                `category` NOT LIKE 'Monitor' AND
                                `category` NOT LIKE 'RAM' AND
                                `category` NOT LIKE 'Motherboard' AND
                                `category` NOT LIKE 'VGA' AND
                                `category` NOT LIKE 'HDD' AND
                                `category` NOT LIKE 'SSD' AND
                                `category` NOT LIKE 'Processor' AND
                                `category` NOT LIKE 'Power Supply' AND
                                `category` NOT LIKE 'Desktop Case') AND
                                `trash` = 0
                            ";
                            $stmt = mysqli_query($conn, $sql);
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    ?><option value="<?php echo $row['category']; ?>" <?php echo $selCategory == $row['category'] ? "selected" : '' ; ?>><?php echo $row['category']; ?></option><?php
                                }
                            }
                        ?>
                    </select>
                    <p>Or</p>
                <!-- enter new category name -->
                    <input type="text" name="txtCategory" placeholder="Enter new category name (Ex: Motherboard)" value="<?php echo $txtCategory; ?>" maxlength="25">
                </fieldset>
                <p class="custom-note">Please enter 1 to 25 characters</p>
            <!-- brand -->
                <h3 class="sub-header">Brand (Optional)</h3>
                <p class="custom-note">The brand names you have previously added are listed here,
                    and if the brand name you are adding appears in the list, you can select it. Alternatively, you can enter your new brand name in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the brand name,
                    it will be considered the brand name of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selBrand">
                        <option value="">-- Select a previously entered name --</option>
                        <?php
                            $sql = "SELECT DISTINCT `brand` FROM `Product` WHERE `brand` != '' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    ?><option value="<?php echo $row['brand']; ?>" <?php echo $selBrand == $row['brand'] ? "selected" : '' ; ?>><?php echo $row['brand']; ?></option><?php
                                }
                            }
                        ?>
                    </select>
                    <p>Or</p>
                <!-- enter new brand name -->
                    <input type="text" name="txtBrand" placeholder="Enter new brand name (Ex: Asus)" value="<?php echo $txtBrand; ?>" maxlength="25">
                </fieldset>
                <p class="custom-note">Please enter 1 to 25 characters</p>
            <!-- model -->
                <h3 class="sub-header">Model (Optional)</h3>
                <p class="custom-note">The model names you have previously added are listed here,
                    and if the model name you are adding appears in the list, you can select it. Alternatively, you can enter your new model name in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the model name,
                    it will be considered the model name of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selModel">
                        <option value="">-- Select a previously entered name --</option>
                        <?php
                            $sql = "SELECT DISTINCT `model` FROM `Product` WHERE `model` != '' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    ?><option value="<?php echo $row['model']; ?>" <?php echo $selModel == $row['model'] ? "selected" : '' ; ?>><?php echo $row['model']; ?></option><?php
                                }
                            }
                        ?>
                    </select>
                    <p>Or</p>
                <!-- enter new model name -->
                    <input type="text" name="txtModel" placeholder="Enter new model name (Ex: L1500CDA)" value="<?php echo $txtModel; ?>" maxlength="25">
                </fieldset>
                <p class="custom-note">Please enter 1 to 25 characters</p>
            <!-- price -->
                <h3 class="sub-header">Price *</h3>
                <input type="text" name="txtPrice" placeholder="Enter price of product (Ex: 160000.00)" value="<?php echo $txtPrice; ?>" maxlength="11" required>
                <p class="custom-note">Please enter 1 to 8 digets</p>
            <!-- discount -->
                <h3 class="sub-header">Discount Price (Optional)</h3>
                <input type="text" name="txtDiscount" placeholder="Enter discount price of product (Ex: 150000.00)" value="<?php echo $txtDiscount; ?>" maxlength="11">
                <p class="custom-note">Please enter 0 to 8 digets</p>
                <p class="custom-note">Note: The amount to be entered for the above field is the remaining amount after deducting the discount amount.</p>
            <!-- quantity in stock -->
                <h3 class="sub-header">Quantity in Stock (Optional)</h3>
                <input type="number" name="numQuantity" placeholder="Enter quantity in stock (Ex: 05)" value="<?php echo $numQuantity; ?>" min="0" max="50">
                <p class="custom-note">Please enter 1 to 2 digets</p>
            <!-- warranty period -->
                <h3 class="sub-header">Warranty Period *</h3>
                <p class="custom-note">The warranty periods you have previously added are listed here,
                    and if the warranty period you are adding appears in the list, you can select it. Alternatively, you can enter your new warranty period in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the warranty period,
                    it will be considered the warranty period of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selWarranty">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="No warrant" <?php echo $selWarranty == "No warrant" ? "selected" : '' ; ?>>No warranty</option>
                        <?php
                            $sql = "SELECT DISTINCT `warranty` FROM `Product` WHERE `warranty` != 'None' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    ?><option value="<?php echo $row['warranty']; ?>" <?php echo $selWarranty == $row['warranty'] ? "selected" : '' ; ?>><?php echo $row['warranty']; ?></option><?php
                                }
                            }
                        ?>
                    </select>
                    <p>Or</p>
                <!-- enter new warranty period -->
                    <input type="text" name="txtWarranty" placeholder="Enter new warranty period (Ex: 1 Year)" value="<?php echo $txtWarranty; ?>" maxlength="8">
                </fieldset>
                <p class="custom-note">Please enter 1 to 8 characters</p>
            <!-- Used type -->
                <h3 class="sub-header">Used Type *</h3>
                <select name="selUsedType">
                    <option value="none">-- Not selected --</option>
                    <option value="Brand New" <?php echo ($selUsedType == "Brand New") ? "selected" : '' ; ?>>Brand New</option>
                    <option value="Used" <?php echo ($selUsedType == "Used") ? "selected" : '' ; ?>>Used</option>
                </select>
                <p class="custom-note">Please select used type of product</p>
            <!-- done & cancel btn -->
                <a href="/admin/dashboard/"><button type="button">Cancel</button></a>
                <button type="submit">Next</button>
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
            <h4>&copy; 2025 Dilla's PC, Inc. All rights reserved.</h4>
        </div>
    </footer>
</body>
</html>