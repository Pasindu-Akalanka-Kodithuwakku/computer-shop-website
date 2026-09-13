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
    if (!(isset($_REQUEST['category']) && strtoupper($_REQUEST['category']) == "MONITOR")) {
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
    $selScreenSize = $specification[0];
    $txtScreenSize = '';
    $selResolution = $specification[1];
    $txtResolution = '';
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
        $selScreenSize = $_POST['selScreenSize'];
        $txtScreenSize = trim($_POST['txtScreenSize']);
        $selResolution = $_POST['selResolution'];
        $txtResolution = trim($_POST['txtResolution']);
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
        if ($selScreenSize == "none" && strlen($txtScreenSize) < 1) { $info[] = "Monitor screen size field is required. ⁉️"; /* screen size field not fill error */ }
        if ($selResolution == "none" && strlen($txtResolution) < 1) { $info[] = "Monitor resolution field is required. ⁉️"; /* resolution field not fill error */ }
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
            if ($txtScreenSize > 0) { $selScreenSize = "none"; $screenSize = mysqli_real_escape_string($conn, $txtScreenSize); } else { $screenSize = mysqli_real_escape_string($conn, $selScreenSize); }
            if ($txtResolution > 0) { $selResolution = "none"; $resolution = mysqli_real_escape_string($conn, $txtResolution); } else { $resolution = mysqli_real_escape_string($conn, $selResolution); }
        /* join specification */
            $specification = array($screenSize, $resolution);
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
            <form action="/admin/edit-item/Monitor/" method="post" enctype="multipart/form-data" autocomplete="off">
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
            <!-- screen size -->
                <h3 class="sub-header">Screen Size (inch) *</h3>
                <p class="custom-note">The monitor screen sizes you have previously added are listed here,
                    and if the monitor screen size you are adding appears in the list, you can select it. Alternatively, you can enter your new monitor screen size in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the monitor screen size,
                    it will be considered the monitor screen size of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selScreenSize">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Screen Size: 14 inch" <?php echo $selScreenSize == "Screen Size: 14 inch" ? "selected" : '' ; ?>>14</option>
                        <option value="Screen Size: 19 inch" <?php echo $selScreenSize == "Screen Size: 19 inch" ? "selected" : '' ; ?>>19</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Monitor' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Screen Size: 14 inch", "Screen Size: 19 inch");
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
                                                ?><option value="<?php echo $value; ?>" <?php echo $selScreenSize == $value ? "selected" : '' ; ?>>
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
                <!-- enter new screen size -->
                    <input type="text" name="txtScreenSize" placeholder="Enter new screen size (Ex: Screen Size: 24 inch)" value="<?php echo $txtScreenSize; ?>" maxlength="30">
                </fieldset>
                <p class="custom-note">Please enter 1 to 30 characters</p>
            <!-- resolution -->
                <h3 class="sub-header">Resolution *</h3>
                <p class="custom-note">The monitor resolutions you have previously added are listed here,
                    and if the monitor resolution you are adding appears in the list, you can select it. Alternatively, you can enter your new monitor resolution in the box below.</p>
                <p class="custom-note" style="margin-bottom: 20px;">Note: If you enter anything in the box corresponding to the monitor resolution,
                    it will be considered the monitor resolution of the new product you are entering.</p>
                <fieldset style="margin-bottom: 0;">
                    <select name="selResolution">
                        <option value="none">-- Select a previously entered name --</option>
                        <option value="Resolution: Full HD (1080p) 1920 x 1080 at 60 Hz" <?php echo $selResolution == "Resolution: Full HD (1080p) 1920 x 1080 at 60 Hz" ? "selected" : '' ; ?>>Full HD (1080p) 1920 x 1080 at 60 Hz</option>
                        <?php
                            $sql = "SELECT `specification` FROM `Product` WHERE `category` = 'Monitor' AND `trash` = 0";
                            $stmt = mysqli_query($conn, $sql);
                            $savedRecords = array("Resolution: Full HD (1080p) 1920 x 1080 at 60 Hz");
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
                                                ?><option value="<?php echo $value; ?>" <?php echo $selResolution == $value ? "selected" : '' ; ?>>
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
                <!-- enter new resolution -->
                    <input type="text" name="txtResolution" placeholder="Enter new resolution (Ex: Resolution: 1920 x 1080 Full HD)" value="<?php echo $txtResolution; ?>" maxlength="50">
                </fieldset>
                <p class="custom-note">Please enter 1 to 50 characters</p>
            <!-- description -->
                <h3 class="sub-header">Description (Optional)</h3>
                <textarea name="rtxtDescription" placeholder="Enter description (Ex: Color: Black, etc..)" maxlength="1500"><?php echo $rtxtDescription; ?></textarea>
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