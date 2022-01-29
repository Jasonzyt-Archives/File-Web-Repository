<?php

include "config.php";

function getFilePath(): string {
    global $fileDirectory;
    return $fileDirectory;
}

$path = getFilePath() . $_REQUEST['path'];
if (file_exists($path)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($path));
    header('Content-Length: ' . filesize($path));
    readfile($path);
}
else {
    echo "File not found, <a href=\"index.php\">go back to home</a>";
}