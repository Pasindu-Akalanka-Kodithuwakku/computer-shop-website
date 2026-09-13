<?php
    function addCart($conn, $id) {
        $sql = "SELECT
            `productId`,
            `productName`
        FROM
            `Product`
        WHERE
            `productId` = '$id'
        LIMIT 1
        ";
        $stmt = mysqli_query($conn, $sql);
        $foundItem = 0;
        if ($stmt == true) {
            if (mysqli_num_rows($stmt) == 1) {
                foreach ($_SESSION['cart'] as $key => $value) {
                    if ($key == $id) {
                        $foundItem = 1;
                        break;
                    }
                }
                if ($foundItem == 0) {
                    $_SESSION['cart'][$id] = 1;
                }
            }
        }
    }
/* count items in cart */
    function countCart() {
        $items = 0;
        foreach ($_SESSION['cart'] as $key => $value) {
            $items = $items + $value;
        }
        return $items;
    }
?>