<?php
// Head
$head = (object) [
    "title" => "File Repository",
    "icon" => "assets/img/icon.ico",
    "description" => "File Repository",
    "keywords" => "File-Repository,WebDisk,Jasonzyt"
];
$enableMarkdown = true;
$textOnTopLeft = "File Repository";
$fileDirectory = "Filedir/";
$encryptPasswordFrontend = true; // It is recommended to set to true, especially if https is not used.
// Account Database
$dbConfig = (object) [
    "host" => "localhost",
    "user" => "root",
    "password" => "password",
    "database" => "file_repository"
];
