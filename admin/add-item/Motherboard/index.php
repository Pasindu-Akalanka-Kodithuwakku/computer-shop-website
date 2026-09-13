<?php
    require_once("../../../include/server/index.php"); 
    require_once("../../../function/create-primary-key/index.php");
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/'); # redirect to back page
        exit();
    }
    if (!(isset($_REQUEST['category']) && strtoupper($_REQUEST['category']) == "MOTHERBOARD")) {
        header('Location: /admin/add-item/'); # redirect to back page
        exit();
    }
    $info = array();
/* set variables */
    $selGeneration = '';
    $txtGeneration = '';
    $selSlot = '';
    $txtSlot = '';
    $rtxtDescription = '';
/* passes values */
    $productName = $_REQUEST['productName'];
    $category = $_REQUEST['category'];
    $brand = $_REQUEST['brand'];
    $model = $_REQUEST['model'];
    $price = $_REQUEST['price'];
    $discount = $_REQUEST['discount'];
    $quantity = $_REQUEST['quantityInStock'];
    $warranty = $_REQUEST['warranty'];
    $usedType = $_REQUEST['usedType'];
/* click submit button */
    if (!empty($_POST)) {
    /* set new values */
        $selGeneration = $_POST['selGeneration'];
        $txtGeneration = trim($_POST['txtGeneration']);
        $selSlot = $_POST['selSlot'];
        $txtSlot = trim($_POST['txtSlot']);
        $rtxtDescription = trim($_POST['rtxtDescription']);
    /* passes values */
        $productName = $_POST['productName'];
        $category = $_POST['category'];
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $price = $_POST['price'];
        $discount = $_POST['discount'];
        $quantity = $_POST['quantityInStock'];
        $warranty = $_POST['warranty'];
        $usedType = $_POST['usedType'];
    /* check errors */
        if ($selGeneration == "none" && strlen($txtGeneration) < 1) { $info[] = "Motherboard generation field is required. ⁉️"; /* generation field not fill error */ }
        if ($selSlot == "none" && strlen($txtSlot) < 1) { $info[] = "Motherboard RAM slot field is required. ⁉️"; /* RAM slot field not fill error */ }
        if ($_FILES['docImage']['tmp_name'][0] != null) {
        /* check upload files type */
            for ($i=0; $i < count($_FILES['docImage']['type']); $i++) {
                if (substr($_FILES['docImage']['type'][$i], 0, 5) != "image") {
                    $info[] = "Please select only images that fall into the image category. 🖼️";
                    break;
                }
            }
        /* count upload files size */
            $imageSize = 0;
            for ($i=0; $i < count($_FILES['docImage']['size']); $i++) {
                $imageSize = $imageSize + $_FILES['docImage']['size'][$i]; # add one by one image size
            }
        /* check upload images size */
            if ((($imageSize / 1024) / 1024) > 20) {
                $info[] = "The size of the images you uploaded is more than 20MB. ⚡";
            }
        }
    /* no errors */
        if (empty($info)) {
            if ($txtGeneration > 0) { $selGeneration = "none"; $generation = mysqli_real_escape_string($conn, $txtGeneration); } else { $generation = mysqli_real_escape_string($conn, $selGeneration); }
            if ($txtSlot > 0) { $selSlot = "none"; $slot = mysqli_real_escape_string($conn, $txtSlot); } else { $slot = mysqli_real_escape_string($conn, $selSlot); }
        /* join specification */
            $specification = array($generation, $slot);
            $specification = implode(", ", $specification); # joined specification
        /* customize description */
            $reMakeDescription = array();
            $countFirstWord = 0;
            for ($i=0; $i < count(explode(',', $rtxtDescription)); $i++) {
                if (trim(explode(',', $rtxtDescription)[$i]) != '') {
                    if ($countFirstWord != 0) {
                        $reMakeDescription[] = ' ' .trim(explode(',', $rtxtDescription)[$i]);
                    } else {
                        $reMakeDescription[] = trim(explode(',', $rtxtDescription)[$i]); # remove first word space
                    }
                    $countFirstWord = $countFirstWord + 1;
                }
            }
            $rtxtDescription = implode(',', $reMakeDescription); # remake description
            $description = mysqli_real_escape_string($conn, $rtxtDescription); # secure description
        /* image encryption */
            $image = array();
            if ($_FILES['docImage']['tmp_name'][0] != null) {
                for ($i=0; $i < count($_FILES['docImage']['tmp_name']); $i++) {
                    $image[] = base64_encode(file_get_contents($_FILES['docImage']['tmp_name'][$i]));
                }
            }
            $image = mysqli_real_escape_string($conn, implode(", ", $image)); # secure images
        /* insert a new product record */
            $id = createPrimaryKey($conn, "Product", "productId", "PD"); # generate primary key
            $sql = "INSERT INTO `Product`(
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
            ) VALUES (
                '$id',
                '$productName',
                '$category',
                '$brand',
                '$model',
                '$price',
                '$discount',
                '$quantity',
                '$warranty',
                '$usedType',
                '$specification',
                '$description',
                '$image'
            )";
            $stmt = mysqli_query($conn, $sql);
            if ($stmt == true) {
                header('Location: /admin/dashboard/?info=Successfully added.');
                exit();
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
            width: 80%;
            margin: auto;
            margin-top: 100px;
            overflow: auto;
        }
        main .box-layout .title-header {
            margin: auto;
            padding: 14px;
            border: 1px solid #fff;
            border-radius: 4px 4px 0 0;
            background-color: #fff;
            color: #000000;
        }
        main .box-layout .sub-header {
            margin: auto;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 8px;
            border: 1px solid #fff;
            border-radius: 4px 4px 0 0;
            background-color: #fff;
            color: #000000;
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
        main .box-layout form input, main .box-layout form textarea {
            width: 100%;
            height: 40px;
            margin: 4px auto;
            padding: 8px;
            outline: none;
            border-radius: 4px;
            border-style: solid;
            border-color: #808080;
        }
        main .box-layout form textarea {
            box-sizing: border-box;
            height: 100px;
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
            <h3 class="title-header">Add New Products + <font style="color: #008000;">(
                    <?php echo $productName ."/ " .$category ."/ " .$brand ."/ " .$model ."/ " .$price ."/ " .$discount ."/ " .$quantity ."/ " .$warranty ."/ " .$usedType; ?>
                )</font></h3>
            <form action="/admin/add-item/Motherboard/" method="post" enctype="multipart/form-data" autocomplete="off">
                <?php
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note' style='color: #808080;'>📪 $value</p>";
                    }
                    $info = array();
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
                ?>
            <!-- product name -->
                <input type="hidden" name="productName" value="<?php echo $productName; ?>">
            <!-- category -->
                <input type="hidden" name="category" value="<?php echo $category; ?>">
            <!-- brand -->
                <input type="hidden" name="brand" value="<?php echo $brand; ?>">
            <!-- model -->
                <input type="hidden" name="model" value="<?php echo $model; ?>">
            <!-- price -->
                <input type="hidden" name="price" value="<?php echo $price; ?>">
            <!-- discount -->
                <input type="hidden" name="discount" value="<?php echo $discount; ?>">
            <!-- quantity in stock -->
                <input type="hidden" name="quantityInStock" value="<?php echo $quantity; ?>">
            <!-- warranty -->
                <input type="hidden" name="warranty" value="<?php echo $warranty; ?>">
            <!-- used type -->
                <input type="hidden" name="usedType" value="<?php echo $usedType; ?>">
            <!-- motherboard generation -->
                <h3 class="sub-header">Generation *</h3>
                <p class="custom-note">The motherboard generations you have previously added are listed here,
                    and if the motherboard generation you are adding appears in the list, you can select it. Alternatively, you can enter your new motherboard generation in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the motherboard generation,
                    it will be considered the motherboard generation of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selGeneration">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Generation: 7th Gen" <?php echo $selGeneration == "Generation: 7th Gen" ? "selected" : '' ; ?>>7th Generation</option>
                        <option value="Generation: 8th Gen" <?php echo $selGeneration == "Generation: 8th Gen" ? "selected" : '' ; ?>>8th Generation</option>
                        <option value="Generation: 10th Gen" <?php echo $selGeneration == "Generation: 10th Gen" ? "selected" : '' ; ?>>10th Generation</option>
                        <option value="Generation: 11th Gen" <?php echo $selGeneration == "Generation: 11th Gen" ? "selected" : '' ; ?>>11th Generation</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Motherboard' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Generation: 7th Gen", "Generation: 8th Gen", "Generation: 10th Gen", "Generation: 11th Gen");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 0) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selGeneration == $value ? "selected" : '' ; ?>>
                                                    <?php echo $value; ?>
                                                </option><?php
                                            }
                                            $foundRecord = 0;
                                            break;
                                        }
                                    }
                                }
                            }
                        ?>
                    </select>
                    <p>Or</p>
                <!-- enter new motherboard generation -->
                    <input type="text" name="txtGeneration" placeholder="Enter new motherboard generation (Ex: Generation: 14th Gen)" value="<?php echo $txtGeneration; ?>" maxlength="30">
                </fieldset>
                <p class="custom-note">Please enter 1 to 30 characters</p>
            <!-- RAM slot -->
                <h3 class="sub-header">RAM Slot *</h3>
                <p class="custom-note">The motherboard RAM slots you have previously added are listed here,
                    and if the motherboard RAM slot you are adding appears in the list, you can select it. Alternatively, you can enter your new motherboard RAM slot in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the motherboard RAM slot,
                    it will be considered the motherboard RAM slot of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selSlot">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="RAM Slots: 1 RAM Slot" <?php echo $selSlot == "RAM Slots: 1 RAM Slot" ? "selected" : '' ; ?>>1 RAM Slot</option>
                        <option value="RAM Slots: 2 RAM Slots" <?php echo $selSlot == "RAM Slots: 2 RAM Slots" ? "selected" : '' ; ?>>2 RAM Slots</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Motherboard' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("RAM Slots: 1 RAM Slot", "RAM Slots: 2 RAM Slots");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 1) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selSlot == $value ? "selected" : '' ; ?>>
                                                    <?php echo $value; ?>
                                                </option><?php
                                            }
                                            $foundRecord = 0;
                                            break;
                                        }
                                    }
                                }
                            }
                        ?>
                    </select>
                    <p>Or</p>
                <!-- enter new RAM slot -->
                    <input type="text" name="txtSlot" placeholder="Enter new motherboard RAM slot (Ex: RAM Slots: 4 RAM Slots)" value="<?php echo $txtSlot; ?>" maxlength="30">
                </fieldset>
                <p class="custom-note">Please enter 1 to 30 characters</p>
            <!-- description -->
                <h3 class="sub-header">Description (Optional)</h3>
                <textarea name="rtxtDescription" placeholder="Enter description (Ex: Data Speed: 1Gbps, etc..)" maxlength="1500"><?php echo $rtxtDescription; ?></textarea>
                <p class="custom-note">Please enter 1 to 1500 characters</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: When presenting facts, use commas
                    <font style="color: #008000; font-weight: bold;">(,)</font> to separate each fact.</p>
            <!-- images -->
                <h3 class="sub-header">Product Images (Optional)</h3>
                <input type="file" name="docImage[]" multiple accept="image/*">
                <p class="custom-note" style="margin-bottom: 20px;">Note: Please ensure that the total size of the
                    images being uploaded is <font style="color: #008000; font-weight: bold;">less than 20MB</font></p>
            <!-- done & cancel btn -->
                <a href="/admin/dashboard/"><button type="button">Cancel</button></a>
                <a href="/admin/add-item/?<?php echo $basicDetails; ?>"><button type="button">Back</button></a>
                <button type="submit" onclick="return confirm('Are you sure you want to add a new product?')">Done</button>
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