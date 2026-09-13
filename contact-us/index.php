<?php
    session_start();
    require_once("../include/server/index.php");
    require_once("../function/create-primary-key/index.php");
    require_once("../function/add-cart/index.php");
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    $info = array();
    if (isset($_POST['complaint'])) {
        $id = createPrimaryKey($conn, "Complaint", "complaintId", "CP");
        $about = $_POST['about'];
        $category = $_POST['category'];
        $complaint =  mysqli_real_escape_string($conn, trim($_POST['complaint']));
        if (strlen($complaint) > 0) {
            $sql = "SELECT * FROM `Complaint` WHERE `description` = '$complaint' LIMIT 1";
            $stmt = mysqli_query($conn, $sql);
            if (mysqli_num_rows($stmt) == 0) {
                $sql = "INSERT INTO `Complaint` (
                    `complaintId`,
                    `description`,
                    `about`,
                    `category`
                ) VALUES (
                    '$id',
                    '$complaint',
                    '$about',
                    '$category'
                )";
                $stmt = mysqli_query($conn, $sql);
                if ($stmt == true) {
                    $info[] = "Thanks for your feedback. Your message has been sent successfully.";
                }
            } else {
                $info[] = "<font style='color: #0000ff;'>Your message has already been sent by you.</font>";
            }
        } else {
            $info[] = "<font style='color: #ff0000;'>Sorry! Space-only entries will not be accepted.</font>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - Contact Us</title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main .search-form, main .contact-layout p, main .contact-layout h3 {
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
        main .contact-layout .des-header {
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
        main .contact-layout {
            margin: 20px auto;
            margin-top: 100px;
            overflow: auto;
        }
        main .contact-layout .des-layout {
            width: 100%;
            margin: auto;
            overflow: auto;
        }
        main .contact-layout .des-layout table {
            width: 100%;
            min-width: fit-content;
            table-layout: fixed;
            border-spacing: 20px;
        }
        main .contact-layout .des-layout table th {
            width: 50%;
            padding: 8px;
            text-align: justify;
        }
        main .contact-layout .des-layout table th a {
            text-decoration: none;
            display: inline-block;
            margin: 4px auto;
            padding: 8px;
            color: #808080;
        }
        main .contact-layout .des-layout table th .img-layout {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background-color: #909090;
            color: #fff;
        }
        main .contact-layout .des-layout table th .email-layout {
            margin: auto;
            overflow: auto;
            scrollbar-width: none;
            padding: 8px;
        }
        main .contact-layout .des-layout table th .img-layout img {
            width: 100%;
            height: 35px;
            border-radius: 4px;
            object-fit: cover;
        }
    /* new suggestions form */
        main .contact-layout .des-layout .suggestions-table form {
            box-sizing: border-box;
        }
        main .contact-layout .des-layout .suggestions-table select, main .contact-layout .des-layout .suggestions-table input {
            box-sizing: border-box;
            border-radius: 4px;
            width: 100%;
            height: 40px;
            padding: 8px;
        }
        main .contact-layout .des-layout .suggestions-table textarea {
            box-sizing: border-box;
            border-radius: 4px;
            width: 100%;
            height: 100px;
            padding: 8px;
        }
        main .contact-layout .des-layout .suggestions-table button[type="submit"] {
            box-sizing: border-box;
            border: 1px solid #fff;
            border-radius: 4px;
            box-shadow: 2px 2px 5px #fff;
            width: 100%;
            padding: 14px;
            cursor: pointer;
            font-weight: bold;
            background-color: #808080;
            color: #000000;
        }
        main .contact-layout .des-layout .suggestions-table button[type="submit"]:hover {
            box-shadow: 2px 2px 2px #fff;
            background-color: #909090;
        }
        main .contact-layout .des-layout .suggestions-table th {
            text-align: left;
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
    <!-- contact us -->
        <div class="contact-layout">
        <!-- new ideas, suggestions or allegations -->
            <h3 class="des-header" style="background-color: rgb(30, 30, 30); color: #fff;">New Ideas, Suggestions or Allegations</h3>
            <div class="des-layout">
                <table class="suggestions-table">
                    <form action="/contact-us/#submit" method="post" autocomplete="off">
                        <tr>
                            <th><label for="about">What are you complaining about?</label></th>
                            <th>
                                <select name="about" id="about">
                                    <option value="General">General</option>
                                    <option value="Shop">About our computer shop</option>
                                    <option value="Website">About our website</option>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th><label for="category">What is your complaint category?</label></th>
                            <th>
                                <select name="category" id="category">
                                    <option value="General">General</option>
                                    <option value="Suggestions">New ideas, Suggestions</option>
                                    <option value="Allegations">Allegations</option>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th><label for="complaint">What is your complaint?</label></th>
                            <th><textarea name="complaint" id="complaint" placeholder="Type your feedback..." maxlength="1500" required></textarea></th>
                        </tr>
                        <tr>
                            <th id="submit">
                                <?php
                                    if (empty($info)) {
                                        ?><font style="color: #808080;">Your feedback is very valuable to us, so we look forward to your valuable feedback.
                                            No verification of your identity is required for this.</font><?php
                                    } else {
                                        foreach ($info as $key => $value) {
                                            echo "<font style='color: #008000;'>" .$value ."</font><br>";
                                        }
                                    }
                                ?>
                            </th>
                            <th><button type="submit">SEND MESSAGE ✔️</button></th>
                        </tr>
                    </form>
                </table>
            </div>
        <!-- visit us -->
            <h3 class="des-header">Visit Us</h3>
            <div class="des-layout">
                <table>
                    <tr>
                        <th><div class="img-layout"><img src="address.jpg" alt="address" title="Address"></div></th>
                        <th><address title="Address">Dilla's PC, Indrani Mahal, Hanwella Road, Padukka.</address></th>
                    </tr>
                    <tr>
                        <th><div class="img-layout"><img src="location.jpg" alt="location" title="Google map location"></div></th>
                        <th title="Google map location">
                            <a href="https://www.google.com/maps/place/Dilla's+PC/@6.843881,80.0911411,17z/data=!4m14!1m7!3m6!1s0x3ae2536296c07609:0xd8f2b5908e183773!2sDilla's+PC!8m2!3d6.843881!4d80.0911411!16s%2Fg%2F11l5y5tr5f!3m5!1s0x3ae2536296c07609:0xd8f2b5908e183773!8m2!3d6.843881!4d80.0911411!16s%2Fg%2F11l5y5tr5f?entry=ttu&g_ep=EgoyMDI0MTIxMS4wIKXMDSoASAFQAw%3D%3D" target="_blank">
                                Horana-Padukka Road, Sri Lanka
                            </a>
                        </th>
                    </tr>
                </table>
            </div>
        <!-- contact us -->
            <h3 class="des-header">Contact Us</h3>
            <div class="des-layout">
                <table>
                    <tr>
                        <th><div class="img-layout"><img src="hotline.jpg" alt="hotline" title="Hotline"></div></th>
                        <th title="Hotline">+94 75 085 5492</th>
                    </tr>
                    <tr>
                        <th><div class="img-layout"><img src="whatsapp.png" alt="whatsapp" title="WhatsApp number"></div></th>
                        <th title="Chat on WhatsApp">
                            <a href="https://api.whatsapp.com/send/?phone=%2B94750855492&text&type=phone_number&app_absent=0" target="_blank">+94 75 085 5492</a>
                        </th>
                    </tr>
                    <tr>
                        <th><div class="img-layout"><img src="email.jpg" alt="email" title="E-mail address"></div></th>
                        <th><div class="email-layout" title="E-mail address">anushkanawagamuwa@gmail.com</div></th>
                    </tr>
                </table>
            </div>
        <!-- follow us -->
            <h3 class="des-header">Follow Us</h3>
            <div class="des-layout">
                <table>
                    <tr>
                        <th><div class="img-layout"><img src="facebook.png" alt="facebook page" title="Our facebook page"></div></th>
                        <th title="Our facebook page">
                            <a href="https://m.facebook.com/61552992297568/" target="_blank">
                                Our Facebook Page
                            </a>
                        </th>
                    </tr>
                    <tr>
                        <th><div class="img-layout"><img src="youtube.png" alt="youtube channel" title="Our youtube channel"></div></th>
                        <th title="Our youtube channel">
                            <a href="https://www.youtube.com/@Dillaspc/" target="_blank">
                                Our YouTube Channel
                            </a>
                        </th>
                    </tr>
                    <tr>
                        <th><div class="img-layout"><img src="tiktok.jpeg" alt="tik-tok account" title="Our tik-tok account"></div></th>
                        <th title="Our TikTok Account">
                            <a href="https://www.tiktok.com/@dillas.pc.padukka" target="_blank">
                                Our TikTok Account
                            </a>
                        </th>
                    </tr>
                </table>
            </div>
        <!-- we welocme you -->
            <h3 class="des-header">We Welcome You</h3>
            <div class="des-layout">
                <table>
                    <tr>
                        <th>MONDAY</th>
                        <th>09:00 - 20:00</th>
                    </tr>
                    <tr>
                        <th>TUESDAY</th>
                        <th>09:00 - 20:00</th>
                    </tr>
                    <tr>
                        <th>WEDNESDAY</th>
                        <th>09:00 - 20:00</th>
                    </tr>
                    <tr>
                        <th>THURSDAY</th>
                        <th>09:00 - 20:00</th>
                    </tr>
                    <tr>
                        <th>FRIDAY</th>
                        <th>09:00 - 20:00</th>
                    </tr>
                    <tr>
                        <th>SATURDAY</th>
                        <th>09:00 - 20:00</th>
                    </tr>
                    <tr>
                        <th>SUNDAY</th>
                        <th>09:00 - 18:00</th>
                    </tr>
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