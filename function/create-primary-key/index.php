<?php
    function createPrimaryKey($conn, $table, $column, $firstWord) {
        $sql = "SELECT `$column` FROM `$table` ORDER BY `$column` DESC LIMIT 1";
        $stmt = mysqli_query($conn, $sql);
        if (mysqli_num_rows($stmt) == 1) {
            $row = mysqli_fetch_assoc($stmt);
            $id = $firstWord .str_pad((substr($row[$column], 2) + 1), 4, 0, STR_PAD_LEFT);
        } else {
            $id = $firstWord ."0001";
        }
        return $id;
    }
?>