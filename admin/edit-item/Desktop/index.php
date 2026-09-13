<?php
    require_once("../../../include/server/index.php");
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/'); # redirect to back page
        exit();
    }
    if (!isset($_REQUEST['productId'])) {
        header('Location: /admin/manage-item/'); # redirect to back page
        exit();
    }
/* check is valid product id */
    $sql = "SELECT * FROM `Product` WHERE `productId` = '$_REQUEST[productId]' LIMIT 1";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt == true && mysqli_num_rows($stmt) == 0) {
        header('Location: /admin/manage-item/'); # redirect to back page
        exit();
    }
    if (!(isset($_REQUEST['category']) && strtoupper($_REQUEST['category']) == "DESKTOP")) {
        header('Location: /admin/edit-item/'); # redirect to back page
        exit();
    }
    $info = array();
    $row = mysqli_fetch_assoc($stmt); # load data
    $rowForSec = $row; # load data 2
/* set variables */
    $specification = array();
    foreach (explode(", ", $row['specification']) as $key => $value) {
        $specification[] = $value;
    }
    $selCapacity = $specification[0];
    $txtCapacity = '';
    $selMemoryType = $specification[1];
    $txtMemoryType = '';
    $selProcessorGeneration = $specification[2];
    $txtProcessorGeneration = '';
    $selProcessorModel = $specification[3];
    $txtProcessorModel = '';
    $selProcessorSeries = $specification[4];
    $txtProcessorSeries = '';
    $selHardDrive = $specification[5];
    $txtHardDrive = '';
    $selHardDriveCapacity = $specification[6];
    $txtHardDriveCapacity = '';
    $selGraphic = $specification[7];
    $txtGraphic = '';
    $selMotherboard = $specification[8];
    $txtMotherboard = '';
    $selPowerSupply = $specification[9];
    $txtPowerSupply = '';
    $selSound = $specification[10];
    $txtSound = '';
    $selCase = $specification[11];
    $txtCase = '';
    $rtxtDescription = $row['description'];
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
        $selCapacity = $_POST['selCapacity'];
        $txtCapacity = trim($_POST['txtCapacity']);
        $selMemoryType = $_POST['selMemoryType'];
        $txtMemoryType = trim($_POST['txtMemoryType']);
        $selProcessorGeneration = $_POST['selProcessorGeneration'];
        $txtProcessorGeneration = trim($_POST['txtProcessorGeneration']);
        $selProcessorModel = $_POST['selProcessorModel'];
        $txtProcessorModel = trim($_POST['txtProcessorModel']);
        $selProcessorSeries = $_POST['selProcessorSeries'];
        $txtProcessorSeries = trim($_POST['txtProcessorSeries']);
        $selHardDrive = $_POST['selHardDrive'];
        $txtHardDrive = trim($_POST['txtHardDrive']);
        $selHardDriveCapacity = $_POST['selHardDriveCapacity'];
        $txtHardDriveCapacity = trim($_POST['txtHardDriveCapacity']);
        $selGraphic = $_POST['selGraphic'];
        $txtGraphic = trim($_POST['txtGraphic']);
        $selMotherboard = $_POST['selMotherboard'];
        $txtMotherboard = trim($_POST['txtMotherboard']);
        $selPowerSupply = $_POST['selPowerSupply'];
        $txtPowerSupply = trim($_POST['txtPowerSupply']);
        $selSound = $_POST['selSound'];
        $txtSound = trim($_POST['txtSound']);
        $selCase = $_POST['selCase'];
        $txtCase = trim($_POST['txtCase']);
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
        if ($selCapacity == "none" && strlen($txtCapacity) < 1) { $info[] = "Desktop RAM capacity (size) field is required. ⁉️"; /* capacity field not fill error*/ }
        if ($selMemoryType == "none" && strlen($txtMemoryType) < 1) { $info[] = "Desktop memory type field is required. ⁉️"; /* memory type field not fill error */ }
        if ($selProcessorGeneration == "none" && strlen($txtProcessorGeneration) < 1) { $info[] = "Desktop processor generation field is required. ⁉️"; /* processor generation field not fill error */ }
        if ($selProcessorModel == "none" && strlen($txtProcessorModel) < 1) { $info[] = "Desktop processor model field is required. ⁉️"; /* processor model field not fill error */ }
        if ($selProcessorSeries == "none" && strlen($txtProcessorSeries) < 1) { $info[] = "Desktop processor series field is required. ⁉️"; /* processor series field not fill error */ }
        if ($selHardDrive == "none" && strlen($txtHardDrive) < 1) { $info[] = "Desktop hard drive field is required. ⁉️"; /* hard drive field not fill error */ }
        if ($selHardDriveCapacity == "none" && strlen($txtHardDriveCapacity) < 1) { $info[] = "Desktop hard drive capacity field is required. ⁉️"; /* hard drive capacity field not fill error */ }
        if ($selGraphic == "none" && strlen($txtGraphic) < 1) { $info[] = "Desktop graphic field is required. ⁉️"; /* graphic field not fill error */ }
        if ($selMotherboard == "none" && strlen($txtMotherboard) < 1) { $info[] = "Desktop motherboard field is required. ⁉️"; /* motherboard field not fill error */ }
        if ($selPowerSupply == "none" && strlen($txtPowerSupply) < 1) { $info[] = "Desktop power supply field is required. ⁉️"; /* power supply field not fill error */ }
        if ($selSound == "none" && strlen($txtSound) < 1) { $info[] = "Desktop sound field is required. ⁉️"; /* sound field not fill error */ }
        if ($selCase == "none" && strlen($txtCase) < 1) { $info[] = "Desktop case field is required. ⁉️"; /* case field not fill error */ }
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
            if ($txtCapacity > 0) { $selCapacity = "none"; $capacity = mysqli_real_escape_string($conn, $txtCapacity); } else { $capacity = mysqli_real_escape_string($conn, $selCapacity); }
            if ($txtMemoryType > 0) { $selMemoryType = "none"; $memoryType = mysqli_real_escape_string($conn, $txtMemoryType); } else { $memoryType = mysqli_real_escape_string($conn, $selMemoryType); }
            if ($txtProcessorGeneration > 0) { $selProcessorGeneration = "none"; $processorGeneration = mysqli_real_escape_string($conn, $txtProcessorGeneration); } else { $processorGeneration = mysqli_real_escape_string($conn, $selProcessorGeneration); }
            if ($txtProcessorModel > 0) { $selProcessorModel = "none"; $processorModel = mysqli_real_escape_string($conn, $txtProcessorModel); } else { $processorModel = mysqli_real_escape_string($conn, $selProcessorModel); }
            if ($txtProcessorSeries > 0) { $selProcessorSeries = "none"; $processorSeries = mysqli_real_escape_string($conn, $txtProcessorSeries); } else { $processorSeries = mysqli_real_escape_string($conn, $selProcessorSeries); }
            if ($txtHardDrive > 0) { $selHardDrive = "none"; $hardDrive = mysqli_real_escape_string($conn, $txtHardDrive); } else { $hardDrive = mysqli_real_escape_string($conn, $selHardDrive); }
            if ($txtHardDriveCapacity > 0) { $selHardDriveCapacity = "none"; $hardDriveCapacity = mysqli_real_escape_string($conn, $txtHardDriveCapacity); } else { $hardDriveCapacity = mysqli_real_escape_string($conn, $selHardDriveCapacity); }
            if ($txtGraphic > 0) { $selGraphic = "none"; $graphic = mysqli_real_escape_string($conn, $txtGraphic); } else { $graphic = mysqli_real_escape_string($conn, $selGraphic); }
            if ($txtMotherboard > 0) { $selMotherboard = "none"; $motherBoard = mysqli_real_escape_string($conn, $txtMotherboard); } else { $motherBoard = mysqli_real_escape_string($conn, $selMotherboard); }
            if ($txtPowerSupply > 0) { $selPowerSupply = "none"; $powerSupply = mysqli_real_escape_string($conn, $txtPowerSupply); } else { $powerSupply = mysqli_real_escape_string($conn, $selPowerSupply); }
            if ($txtSound > 0) { $selSound = "none"; $sound = mysqli_real_escape_string($conn, $txtSound); } else { $sound = mysqli_real_escape_string($conn, $selSound); }
            if ($txtCase > 0) { $selCase = "none"; $case = mysqli_real_escape_string($conn, $txtCase); } else { $case = mysqli_real_escape_string($conn, $selCase); }
        /* join specification */
            $specification = array($capacity, $memoryType, $processorGeneration, $processorModel, $processorSeries, $hardDrive, $hardDriveCapacity, $graphic, $motherBoard, $powerSupply, $sound, $case);
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
                $image = mysqli_real_escape_string($conn, implode(", ", $image)); # secure images
            } else {
            /* no uploaded new images */
                $image = mysqli_real_escape_string($conn, $row['image']); # save previous data
            }
        /* update the record */
            $sql = "UPDATE
                `Product`
            SET
                `productName` = '$productName',
                `category` = '$category',
                `brand` = '$brand',
                `model` = '$model',
                `price` = '$price',
                `discount` = '$discount',
                `quantityInStock` = '$quantity',
                `warranty` = '$warranty',
                `usedType` = '$usedType',
                `specification` = '$specification',
                `description` = '$description',
                `image` = '$image',
                `lastUpdateDate` = NOW()
            WHERE
                `productId` = '$row[productId]'
            ";
            $stmt = mysqli_query($conn, $sql);
            if (mysqli_affected_rows($conn) == 1) {
                header('Location: /admin/dashboard/?info=Successfully edited.');
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
    /* load image design */
        main .box-layout form .load-image-layout {
            margin: auto;
            overflow: auto;
        }
        main .box-layout form .load-image-layout table {
            box-sizing: border-box;
            border-collapse: collapse;
            table-layout: fixed;
        }
        main .box-layout form .load-image-layout table td {
            padding: 8px;
        }
        main .box-layout form .load-image-layout table td img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 1px solid #fff;
            border-radius: 4px;
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
            <h3 class="title-header">Edit Item 🛠️ (<font style="color: #0000ff;">
                <?php echo $productName ."/ " .$category ."/ " .$brand ."/ " .$model ."/ " .$price ."/ " .$discount ."/ " .$quantity ."/ " .$warranty ."/ " .$usedType; ?>
            </font>)</h3>
            <form action="/admin/edit-item/Desktop/" method="post" enctype="multipart/form-data" autocomplete="off">
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
                    $info["productId"] = $_REQUEST['productId'];
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
            <!-- product id -->
                <input type="hidden" name="productId" value="<?php echo $_REQUEST['productId']; ?>">
            <!-- capacity -->
                <h3 class="sub-header">Memory Capacity (RAM Size) *</h3>
                <p class="custom-note">The desktop RAM capacities you have previously added are listed here,
                    and if the desktop RAM capacity you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop RAM capacity in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop RAM capacity,
                    it will be considered the desktop RAM capacity of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selCapacity">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Capacity: 2GB" <?php echo $selCapacity == "Capacity: 2GB" ? "selected" : '' ; ?>>2GB</option>
                        <option value="Capacity: 4GB" <?php echo $selCapacity == "Capacity: 4GB" ? "selected" : '' ; ?>>4GB</option>
                        <option value="Capacity: 8GB" <?php echo $selCapacity == "Capacity: 8GB" ? "selected" : '' ; ?>>8GB</option>
                        <option value="Capacity: 32GB" <?php echo $selCapacity == "Capacity: 32GB" ? "selected" : '' ; ?>>32GB</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Capacity: 2GB", "Capacity: 4GB", "Capacity: 8GB", "Capacity: 32GB");
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
                                                ?><option value="<?php echo $value; ?>" <?php echo $selCapacity == $value ? "selected" : '' ; ?>>
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
                    <input type="text" name="txtCapacity" placeholder="Enter new RAM capacity (Ex: Capacity: 8GB)" value="<?php echo $txtCapacity; ?>" maxlength="20">
                </fieldset>
                <p class="custom-note">Please enter 1 to 20 characters</p>
            <!-- memory type -->
                <h3 class="sub-header">Memory Type *</h3>
                <p class="custom-note">The desktop memory types you have previously added are listed here,
                    and if the desktop memory type you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop memory type in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop memory type,
                    it will be considered the desktop memory type of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selMemoryType">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Memory type: DDR4" <?php echo $selMemoryType == "Memory type: DDR4" ? "selected" : '' ; ?>>DDR4</option>
                        <option value="Memory type: DDR5" <?php echo $selMemoryType == "Memory type: DDR5" ? "selected" : '' ; ?>>DDR5</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Memory type: DDR4", "Memory type: DDR5");
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
                                                ?><option value="<?php echo $value; ?>" <?php echo $selMemoryType == $value ? "selected" : '' ; ?>>
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
                <!-- enter new memory type -->
                    <input type="text" name="txtMemoryType" placeholder="Enter new RAM capacity (Ex: Memory type: DDR3)" value="<?php echo $txtMemoryType; ?>" maxlength="20">
                </fieldset>
                <p class="custom-note">Please enter 1 to 20 characters</p>
            <!-- processor generation -->
                <h3 class="sub-header">Processor Generation *</h3>
                <p class="custom-note">The desktop processor generations you have previously added are listed here,
                    and if the desktop processor generation you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop processor generation in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop processor generation,
                    it will be considered the desktop processor generation of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selProcessorGeneration">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Processor Generation: 7th Generation" <?php echo $selProcessorGeneration == "Processor Generation: 7th Generation" ? "selected" : '' ; ?>>7th Generation</option>
                        <option value="Processor Generation: 8th Generation" <?php echo $selProcessorGeneration == "Processor Generation: 8th Generation" ? "selected" : '' ; ?>>8th Generation</option>
                        <option value="Processor Generation: 10th Generation" <?php echo $selProcessorGeneration == "Processor Generation: 10th Generation" ? "selected" : '' ; ?>>10th Generation</option>
                        <option value="Processor Generation: 11th Generation" <?php echo $selProcessorGeneration == "Processor Generation: 11th Generation" ? "selected" : '' ; ?>>11th Generation</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Processor Generation: 7th Generation", "Processor Generation: 8th Generation", "Processor Generation: 10th Generation", "Processor Generation: 11th Generation");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 2) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selProcessorGeneration == $value ? "selected" : '' ; ?>>
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
                <!-- enter new processor generation -->
                    <input type="text" name="txtProcessorGeneration" placeholder="Enter new processor generation (Ex: Processor Generation: 14th Generation)" value="<?php echo $txtProcessorGeneration; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- processor model -->
                <h3 class="sub-header">Processor Model *</h3>
                <p class="custom-note">The desktop processor models you have previously added are listed here,
                    and if the desktop processor model you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop processor model in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop processor model,
                    it will be considered the desktop processor model of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selProcessorModel">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Processor Model: Intel Core i3" <?php echo $selProcessorModel == "Processor Model: Intel Core i3" ? "selected" : '' ; ?>>Intel Core i3</option>
                        <option value="Processor Model: Intel Core i5" <?php echo $selProcessorModel == "Processor Model: Intel Core i5" ? "selected" : '' ; ?>>Intel Core i5</option>
                        <option value="Processor Model: Intel Core i7" <?php echo $selProcessorModel == "Processor Model: Intel Core i7" ? "selected" : '' ; ?>>Intel Core i7</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Processor Model: Intel Core i3", "Processor Model: Intel Core i5", "Processor Model: Intel Core i7");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 3) {
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
                <!-- enter new processor model -->
                    <input type="text" name="txtProcessorModel" placeholder="Enter new processor model (Ex: Processor Model: Intel Core i9)" value="<?php echo $txtProcessorModel; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- processor series -->
                <h3 class="sub-header">Processor Series *</h3>
                <p class="custom-note">The desktop processor series you have previously added are listed here,
                    and if the desktop processor series you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop processor series in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop processor series,
                    it will be considered the desktop processor series of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selProcessorSeries">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Processor Series: Intel Core I3" <?php echo $selProcessorSeries == "Processor Series: Intel Core I3" ? "selected" : '' ; ?>>Intel Core I3</option>
                        <option value="Processor Series: Intel Core I5" <?php echo $selProcessorSeries == "Processor Series: Intel Core I5" ? "selected" : '' ; ?>>Intel Core I5</option>
                        <option value="Processor Series: Intel Core I7" <?php echo $selProcessorSeries == "Processor Series: Intel Core I7" ? "selected" : '' ; ?>>Intel Core I7</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Processor Series: Intel Core I3", "Processor Series: Intel Core I5", "Processor Series: Intel Core I7");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 4) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selProcessorSeries == $value ? "selected" : '' ; ?>>
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
                <!-- enter new processor series -->
                    <input type="text" name="txtProcessorSeries" placeholder="Enter new processor series (Ex: Processor Series: Intel Core I9)" value="<?php echo $txtProcessorSeries; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- hard drive -->
                <h3 class="sub-header">Hard Drive *</h3>
                <p class="custom-note">The desktop hard drives you have previously added are listed here,
                    and if the desktop hard drive you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop hard drive in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop hard drive,
                    it will be considered the desktop hard drive of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selHardDrive">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Drive Type: HDD" <?php echo $selHardDrive == "Drive Type: HDD" ? "selected" : '' ; ?>>Hard Disk Drive (HDD)</option>
                        <option value="Drive Type: SSD" <?php echo $selHardDrive == "Drive Type: SSD" ? "selected" : '' ; ?>>Solid State Drive (SSD)</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Drive Type: HDD", "Drive Type: SSD");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 5) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selHardDrive == $value ? "selected" : '' ; ?>>
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
                <!-- enter new hard drive -->
                    <input type="text" name="txtHardDrive" placeholder="Enter new hard drive (Ex: Drive Type: SSD)" value="<?php echo $txtHardDrive; ?>" maxlength="20">
                </fieldset>
                <p class="custom-note">Please enter 1 to 20 characters</p>
            <!-- hard drive capacity -->
                <h3 class="sub-header">Hard Drive Capacity (Size) *</h3>
                <p class="custom-note">The desktop hard drive capacities you have previously added are listed here,
                    and if the desktop hard drive capacity you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop hard drive capacity in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop hard drive capacity,
                    it will be considered the desktop hard drive capacity of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selHardDriveCapacity">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Drive Capacity: 500GB" <?php echo $selHardDriveCapacity == "Drive Capacity: 500GB" ? "selected" : '' ; ?>>500GB</option>
                        <option value="Drive Capacity: 1TB" <?php echo $selHardDriveCapacity == "Drive Capacity: 1TB" ? "selected" : '' ; ?>>1TB</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Drive Capacity: 500GB", "Drive Capacity: 1TB");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 6) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selHardDriveCapacity == $value ? "selected" : '' ; ?>>
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
                <!-- enter new hard drive capacity -->
                    <input type="text" name="txtHardDriveCapacity" placeholder="Enter new hard drive capacity (Ex: Drive Capacity: 256GB)" value="<?php echo $txtHardDriveCapacity; ?>" maxlength="20">
                </fieldset>
                <p class="custom-note">Please enter 1 to 20 characters</p>
            <!-- graphics -->
                <h3 class="sub-header">Graphics *</h3>
                <p class="custom-note">The desktop graphics you have previously added are listed here,
                    and if the desktop graphic you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop graphic in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop graphic,
                    it will be considered the desktop graphic of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selGraphic">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Graphics: Intel HD Graphics" <?php echo $selGraphic == "Graphics: Intel HD Graphics" ? "selected" : '' ; ?>>Intel HD Graphics</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Graphics: Intel HD Graphics");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 7) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selGraphic == $value ? "selected" : '' ; ?>>
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
                <!-- enter new graphic -->
                    <input type="text" name="txtGraphic" placeholder="Enter new graphic by (Ex: Graphics: Intel HD Graphics)" value="<?php echo $txtGraphic; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- motherboard -->
                <h3 class="sub-header">Motherboard *</h3>
                <p class="custom-note">The desktop motherboard names you have previously added are listed here,
                    and if the desktop motherboard name you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop motherboard name in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop motherboard name,
                    it will be considered the desktop motherboard name of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selMotherboard">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Motherboard Brand: Intel" <?php echo $selMotherboard == "Motherboard Brand: Intel" ? "selected" : '' ; ?>>Intel</option>
                        <option value="Motherboard Brand: MSI" <?php echo $selMotherboard == "Motherboard Brand: MSI" ? "selected" : '' ; ?>>MSI</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Motherboard Brand: Intel", "Motherboard Brand: MSI");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 8) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selMotherboard == $value ? "selected" : '' ; ?>>
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
                <!-- enter new desktop motherboard name -->
                    <input type="text" name="txtMotherboard" placeholder="Enter new motherboard name (Ex: Motherboard Brand: MSI B450 Gaming Plus Motherboard)" value="<?php echo $txtMotherboard; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- power supply -->
                <h3 class="sub-header">Power Supply *</h3>
                <p class="custom-note">The desktop power supply names you have previously added are listed here,
                    and if the desktop power supply name you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop power supply name in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop power supply name,
                    it will be considered the desktop power supply name of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selPowerSupply">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Power Supply Watt: 550W" <?php echo $selPowerSupply == "Power Supply Watt: 550W" ? "selected" : '' ; ?>>550W</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Power Supply Watt: 550W");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 9) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selPowerSupply == $value ? "selected" : '' ; ?>>
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
                <!-- enter new desktop power supply name -->
                    <input type="text" name="txtPowerSupply" placeholder="Enter new power supply name (Ex: Power Supply Watt: 550W)" value="<?php echo $txtPowerSupply; ?>" maxlength="25">
                </fieldset>
                <p class="custom-note">Please enter 1 to 25 characters</p>
            <!-- sound -->
                <h3 class="sub-header">Sound *</h3>
                <p class="custom-note">The desktop sound names you have previously added are listed here,
                    and if the desktop sound name you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop sound name in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop sound name,
                    it will be considered the desktop sound name of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selSound">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Sound: Realtek High Definition Audio" <?php echo $selSound == "Sound: Realtek High Definition Audio" ? "selected" : '' ; ?>>Realtek High Definition Audio</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Sound: Realtek High Definition Audio");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 10) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selSound == $value ? "selected" : '' ; ?>>
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
                <!-- enter new sound name -->
                    <input type="text" name="txtSound" placeholder="Enter new sound name (Ex: Sound: Realtek High Definition Audio)" value="<?php echo $txtSound; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- case -->
                <h3 class="sub-header">Computer Case *</h3>
                <p class="custom-note">The desktop case names you have previously added are listed here,
                    and if the desktop case name you are adding appears in the list, you can select it. Alternatively, you can enter your new desktop case name in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the desktop case name,
                    it will be considered the desktop case name of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selCase">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Case: Normal" <?php echo $selCase == "Case: Normal" ? "selected" : '' ; ?>>Normal</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Desktop' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Case: Normal");
                            $foundRecord = 0;
                            if ($stmt == true) {
                                while ($row = mysqli_fetch_assoc($stmt)) {
                                    foreach (explode(", ", $row['specification']) as $key => $value) {
                                        if ($key == 12) {
                                            for ($i=0; $i < count($savedRecords); $i++) {
                                                if ($savedRecords[$i] == $value) {
                                                    $foundRecord = 1;
                                                    break;
                                                }
                                            }
                                            if ($foundRecord == 0) {
                                                ?><option value="<?php echo $value; ?>" <?php echo $selCase == $value ? "selected" : '' ; ?>>
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
                <!-- enter new case name -->
                    <input type="text" name="txtCase" placeholder="Enter new case name (Ex: Case: Golden Field Q14B Computer Case)" value="<?php echo $txtCase; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- description -->
                <h3 class="sub-header">Description (Optional)</h3>
                <textarea name="rtxtDescription" placeholder="Enter description (Ex: RGB Fan: Yes, etc..)" maxlength="1500"><?php echo $rtxtDescription; ?></textarea>
                <p class="custom-note">Please enter 1 to 1500 characters</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: When presenting facts, use commas
                    <font style="color: #008000; font-weight: bold;">(,)</font> to separate each fact.</p>
            <!-- images -->
                <h3 class="sub-header">Product Images (Optional)</h3>
                <div class="load-image-layout">
                    <table>
                        <tr>
                            <?php
                            /* display saved images */
                                $zipImages = explode(", ", $rowForSec['image']);
                                for ($i=0; $i < count($zipImages); $i++) {
                                    echo "<td><img src='data:image; base64, $zipImages[$i]' alt=''></td>";
                                }
                            ?>
                        </tr>
                    </table>
                </div>
                <input type="file" name="docImage[]" multiple accept="image/*">
                <p class="custom-note" style="margin-bottom: 20px;">Note: Please ensure that the total size of the
                    images being uploaded is <font style="color: #008000; font-weight: bold;">less than 20MB</font></p>
            <!-- done & cancel btn -->
                <a href="/admin/dashboard/"><button type="button">Cancel</button></a>
                <a href="/admin/edit-item/?<?php echo $basicDetails; ?>"><button type="button">Back</button></a>
                <button type="submit" onclick="return confirm('Are you sure you want to edit the product?')">Done</button>
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