<?php

session_start();
chdir("../..");
header("Content-Type: application/json");
$info = $_SESSION["current_background"];
if ($info == null) {
    exit("{}");
}
$json = json_decode(file_get_contents("assets/img/backgrounds/copyrights.json"));
$info["copyright"] = $json->{basename($info["path"])};
exit(json_encode($info, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));