<?php
    function deleteFolder($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != "..") {
                    if (filetype($dir .'/' .$object) == "dir") {
                        deleteFolder($dir .'/' .$object);
                    } else {
                        unlink($dir .'/' .$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
?>