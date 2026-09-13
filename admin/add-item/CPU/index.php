<?php
    require_once("../../../include/server/index.php"); 
    require_once("../../../function/create-primary-key/index.php");
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/'); # redirect to back page
        exit();
    }
    if (!(isset($_REQUEST['category']) && (strtoupper($_REQUEST['category']) == "CPU" || strtoupper($_REQUEST['category']) == "PROCESSOR"))) {
        header('Location: /admin/add-item/'); # redirect to back page
        exit();
    }
    $info = array();
/* set variables */
    $selProcessorModel = '';
    $txtProcessorModel = '';
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
        $selProcessorModel = $_POST['selProcessorModel'];
        $txtProcessorModel = trim($_POST['txtProcessorModel']);
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
        if ($selProcessorModel == "none" && strlen($txtProcessorModel) < 1) {
            $info[] = "Processor model field is required. ⁉️"; # model field not fill error
        }
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
            if ($txtProcessorModel > 0) {
                $selProcessorModel = "none"; # reset capacity list
                $processorModel = mysqli_real_escape_string($conn, $txtProcessorModel);
            } else {
                $processorModel = mysqli_real_escape_string($conn, $selProcessorModel);
            }
        /* join specification */
            $specification = array($processorModel);
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
            <h3 class="title-header">Add New Products + <font style="color: #fff;">(
                    <?php echo $productName ."/ " .$category ."/ " .$brand ."/ " .$model ."/ " .$price ."/ " .$discount ."/ " .$quantity ."/ " .$warranty ."/ " .$usedType; ?>
                )</font></h3>
            <form action="/admin/add-item/CPU/" method="post" enctype="multipart/form-data" autocomplete="off">
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
            <!-- processor model -->
                <h3 class="sub-header">Processor Model *</h3>
                <p class="custom-note">The processor model names you have previously added are listed here,
                    and if the processor model you are adding appears in the list, you can select it. Alternatively, you can enter your new processor model in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the processor model,
                    it will be considered the processor model of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selProcessorModel">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Model: Intel Core i3" <?php echo $selProcessorModel == "Model: Intel Core i3" ? "selected" : '' ; ?>>Intel Core i3</option>
                        <option value="Model: Intel Core i5" <?php echo $selProcessorModel == "Model: Intel Core i5" ? "selected" : '' ; ?>>Intel Core i5</option>
                        <option value="Model: Intel Core i7" <?php echo $selProcessorModel == "Model: Intel Core i7" ? "selected" : '' ; ?>>Intel Core i7</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE (`category` = 'Processor' OR `category` = 'CPU') AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Model: Intel Core i3", "Model: Intel Core i5", "Model: Intel Core i7");
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
                                                ?><option value="<?php echo $value; ?>" <?php echo $selProcessorModel == $value ? "selected" : '' ; ?>>
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
                <!-- enter new capacity -->
                    <input type="text" name="txtProcessorModel" placeholder="Enter new processor model (Ex: Model: Intel Core i9)" value="<?php echo $txtProcessorModel; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- description -->
                <h3 class="sub-header">Description (Optional)</h3>
                <textarea name="rtxtDescription" placeholder="Enter description (Ex: Base Speed: 2.60GHz, etc..)" maxlength="1500"><?php echo $rtxtDescription; ?></textarea>
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