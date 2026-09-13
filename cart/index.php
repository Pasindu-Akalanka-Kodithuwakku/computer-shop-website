<?php
    session_start();
    require_once("../include/server/index.php");
    require_once("../function/create-primary-key/index.php");
    require_once("../function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_GET['cart']) && $_GET['cart'] != '') {
        unset($_SESSION['cart'][$_GET['cart']]);
    }
    if (!empty($_POST)) {
        foreach ($_POST as $key => $value) {
            foreach ($_SESSION['cart'] as $i => $x) {
                if ($key == $i) {
                    $_SESSION['cart'][$key] = $value;
                    break;
                }
            }
        }
    }
    if (isset($_POST['clearAll'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_GET['send'])) {
        if (!isset($_SESSION['memberId'])) {
            header("location: /member/sign-in/");
            exit();
        } else {
            if (!is_dir("../attachment/$_SESSION[memberId]")) {
                mkdir("../attachment/$_SESSION[memberId]", 0777, true); # create new attachment folder
            }
        /* create new file for invalid inputs */
            $createFile = fopen("../attachment/index.php", 'w'); # file pointer
            fwrite($createFile, "<?php\n/* redirect main page */\n\theader('Location: /');\n\texit();\n?>"); # write into this file
            fclose($createFile); # close the file
        /* create new file for invalid inputs */
            $createFile = fopen("../attachment/$_SESSION[memberId]/index.php", 'w'); # file pointer
            fwrite($createFile, "<?php\n/* redirect main page */\n\theader('Location: /');\n\texit();\n?>"); # write into this file
            fclose($createFile); # close the file
        /* create new quotation */
            $createFile = fopen("../attachment/$_SESSION[memberId]/quotation.txt", 'w'); # file pointer
            $content = "--- DILLA'S PC - PADUKKA ---\n\n\n\n";
            $content .= "PRODUCT NAME \t QUANTITY \t PRICE\n";
            $content .= "-------------------------------------------\n\n";
            $total = 0;
            $count = 0;
            foreach ($_SESSION['cart'] as $key => $value) {
                $sql = "SELECT
                    `productName`,
                    `discount`
                FROM
                    `Product`
                WHERE
                    `productId` = '$key'
                LIMIT 1
                ";
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true) {
                    if (mysqli_num_rows($stmt) == 1) {
                        $row = mysqli_fetch_assoc($stmt);
                        $content .= $row['productName'] ." \t " .$value ." \t " .number_format(($row['discount'] * $value), 2) ." LKR \n";
                        $count = $count + $value; # count all items
                        $total = $total + ($row['discount'] * $value);
                    }
                }
            }
            $content .= "-------------------------------------------";
            $content .= "\n\nTOTAL : \t" .$total = number_format($total, 2) ." LKR //\n\nNo. of items: " .$count ."\n\n\n\n";
            $content .= "--- THANK YOU ---";
            fwrite($createFile, $content); # write into this file
            fclose($createFile); # close the file
        /* update quotation details */
            $id = createPrimaryKey($conn, "Message", "messageId", "MG");
            $encMessage = mysqli_real_escape_string($conn, base64_encode('New Quotation Details Text File'));
            $sql = "INSERT INTO `Message`(
                `messageId`,
                `description`,
                `attachment`,
                `sender`,
                `receiver`
            ) VALUES (
                '$id',
                '$encMessage',
                'quotation.txt',
                '$_SESSION[memberId]',
                'Admin'
            )";
            $stmt = mysqli_query($conn, $sql);
            if ($stmt == true) {
                header("Location: /member/message-center/");
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Cart</title>
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
        main .main-header {
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
    /* cart div style */
        main .cart-div {
            max-height: 300px;
            margin: 20px auto;
            overflow: auto;
            scrollbar-width: none;
        }
        main .cart-div table {
            min-width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
        }
        main .cart-div table th, main .cart-div table td {
            width: 200px;
            min-width: min-content;
        }
        main .cart-div table th {
            padding: 14px;
            font-size: large;
            text-transform: uppercase;
            background-color: #fff;
            color: #000000;
        }
        main .cart-div table td {
            text-align: center;
            border-bottom: 1px solid #808080;
            padding: 14px;
        }
        main .cart-div table td a {
            display: block;
            text-decoration: none;
        }
        main .cart-div table td img {
            display: block;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin: 4px auto;
        }
        main form table td input[type="number"] {
            padding: 14px;
        }
        main form .frm-btn {
            margin: 20px;
        }
        main form button {
            margin: 8px;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
    /* last details style */
        main .last-del-layout {
            width: 80%;
            margin: auto;
            overflow: auto;
            box-sizing: border-box;
        }
        main .last-del-layout table {
            min-width: max-content;
            max-height: 300px;
            box-sizing: border-box;
            margin: 4px auto;
            border-radius: 4px;
            box-shadow: 2px 2px 5px #fff;
            background-color: #ddd;
            color: #000000;
        }
        main .last-del-layout table td {
            min-width: max-content;
            padding: 8px;
            text-align: center;
        }
        main .last-del-layout table td hr {
            margin: 2px auto;
            height: 2px;
            background-color: #000000;
        }
        main .last-del-layout table a {
            display: inline-block;
            text-decoration: none;
            margin: 4px;
            padding: 8px;
            font-weight: bold;
            border-radius: 4px;
            box-sizing: border-box;
            box-shadow: 2px 2px 5px #000000;
            background-color: #909090;
            color: #000000;
        }
    /* PDF paper style */
        .quote-outline-border, .quote-outline-border .quote-outline {
            width: 100%;
            box-sizing: border-box;
            padding: 25px;
        }
        .quote-outline-border {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            background-color: #fff;
            color: #000000;
            display: none;
        }
        .quote-outline-border .quote-outline {
            min-height: 297mm;
            border: 1px solid #000000;
        }
        .quote-outline-border .quote-outline .img-border {
            text-align: right;
        }
        .quote-outline-border .quote-outline .img-border img {
            width: 85px;
            display: inline-block;
        }
        .quote-outline-border .quote-outline p {
            color: #808080;
        }
        .quote-outline-border .quote-outline h3 {
            margin: 15px 0 5px 0;
        }
        .quote-outline-border .quote-outline table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .quote-outline-border .quote-outline .header-part-1, .quote-outline-border .quote-outline .header-part-2 {
            width: 50%;
            float: left;
            margin-bottom: 50px;
        }
        .quote-outline-border .quote-outline .header-part-1 p {
            color: #000000;
        }
        .quote-outline-border .quote-outline .header-part-2 table th {
            text-align: right;
        }
        .quote-outline-border .quote-outline .item-table-layout {
            width: 100%;
        }
        .quote-outline-border .quote-outline .item-table-layout table thead th {
            padding: 10px;
            text-align: right;
            background-color: #ddd;
        }
        .quote-outline-border .quote-outline .item-table-layout table td {
            padding: 10px;
            text-align: right;
        }
        .quote-outline-border .quote-outline .item-table-layout table #first-column {
            text-align: left;
        }
        .quote-outline-border .quote-outline .item-table-layout table tr {
            border: 1px solid #909090;
        }
        #total-table, #total-table * {
            border: none;
        }
        #total-table * {
            margin: 0;
            padding: 0;
        }
        #total-table #main-table-column table {
            border: 1px solid #909090;
            margin-top: 25px;
        }
        #total-table #main-table-column table th, #total-table #main-table-column table td {
            padding: 10px;
        }
        #total-table #main-table-column table th {
            text-align: left;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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
        <h3 class="main-header">Cart 🛒</h3>
        <form action="/cart/" method="post">
            <div class="cart-div">
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th></th>
                    </tr>
                    <?php
                        foreach ($_SESSION['cart'] as $key => $value) {
                            $sql = "SELECT
                                `productId`,
                                `productName`,
                                `price`,
                                `discount`,
                                `quantityInStock`,
                                `image`
                            FROM
                                `Product`
                            WHERE
                                `productId` = '$key'
                            LIMIT 1
                            ";
                            $stmt = mysqli_query($conn, $sql);
                            if ($stmt == true) {
                                if (mysqli_num_rows($stmt) == 1) {
                                    $row = mysqli_fetch_assoc($stmt);
                                    echo "<tr>";
                                    echo "<td>";
                                    if ($row['image'] != null) {
                                        foreach (explode(", ", $row['image']) as $i => $x) {
                                            echo "<img src='data:image; base64, $x' alt=''>";
                                            break;
                                        }
                                    } else {
                                        echo "<img src='' alt=''>";
                                    }
                                    echo $row['productName'];
                                    echo "</td>";
                                    echo "<td><input type='number' name='$key' value='$value' min='1' max='$row[quantityInStock]'></td>";
                                    echo "<td>" .($row['discount'] == 0 ? number_format(($row['price'] * $value), 2) : number_format(($row['discount'] * $value), 2)) ." LKR</td>";
                                    echo "<td><a href='/cart/?cart=$row[productId]' title='Cancel item'>❌</a></td>";
                                    echo "</tr>";
                                }
                            }
                        }
                    ?>
                </table>
            </div>
            <?php
                if (!empty($_SESSION['cart'])) {
                    echo "<div class='frm-btn'>";
                    echo "<button name='clearAll' title='Cancel all saved items'>Clear All 🧹</button>";
                    echo "<button type='submit'>Update Changes ✔️</button>";
                    echo "</div>";
                }
            ?>
        </form>
        <?php
            if (countCart() > 0) {
                ?>
                <div class="last-del-layout">
                    <table>
                        <tr><td colspan="3"><h1><u>FINAL REPORT</u></h1></td></tr>
                        <?php
                            $total = 0;
                            $count = 0;
                            foreach ($_SESSION['cart'] as $key => $value) {
                                $sql = "SELECT
                                    `productName`,
                                    `price`,
                                    `discount`
                                FROM
                                    `Product`
                                WHERE
                                    `productId` = '$key'
                                LIMIT 1
                                ";
                                $stmt = mysqli_query($conn, $sql);
                                if ($stmt == true) {
                                    if (mysqli_num_rows($stmt) == 1) {
                                        $row = mysqli_fetch_assoc($stmt);
                                        echo "<tr>";
                                        echo "<td style='text-align: left;'>$row[productName]</td>";
                                        echo "<td>X$value</td>";
                                        echo "<td style='text-align: right;'>" .($row['discount'] ? $row['price'] * $value : $row['discount'] * $value) ." LKR";
                                        if ($count == count($_SESSION['cart']) - 1) {
                                            echo "<hr>";
                                        }
                                        echo "</td>";
                                        echo "</tr>";
                                        $total = $total + ($row['discount'] ? $row['price'] * $value : $row['discount'] * $value);
                                    }
                                }
                                $count++;
                            }
                            $total = number_format($total, 2);
                            echo "<tr>";
                            echo "<td style='text-align: left;'><h2>Total</h2></td><td></td>";
                            echo "<td style='text-align: right;'><h2>$total LKR</h2><hr><hr></td>";
                            echo "</tr>";
                            if (!empty($_SESSION['cart'])) {
                                echo "<tr><td colspan='3'>";
                                echo "<a href='/cart/?send'>Send Quotation</a>"; // button 1
                                ?><a href="#" onclick="saveDivAsPDF()">Download Quotation</a><?php // button 2
                                echo "</td></tr>"; # sent & download buttons
                            }
                        ?>
                    </table>
                </div>
                <?php
            }
        /* process data for pdf file */
            $receiverName = '';
            $contact = '';
            $email = '';

            if(isset($_SESSION['memberId'])) {
                $sql = "SELECT * FROM `Member` WHERE `memberId` = '$_SESSION[memberId]' LIMIT 1";
                $stmt = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($stmt);

                $receiverName = $row['firstName'] .' ' .$row['lastName'];
                $contact = $row['contact'];
                $email = $row['email'];
            }
        ?>
    <!-- PDF download -->
        <div class="quote-outline-border" id="pdf-content">
            <div class="quote-outline">
                <h1>QUOTE</h1>
                <div class="img-border"><img src="/image/logo.jpg" alt=""></div>
                <p>Dilla's PC, Indrani Mahal, Hanwella Road, Padukka, Sri Lanka</p>
                <h3>FOR</h3>
                <div class="header-part-1">
                    <p><?php echo($receiverName); ?></p>
                    <p><?php echo($contact); ?><br><?php echo($email); ?></p>
                </div>
                <div class="header-part-2">
                    <table>
                        <tr>
                            <td>Quote No:</td>
                            <th>-</th>
                        </tr>
                        <tr>
                            <td>Issue date:</td>
                            <th><?php echo(date("d/m/Y"));?></th>
                        </tr>
                        <tr>
                            <td>Valid until:</td>
                            <th><?php echo date('d/m/Y', strtotime(date('Y-m-d') . ' +14 days')); ?></th>
                        </tr>
                    </table>
                </div>
                <div class="item-table-layout">
                    <table>
                        <thead>
                            <tr>
                                <th id="first-column">DESCRIPTION</th>
                                <th>QUANTITY</th>
                                <th>UNIT PRICE (Rs)</th>
                                <th>AMOUNT (Rs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $total = 0;
                                foreach ($_SESSION['cart'] as $key => $value) {
                                    $sql = "SELECT
                                        `productName`,
                                        `price`,
                                        `discount`
                                    FROM
                                        `Product`
                                    WHERE
                                        `productId` = '$key'
                                    LIMIT 1
                                    ";
                                    $stmt = mysqli_query($conn, $sql);
                                    if ($stmt == true) {
                                        if (mysqli_num_rows($stmt) == 1) {
                                            $row = mysqli_fetch_assoc($stmt);
                                            echo "<tr>";
                                            echo "<td id='first-column'>$row[productName]</td>";
                                            echo "<td>$value</td>";
                                            echo "<td>" .($row['discount'] ? $row['price'] : $row['discount']) ."</td>";
                                            echo "<td>" .($row['discount'] ? $row['price'] * $value : $row['discount'] * $value) ."</td>";
                                            echo "</tr>";
                                            $total = $total + ($row['discount'] ? $row['price'] * $value : $row['discount'] * $value);
                                        }
                                    }
                                }
                                $total = number_format($total, 2);
                            ?>
                            <tr id="total-table">
                                <td></td>
                                <td colspan="3" id="main-table-column">
                                    <table>
                                        <tr>
                                            <th style="font-weight: 900;">SUBTOTAL:</th>
                                            <td style="font-weight: 600;"><?php echo("Rs. " .$total); ?></td>
                                        </tr>
                                        <tr>
                                            <th style="font-weight: normal;">TOTAL:</th>
                                            <td><?php echo("Rs. " .$total); ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p style="margin: 30px auto; padding: 10px; font-size: 12px; color: #000000;">
                    <b>Dilla's PC, </b>Indrani Mahal, Hanwella Road, Padukka, Sri Lanka.&nbsp;
                    <b>E-mail: </b>anushkanawagamuwa@gmail.com
                </p>
            </div>
        </div>
    </main>
    <footer>
        <div class="footer-about"><hr>
            <div class="about-title">
                <div class="footer-img"><img src="../image/logo.jpg" alt="dilla's pc"></div>
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
        function saveDivAsPDF() {
            document.getElementById("pdf-content").style.display = "block";
            
            const element = document.getElementById('pdf-content');

            html2canvas(element, { scale: 1.5, useCORS: true }).then(canvas => {
                // Convert to JPEG for better compression
                const imgData = canvas.toDataURL('image/jpeg', 0.6); // Lower quality (0.6)

                const imgWidth = 190; // Keep fixed width
                const imgHeight = (canvas.height * imgWidth) / canvas.width; // Maintain aspect ratio

                const pageWidth = 210; // A4 width in mm
                const pageHeight = imgHeight + 20; // Dynamic height with padding

                const pdf = new jspdf.jsPDF('p', 'mm', [pageWidth, pageHeight]);

                pdf.addImage(imgData, 'JPEG', 10, 10, imgWidth, imgHeight);
                pdf.save('Quote.pdf');
            });

            document.getElementById("pdf-content").style.display = "none";
        }
    </script>
</body>
</html>