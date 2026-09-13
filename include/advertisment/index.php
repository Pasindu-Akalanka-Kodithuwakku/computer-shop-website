<?php
    date_default_timezone_set("Asia/Colombo");
    /* check about status table */
    $sql = "SELECT `statusId`, `lastUpdateDate`, `duration` FROM `Status` WHERE `trash` = 0";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt == true) {
        while ($row = mysqli_fetch_assoc($stmt)) {
        /* create times */
            $updateDate = date_create($row['lastUpdateDate']);
            $today = date_create(date("Y-m-d H:i:s"));
            $interval = date_diff($updateDate, $today);
            $overHours = $interval -> h + ($interval -> d * 24);
            $overMinutes = $interval -> i;
        /* check duration time */
            if (($overHours > $row['duration']) || ($overHours == $row['duration'] && $overMinutes > 0)) {
                $sql2 = "DELETE FROM `Status` WHERE `statusId` = '$row[statusId]'";
                $stmt2 = mysqli_query($conn, $sql2);
                if ($stmt2 == false) {
                    die("Sql query executing failed! try again.");
                }
            }
        }
    }
    /* check about update table */
    $sql = "SELECT `updateId`, `lastUpdateDate` FROM `Update` WHERE `trash` = 0";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt == true) {
        while ($row = mysqli_fetch_assoc($stmt)) {
        /* create times */
            $nextDate = date("Y-m-d", strtotime($row['lastUpdateDate'] ."+1 day"));
            $today = date("Y-m-d");
        /* check duration time */
            if ($today >= $nextDate) {
                $sql2 = "DELETE FROM `Update` WHERE `updateId` = '$row[updateId]'";
                $stmt2 = mysqli_query($conn, $sql2);
                if ($stmt2 == false) {
                    die("Sql query executing failed! try again.");
                }
            }
        }
    }
?>