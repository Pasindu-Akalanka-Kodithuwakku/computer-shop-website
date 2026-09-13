<?php
/* check all files */
    $sql = "SELECT
        `attachment`,
        `sender`,
        `receiver`
    FROM
        `Message`
    WHERE
        `clearBySender` = 1 AND
        `clearByReceiver` = 1
    ";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt == true) {
        while ($row = mysqli_fetch_assoc($stmt)) {
            $expAttach = explode("/ ", $row['attachment']); # all attachments
            for ($i=0; $i < count($expAttach); $i++) {
            /* check the file name already active anymore */
                $foundFile = 0; # define a variable
                if ($row['sender'] != "Admin") {
                /* sender is not admin */
                    $sql2 = "SELECT
                        `attachment`
                    FROM
                        `Message`
                    WHERE
                        (`clearBySender` = 0 OR
                        `clearByReceiver` = 0) AND
                        (`sender` = '$row[sender]' OR
                        `receiver` = '$row[sender]') AND
                        `trash` = 0
                    ";
                } else if ($row['receiver'] != "Admin") {
                /* receiver is not admin */
                    $sql2 = "SELECT
                        `attachment`
                    FROM
                        `Message`
                    WHERE
                        (`clearBySender` = 0 OR
                        `clearByReceiver` = 0) AND
                        (`sender` = '$row[receiver]' OR
                        `receiver` = '$row[receiver]') AND
                        `trash` = 0
                    ";
                }
                $stmt2 = mysqli_query($conn, $sql2);
                if ($stmt2 == true) {
                    while ($row2 = mysqli_fetch_assoc($stmt2)) {
                        $expAttach2 = explode("/ ", $row2['attachment']);
                        for ($x=0; $x < count($expAttach2); $x++) {
                        /* check if file is exists */
                            if (strtolower($expAttach[$i]) == strtolower($expAttach2[$x])) {
                                $foundFile = 1;
                                break;
                            }
                        }
                        if ($foundFile == 1) {
                            break;
                        }
                    }
                }
            /* not found a similar file */
                if ($foundFile == 0) {
                    if ($row['sender'] != "Admin") {
                        $path = "../../attachment/$row[sender]"; # set the path
                    } else if ($row['receiver'] != "Admin") {
                        $path = "../../attachment/$row[receiver]"; # set the path
                    }
                /* scan folder */
                    $scandir = scandir($path);
                    for ($x=0; $x < count($scandir); $x++) {
                        if ($scandir[$x] != '.' && $scandir[$x] != "..") {
                            if ($expAttach[$i] == $scandir[$x]) {
                                unlink($path .'/' .$scandir[$x]); # delete attachment file
                                break;
                            }
                        }
                    }
                    reset($scandir);
                }
            }
        }
    }
/* delete messages */
    $sql = "DELETE FROM `Message` WHERE `clearBySender` = 1 AND `clearByReceiver` = 1";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt == false) {
        die("Something went wrong.");
        exit();
    }
?>