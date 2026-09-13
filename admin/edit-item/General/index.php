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
    if (!(isset($_REQUEST['category']) && $_REQUEST['category'] != '')) {
        header('Location: /admin/edit-item/'); # redirect to back page
        exit();
    }
    $info = array();
    $row = mysqli_fetch_assoc($stmt); # load data
    $rowForSec = $row; # load data 2
/* set variables */
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
            <h3 class="title-header">Edit Item 🛠️</h3>
            <form action="/admin/edit-item/General/" method="post" enctype="multipart/form-data" autocomplete="off">
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
            <!-- description -->
                <h3 class="sub-header">Description (Optional)</h3>
                <textarea name="rtxtDescription" placeholder="Enter product description" maxlength="1500"><?php echo $rtxtDescription; ?></textarea>
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