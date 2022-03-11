<?php

session_start();
header("Content-Type: application/json");
$info = $_SESSION["current_background"];
if ($info == null) {
    exit("{}");
}
exit(json_encode($info, JSON_UNESCAPED_SLASHES));