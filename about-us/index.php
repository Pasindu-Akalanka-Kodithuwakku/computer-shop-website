<?php
    session_start();
    require_once("../include/server/index.php");
    require_once("../function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    $sql = "SELECT
        `firstName`,
        `lastName`,
        `image`
    FROM
        `Admin`
    WHERE
        `trash` = 0
    ORDER BY
        `adminId` ASC
    ";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt == false) {
        header('Location: /?error=Something went wrong!');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - About Us</title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main .search-form, main .about-layout p, main .about-layout h3 {
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
        main .about-layout {
            margin: 20px auto;
            margin-top: 100px;
            overflow: auto;
        }
        main .about-layout .image-layout {
            width: 100%;
            margin: auto;
            overflow: auto;
            scrollbar-width: none;
        }
        main .about-layout .about-paragraph {
            margin: 20px;
            padding: 8px;
            text-align: justify;
        }
        main .about-layout .team-header {
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
        main .about-layout .image-layout table {
            min-width: max-content;
            table-layout: fixed;
            border-spacing: 20px;
        }
        main .about-layout .image-layout table th {
            width: 200px;
            padding: 8px;
            border-radius: 4px;
            box-shadow: 2px 2px 8px #fff;
        }
        main .about-layout .image-layout table th div {
            width: 100px;
            height: 100px;
            margin: auto;
            border-radius: 50%;
            background-color: #fff;
            color: #000000;
        }
        main .about-layout .image-layout table th div img {
            width: 100%;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        main .about-layout .image-layout table th h3 {
            margin: 4px auto;
            margin-top: 20px;
        }
        main .about-layout .image-layout table th p {
            margin: 4px auto;
            font-weight: normal;
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
    <!-- about us -->
        <div class="about-layout">
            <h3 class="team-header">We are Dilla's PC</h3>
            <p class="about-paragraph">
                At Dilla's PC, we are dedicated to providing top-quality computer products, services,
                and support to our valued customers. With nearly 11 years of industry experience,
                our expert team is committed to helping you find the perfect tech solutions to meet your needs.
                Whether you're looking for the latest hardware, software, or repair services,
                you can count on us to deliver exceptional value and personalized service every time.
                Your satisfaction is our priority, and we're here to ensure you have the best possible experience with your technology.
            </p>
            <h3 class="team-header">Our Team</h3>
            <div class="image-layout">
                <table>
                    <?php
                        $countRows = 1;
                        $countColumn = 0;
                        while ($row = mysqli_fetch_assoc($stmt)) {
                            if ($countRows == 1) {
                                ?>
                                <tr>
                                    <th>
                                        <div><img src="data:image; base64, <?php echo $row['image']; ?>" alt=""></div>
                                        <h3><?php echo $row['firstName'] .' ' .$row['lastName']; ?></h3>
                                        <p>Administrator of Dilla's PC</p>
                                    </th>
                                </tr>
                                <?php
                            } else {
                                if ($countColumn == 0) {
                                    echo "<tr>";
                                }
                                ?>
                                <th>
                                    <div><img src="data:image; base64, <?php echo $row['image']; ?>" alt=""></div>
                                    <h3><?php echo $row['firstName'] .' ' .$row['lastName']; ?></h3>
                                    <p>Service Provider</p>
                                </th>
                                <?php
                                if ($countColumn == 4) {
                                    echo "</tr>";
                                    $countColumn = 0;
                                }
                                $countColumn = $countColumn + 1;

                            }
                            $countRows = $countRows + 1;
                        }
                    ?>
                </table>
            </div>
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