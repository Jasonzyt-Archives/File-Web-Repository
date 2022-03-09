<?php
include "config.php";

$fileExtensionIcon = [
    "c", "i", "s", "o", "out", "cxx", "cc", "c++", "C", "cpp", "inl", "hpp", "hxx", "h++", "h",
    "cs", "aspx", "resx", "json", "md", "py", "pyo", "pyw", "pyc", "pyd", "php", "phps", "lua",
    "go", "sln", "ttf", "otf", "woff", "woff2", "eot", "apk", "xapk", "css", "less", "js", "exe",
    "log", "doc", "docx", "docm", "dot", "dotx", "dotm", "jpg", "png", "jpeg", "bmp", "gif", "tif",
    "pcx", "tga", "exif", "fpx", "ai", "raw", "webp", "pdf", "ppt", "pptx", "pptm", "potx", "potm",
    "pot", "ppsx", "ppsm", "ppa", "ppam", "zip", "xml", "ini", "cfg", "config", "conf", "propreties",
    "ipa", "plist", "applescript", "ps1", "bat", "sh", "bash", "html", "htm", "dll", "lib", "txt",
    "gitignore", "mcpack", "mcaddon", "mcworld", "cer", "p12", "p7b", "pfx", "sst",
    //"xls", "xlsx", "xlsm", "xltx", "xltm", "xlt", "xlsb"
];
$previewFiles = [
    "doc", "docx", "docm", "dot", "dotx", "dotm", "pdf", "ppt", "pptx", "pptm", "potx", "potm",
    "pot", "ppsx", "ppsm", "ppa", "ppam", "xls", "xlsx", "xlsm", "xltx", "xltm", "xlt", "xlsb"
];
define("config", getConfig());

function getConfig(): object
{
    global $head;
    global $enableMarkdown;
    global $textOnTopLeft;
    global $fileDirectory;
    return (object)[
        "head" => $head,
        "enableMarkdown" => $enableMarkdown,
        "textOnTopLeft" => $textOnTopLeft,
        "fileDirectory" => $fileDirectory
    ];
}

function getWebsiteTitle($uri): ?string
{
    $h = curl_init();
    curl_setopt($h, CURLOPT_URL, $uri);
    curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($h, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($h, CURLOPT_MAXREDIRS, 10);
    curl_setopt($h, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($h, CURLOPT_TIMEOUT, 10);
    curl_setopt($h, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
    curl_setopt($h, CURLOPT_SSL_VERIFYPEER, false);
    $content = curl_exec($h);
    curl_close($h);
    if (mb_strpos($content, "<title>") !== false) {
        $title = mb_substr($content, mb_strpos($content, "<title>") + 7);
        return mb_substr($title, 0, mb_strpos($title, "</title>"));
    }
    return null;
}

function getLine($file, $line, $length = 4096)
{
    $returnTxt = null;
    $i = 1;
    $handle = @fopen($file, "r");
    if ($handle) {
        while (!feof($handle)) {
            $buffer = fgets($handle, $length);
            if ($line == $i) $returnTxt = $buffer;
            $i++;
        }
        fclose($handle);
    }
    return $returnTxt;
}

function getFileSizeStr($fileSize): string
{
    if ($fileSize >= 1024 && $fileSize < 1048576) {
        return round($fileSize / 1024, 2) . "KB";
    } else if ($fileSize >= 1048576 && $fileSize < 1073741824) {
        return round($fileSize / 1048576, 2) . "MB";
    } else if ($fileSize >= 1073741824 && $fileSize < 1099511627776) {
        return round($fileSize / 1073741824, 2) . "GB";
    } else {
        return $fileSize . "B";
    }
}

function getFolderSize($path): int
{
    $result = 0;
    $files = glob($path . "/*");
    foreach ($files as $file) {
        if (is_dir($file)) {
            $result += getFolderSize($file);
        } else {
            $result += filesize($file);
        }
    }
    return $result;
}

function getFullHostName(): string
{
    $result = "http://";
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        $result = "https://";
    } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $result = "https://";
    } else if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        $result = "https://";
    }
    return $result . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . '/';
}

class MySQL extends mysqli {

    /**
     * @throws Exception
     */
    public function __construct($host, $user, $pass, $db, $port = 3306)
    {
        parent::__construct($host, $user, $pass, $db, $port);
        if ($this->connect_error) {
            throw new Exception("MySQL Connection Error: " . $this->connect_error);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

}