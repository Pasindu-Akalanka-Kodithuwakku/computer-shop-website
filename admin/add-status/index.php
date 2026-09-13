<?php
    require_once("../../include/server/index.php"); 
    require_once("../../function/create-primary-key/index.php");
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/'); # redirect to back page
        exit();
    }
    $info = array();
/* set null variables */
    $rtxtDescription = '';
    $selDuration = 24;
/* click submit button */
    if (!empty($_POST)) {
    /* check upload files type */
        for ($i=0; $i < count($_FILES['docMedia']['type']); $i++) {
            if (substr($_FILES['docMedia']['type'][$i], 0, 5) != "image" && substr($_FILES['docMedia']['type'][$i], 0, 5) != "video") {
                $info[] = "Please select only images or videos that fall into the image or video category. 🖼️🎥";
                break;
            }
        }
    /* check upload file size */
        for ($i=0; $i < count($_FILES['docMedia']['size']); $i++) {
            if ((($_FILES['docMedia']['size'][$i] / 1024) / 1024) > 20) {
                $info[] = "The size of the one image or video you uploaded is more than 20MB. ⚡";
                break;
            }
        }
    /* no errors */
        if (empty($info)) {
            $rtxtDescription = trim($_POST['rtxtDescription']);
            $selDuration = $_POST['selDuration'];
        /* source encryption & insert file one by one */
            for ($i=0; $i < count($_FILES['docMedia']['tmp_name']); $i++) {
                $id = createPrimaryKey($conn, "Status", "statusId", "ST"); # generate primary key
                $pathName = mysqli_real_escape_string($conn, $_FILES['docMedia']['name'][$i]); # secure media
                $type = mysqli_real_escape_string($conn, $_FILES['docMedia']['type'][$i]);
                $description = mysqli_real_escape_string($conn, $rtxtDescription); # secure description
                $duration = $selDuration; # secure duration
            /* insert records */
                $sql = "INSERT INTO `Status` (
                    `statusId`,
                    `pathName`,
                    `type`,
                    `description`,
                    `duration`
                ) VALUES (
                    '$id',
                    '$pathName',
                    '$type',
                    '$description',
                    $duration
                )";
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true) {
                    /* check is dir */
                    if (!is_dir("../../status")) {
                        mkdir("../../status", 0777, true); # create new status folder
                    }
                /* create new file for invalid inputs */
                    $createFile = fopen("../../status/index.php", 'w'); # file pointer
                    fwrite($createFile, "<?php\n/* redirect main page */\n\theader('Location: /');\n\texit();\n?>"); # write into this file
                    fclose($createFile); # close the file
                /* move uploaded file to status dir */
                    move_uploaded_file($_FILES['docMedia']['tmp_name'][$i], "../../status/" .$_FILES['docMedia']['name'][$i]);
                } else {
                    $info[] = "The one status is uploading failed.";
                }
            }
        /* go to main page */
            header('Location: /admin/dashboard/?info=Successfully added.');
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Add New Status</title>
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
            <h3 class="title-header">Add New Status +</h3>
            <form action="/admin/add-status/" method="post" enctype="multipart/form-data" autocomplete="off">
                <?php
                    foreach ($info as $key => $value) {
                        echo "<p class='custom-note' style='color: #808080;'>📪 $value</p>";
                    }
                ?>
            <!-- status image -->
                <h3 class="sub-header">Status Image *</h3>
                <input type="file" name="docMedia[]" multiple accept="image/*, video/*" required>
                <p class="custom-note">Note: Please ensure that the size of the
                    images being uploaded is less than 20MB</p>
            <!-- description -->
                <h3 class="sub-header">Description (Optional)</h3>
                <textarea name="rtxtDescription" placeholder="Enter description" maxlength="150"><?php echo $rtxtDescription; ?></textarea>
                <p class="custom-note">Please enter 1 to 150 characters</p>
            <!-- duration -->
                <h3 class="sub-header">Duration</h3>
                <select name="selDuration">
                    <option value="24">24 Hours</option>
                    <option value="48" <?php $selDuration == 48 ? "selected" : '' ; ?>>48 Hours</option>
                    <option value="72" <?php $selDuration == 72 ? "selected" : '' ; ?>>72 Hours</option>
                </select>
                <p class="custom-note" style="margin-bottom: 20px;">Select the duration for which the status should last.</p>
            <!-- done & cancel btn -->
                <a href="/admin/dashboard/"><button type="button">Cancel</button></a>
                <button type="submit" onclick="return confirm('Are you sure you want to add a new status?')">Done</button>
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