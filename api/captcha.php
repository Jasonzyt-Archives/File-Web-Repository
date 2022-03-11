<?php

const imgWidth = 100;
const imgHeight = 40;
const imgBgColor = [255, 255, 255];
const codeLength = 4;
const fontSize = 18;
const minLines = 3;
const maxLines = 8;
const minDots = 200;
const maxDots = 500;

session_start();

$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$chars_length = strlen($chars);
$code = '';
for ($i = 0; $i < codeLength; $i++) {
    $code .= $chars[rand(0, $chars_length - 1)];
}
$_SESSION['captcha'] = $code;

$image = imagecreate(imgWidth, imgHeight);
$backgroundColor = imagecolorallocate($image, imgBgColor[0], imgBgColor[1], imgBgColor[2]);
imagefill($image, 0, 0, $backgroundColor);
// Put the code
$perChar = imgWidth / codeLength;
for ($i = 0; $i < codeLength; $i++) {
    $ch = $code[$i];
    $fontColor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));
    imagefttext($image, fontSize, 0,
        rand($perChar * $i, $perChar * ($i + 1) - fontSize),
        rand(fontSize, 40), $fontColor,
        '../assets/fonts/Consola.ttf', $ch);
    //imagechar($image, 5, rand(20 * $i, 20 * ($i + 1) - 10), rand(5, 20), $ch, $fontColor);
}
// Draw lines
for ($i = 0; $i < minLines + rand(0, maxLines - minLines); $i++) {
    $lineColor = imagecolorallocate($image, rand(0, 254), rand(0, 255), rand(0, 255));
    imageline($image, rand(0, imgWidth), rand(0, imgHeight),
        rand(0, imgWidth), rand(0, imgHeight), $lineColor);
}
// Draw dots
for ($i = 0; $i < minDots + rand(0, maxDots - minDots); $i++) {
    $dotColor = imagecolorallocate($image, rand(0, 254), rand(0, 255), rand(0, 255));
    imagesetpixel($image, rand(0, imgWidth), rand(0, imgHeight), $dotColor);
}
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
