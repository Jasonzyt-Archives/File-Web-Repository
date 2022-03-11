<?php

function output($path, $info) {
    header('Content-Type: ' . $info["mime"]);
    $_SESSION["current_background"] = [
        "path" => $path,
        "mime" => $info["mime"],
        "width" => $info[0],
        "height" => $info[1],
    ];
    readfile($path);
    exit();
}

chdir("../..");
session_start();
$width = $_GET['width'] ?? 1920;
$height = $_GET['height'] ?? 1080;
$aspectRatio = $width / $height;
$files = glob("assets/img/backgrounds/*");
shuffle($files);
foreach ($files as $file) {
    $imageInfo = getimagesize($file);
    $imageMime = $imageInfo["mime"];
    $imageWidth = $imageInfo[0];
    $imageHeight = $imageInfo[1];
    $imageAspectRatio = $imageWidth / $imageHeight;
    if ($aspectRatio > 1 && $imageAspectRatio > 1) {
        output($file, $imageInfo);
    } else if ($aspectRatio <= 1 && $imageAspectRatio <= 1) {
        output($file, $imageInfo);
    }
}