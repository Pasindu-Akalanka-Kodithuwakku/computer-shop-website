<?php
    function chatReply($conn, $chatId, $itemId, $spaces) {
        global $count;
        $sql = "SELECT
            `chatId`,
            `description`,
            `sender`,
            `sentDate`
        FROM
            `ItemChat`
        WHERE
            `trash` = 0 AND
            `itemId` = '$itemId' AND
            `reply` = '$chatId'
        ORDER BY
            `chatId` ASC
        ";
        $stmt = mysqli_query($conn, $sql);
        if ($stmt == true) {
            while ($row = mysqli_fetch_assoc($stmt)) {
                $count++;
                echo "<span class='msg-border' id='$count'>";
                for ($i=0; $i < $spaces; $i++) {
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; # create tab space
                }
                echo "<span class='pro-pic'>";
                if (substr($row['sender'], 0, 2) == "AM") {
                    $sql2 = "SELECT
                        `adminId`,
                        `firstName`,
                        `lastName`,
                        `image`
                    FROM
                        `Admin`
                    WHERE
                        `adminId` = '$row[sender]'
                    LIMIT 1
                    ";
                } else {
                    $sql2 = "SELECT
                        `memberId`,
                        `firstName`,
                        `lastName`,
                        `image`
                    FROM
                        `Member`
                    WHERE
                        `memberId` = '$row[sender]'
                    LIMIT 1
                    ";
                }
                $stmt2 = mysqli_query($conn, $sql2);
                if ($stmt2 == true) {
                /* check is sender a member */
                    if ($row['sender'] != '') {
                    /* sender is a member */
                        $row2 = mysqli_fetch_assoc($stmt2);
                        if ($row2['image'] != '') {
                            echo "<img src='data:image; base64, $row2[image]' alt=''>";
                        } else {
                            echo "<img src='/image/logo.jpg' alt=''>";
                        }
                    } else {
                    /* sender is not a member */
                        echo "<img src='/image/logo.jpg' alt=''>";
                    }
                }
                echo "</span>";
                echo "<span class='msg-cont'>";
            /* check is sender a member */
                if ($row['sender'] != '') {
                /* sender is a member */
                    echo "<h3>";
                    echo $row2['firstName'] .' ' .$row2['lastName'];
                    if (substr($row['sender'], 0, 2) == "AM") {
                        echo " (<font style='color: #808080;'>Admin</font>)";
                    }
                    echo "</h3>";
                } else {
                /* sender is not a member */
                    echo "<h3>Anonymous</h3>";
                }
                echo "<p>" .base64_decode($row['description']) ."</p>";
                echo "<h5>";
                echo "<a href='/product/?index=$_REQUEST[index]&replyId=$row[chatId]#$count'>Reply ↩️</a>"; # set reply button
                switch (true) {
                    case isset($_SESSION['memberId']) && $row['sender'] != '' &&  $_SESSION['memberId'] == $row2['memberId']:
                    case isset($_SESSION['adminId']):
                        echo "<a href='/product/?index=$_REQUEST[index]&deleteId=$row[chatId]#$count' id='del-btn'>Delete 🗑️</a>"; # set delete button
                        break;
                }
                echo $row['sentDate'];
                echo "</h5>";
                echo "</span>";
                echo "</span>";
            /* check is reply of previously replied messages */
                $sql3 = "SELECT
                    `reply`
                FROM
                    `ItemChat`
                WHERE
                    `trash` = 0 AND
                    `itemId` = '$itemId' AND
                    `reply` = '$row[chatId]'
                LIMIT 1
                ";
                $stmt3 = mysqli_query($conn, $sql3);
                if ($stmt3 == true) {
                    while ($row3 = mysqli_fetch_assoc($stmt3)) {
                        chatReply($conn, $row3['reply'], $itemId, ($spaces + 1));
                    }
                }
            }
        }
    }
?>