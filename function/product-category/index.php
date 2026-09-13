<?php
    function displayBasicProduct($conn, $category) {
        ?>
        <div class="category-box-layout">
            <div class="category-div">
                <?php
                    if ($category != "Accessories") {
                        $sql = "SELECT
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
                        FROM
                            `Product`
                        WHERE
                            `category` = '$category' AND
                            `trash` = 0
                        ORDER BY
                            RAND(),
                            `price` ASC
                        LIMIT 5";
                    } else {
                        $sql = "SELECT
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
                        FROM
                            `Product`
                        WHERE
                            (`category` != 'Laptop' AND
                            `category` != 'Desktop' AND
                            `category` != 'Monitor') AND
                            `trash` = 0
                        ORDER BY
                            RAND(),
                            `price` ASC
                        LIMIT 5";
                    }
                    $stmt = mysqli_query($conn, $sql);
                    if ($stmt == true) {
                        $count = 0;
                        while ($row = mysqli_fetch_assoc($stmt)) {
                            $count++;
                            ?>
                            <div class="show-item" id="<?php echo $category .'-' .$count; ?>">
                                <a href="/product/?index=<?php echo $row['productId']; ?>"> <!-- item-click -->
                                    <?php
                                        if ($row['discount'] > 0.00) {
                                            ?><p id="show-discount"><?php echo (int)((100 * ($row['price'] - $row['discount'])) / $row['price']); ?>% OFF</p><?php # display item discount
                                        }
                                    ?>
                                    <div class="item-image">
                                        <?php
                                            if ($row['image'] != null) {
                                                foreach (explode(", ", $row['image']) as $key => $value) {
                                                    ?><img src="data:image; base64, <?php echo  $value; ?>" alt=""><?php # display first image of item
                                                    break;
                                                }
                                            } else {
                                                ?><img src="" alt="item-image"><?php # no item image
                                            }
                                        ?>
                                    </div>
                                    <?php
                                        if ($row['quantityInStock'] > 0) {
                                            ?><p id="show-stock"><?php echo array($row['usedType'] /* store first data */, "In Stock" /* store second data */)[rand(0, 1)]; ?></p><?php # display brand new and in stock randomly
                                        } else {
                                            ?><p id="out-stock">Out of Stock</p><?php # display out of stock
                                        }
                                    ?>
                                    <div class="item-content">
                                        <?php
                                            $impDetails = array();
                                            $impDetails[] = $row['productName']; # store item name
                                            if ($row['brand'] != "") {
                                                $impDetails[] = $row['brand']; # store brand name
                                            }
                                            if ($row['model'] != "") {
                                                $impDetails[] = $row['model']; # store model name
                                            }
                                            if ($row['warranty'] != "No warrant") {
                                                if (strtolower(substr($row['warranty'], -8)) == "warranty") {
                                                    $impDetails[] = $row['warranty']; # store warrant period
                                                } else {
                                                    $impDetails[] = $row['warranty'] ." Warranty"; # store warrant period
                                                }
                                            }
                                            if ($row['specification'] != null) {
                                                foreach (explode(", ", $row['specification']) as $key => $value) {
                                                    $impDetails[] = $value; # store item specification
                                                }
                                            }
                                            if ($row['description']!= null) {
                                                foreach (explode(", ", $row['description']) as $key => $value) {
                                                    $impDetails[] = $value; # store item description
                                                }
                                            }
                                        ?>
                                        <h3><?php echo implode(" | ", $impDetails); ?></h3> <!-- display item content -->
                                    </div>
                                    <div class="item-price">
                                        <?php
                                            if ($row['discount'] == 0.0) {
                                                $price = number_format($row['price'], 2);
                                                ?><h2><?php echo $price; ?>LKR</h2><?php # display normal price
                                            } else {
                                                ?><p><s><?php echo $row['price']; ?> LKR</s></p><?php
                                                $price = number_format($row['discount'], 2);
                                                ?><h2><?php echo $price; ?> LKR</h2><?php # display discount price
                                            }
                                        ?>
                                    </div>
                                </a>
                                <div class="cart-item">
                                    <?php
                                        if (!empty($_REQUEST)) {
                                            $passValue = array();
                                            foreach ($_REQUEST as $key => $value) {
                                                if ($key != "cart") {
                                                    $passValue[$key] = $value;
                                                }
                                            }
                                            $getValues = (!empty($passValue) ? '&' : '') .http_build_query($passValue);
                                        } else {
                                            $getValues = '';
                                        }
                                        ?><a href="./?cart=<?php echo $row['productId'] .$getValues ."#$category-" .$count; ?>">Add To Cart <img src="/image/cart.png" alt=""></a><?php # display add to cart option
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                ?>
            </div>
        </div>
        <?php
    }
?>