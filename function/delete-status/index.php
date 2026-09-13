<?php
    function deleteStatus($conn, $dirName) {
    /* check all files */
        $sql = "SELECT
            `statusId`,
            `pathName`
        FROM
            `Status`
        ";
        $stmt = mysqli_query($conn, $sql);
    /* check is dir */
        if (!is_dir($dirName)) {
            mkdir($dirName, 0777, true); # create new status dir
        }
    /* create new file for invalid inputs */
        $createFile = fopen("$dirName/index.php", 'w'); # file pointer
        fwrite($createFile, "<?php\n/* redirect main page */\n\theader('Location: /');\n\texit();\n?>"); # write into this file
        fclose($createFile); # close the file
    /* check media files */
        $scanDir = scandir($dirName);
        $docFound = 0;
        if ($stmt == true) {
            for ($i=0; $i < count($scanDir); $i++) {
                if ($scanDir[$i] != '.' && $scanDir[$i] != ".." && $scanDir[$i] != "index.php" && $scanDir[$i] != "default.mp4") {
                    while ($row = mysqli_fetch_assoc($stmt)) {
                        if ($scanDir[$i] == $row['pathName']) {
                            $docFound = 1;
                            break;
                        }
                    }
                    if ($docFound == 0) {
                    /* The file has no references */
                        unlink("$dirName/" .$scanDir[$i]);
                    }
                }
            }
            reset($scanDir);
        }
    }
?>