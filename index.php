<?php

include "api/internal.php";

// ##########################  MAIN SCRIPT  ########################## //
// Set timezone
date_default_timezone_set("Asia/Shanghai");
// Request parameters
$dir = $_REQUEST['dir'] ?? "";
str_replace("\\", "/", $dir);
if ($dir != "" && $dir[mb_strlen($dir) - 1] == '/') {
    $dir = mb_substr($dir, 0, mb_strlen($dir) - 1);
}
?>
<html lang="zh-CN">

<head>
    <!-- META -->
    <meta charset="UTF-8"/>
    <meta name="description" content="<?php echo config->head->description; ?>"/>
    <meta name="keywords" content="<?php echo config->head->keywords; ?>"/>
    <meta name="author" content="JasonZYT"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- TITLE -->
    <title><?php echo config->head->title; ?></title>
    <!-- ICON -->
    <link rel="shortcut icon" href="<?php echo config->head->icon; ?>"/>
    <link rel="bookmark" href="<?php echo config->head->icon; ?>"/>
    <!-- LINK-CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<?php include "assets/svg/icon.svg" ?>
<nav id="navbar" style="display:block;">
    <div class="row">
        <div class="container">
            <div class="logo unit">
                <span><?php echo config->textOnTopLeft; ?></span>
            </div>
            <ul class="nav-menu">
                <?php
                // Path on the top
                if ($dir != "") {
                    echo '<li><a href="/"><span class="i18n">Home</span><svg><use xlink:href="#AngleBracket-R" /></svg></a></li>';
                } else {
                    echo '<li><a class="i18n" style="margin-top:0.15em;color:#000;">Home</a></li>';
                }
                $dirs = explode("/", $dir);
                $curDir = $dirs[count($dirs) - 1];
                array_pop($dirs);
                $path = "";
                $parentDir = $dir == "" ? null : "/";
                if ($curDir != "") {
                    foreach ($dirs as $d) {
                        if ($d == "") {
                            continue;
                        }
                        $path .= $d;
                        echo '<li><a href="/?dir=' . $path . '">' . $d . '<svg><use xlink:href="#AngleBracket-R"></use></svg></a></li>';
                        $parentDir = $path;
                        $path .= '/';
                    }
                    echo '<li><a style="margin-top:0.15em;color:#000;">' . $curDir . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
<section id="list" class="services-section spad">
    <?php
    $path = config->fileDirectory . $dir;
    if ($dir != "" && (!file_exists($path) || !is_dir($path))) {
        echo <<<EOT
        <div class="not-found">
              <svg><use xlink:href="#Warning"/></svg>
              <span class="i18n">Directory&nbsp;<b>$dir</b>&nbsp;NOT FOUND!</span>
        </div>
EOT;
        goto footer;
    }
    ?>
    <div class="container">
        <div id="dir-list-header">
            <div class="row" style="font-family:Consolas,sans-serif">
                <div class="i18n file-name col-md-7 col-sm-6 col-xs-9">File</div>
                <div class="i18n file-size col-md-2 col-sm-2 col-xs-3 text-right">Size</div>
                <div class="i18n last-edit-time col-md-3 col-sm-4 hidden-xs text-right">Modified</div>
            </div>
        </div>
        <ul id="dir-list" class="nav nav-pills nav-stacked">
            <?php
            function directory($cur)
            {
                global $fileExtensionIcon;
                global $previewFiles;
                $path = config->fileDirectory . $cur;
                $all = array_diff(scandir($path), [".", ".."]);
                $dirs = [];
                $files = [];
                foreach ($all as $file) {
                    $filePath = $path . "/" . $file;
                    if (is_dir($filePath)) {
                        $dirs[] = $file;
                    } else {
                        $files[] = $file;
                    }
                }
                global $parentDir;
                if ($parentDir != null) {
                    echo <<<EOT
                <li data-name=".." data-href="?dir=$parentDir">
                    <a href="?dir=$parentDir" class="clearfix" data-name="..">
                        <div class="row">
                            <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                <svg><use xlink:href="#Prev-Folder"/></svg>
                                ..
                            </span>
                        </div>
                    </a>
                </li>
EOT;
                }
                foreach ($dirs as $d) {
                    $realPath = $path . '/' . $d;
                    $lastEditTimeStamp = filemtime($realPath);
                    $lastEditTime = date("Y-m-d H:i:s", $lastEditTimeStamp);
                    $size = getFileSizeStr(getFolderSize($realPath));
                    $href = "?dir=" . ($cur == "" ? $cur : "$cur/") . $d;
                    echo <<<EOT
                <li data-name="$d" data-href="$href">
                    <a href="$href" class="clearfix" data-name="$d">
                        <div class="row">
                            <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                <svg><use xlink:href="#Folder"/></svg>
                                $d
                            </span>
                            <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                $size
                            </span>
                            <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                $lastEditTime
                            </span>
                        </div>
                    </a>
                </li>
EOT;
                }
                foreach ($files as $file) {
                    $realPath = $path . '/' . $file;
                    $lastEditTimeStamp = filemtime($realPath);
                    $lastEditTime = date("Y-m-d H:i:s", $lastEditTimeStamp);
                    $size = getFileSizeStr(filesize($realPath));
                    $extOri = mb_substr(mb_strrchr($file, '.'), 1);
                    $ext = mb_strtolower($extOri);
                    $name = $file;
                    $href = "download.php?path=$cur/$file";
                    $icon = ".$ext";
                    if ($ext == "url") {
                        $uri = getLine($realPath, 1);
                        $displayName = getLine($realPath, 2);
                        if ($displayName == null) {
                            $uriArray = parse_url($uri);
                            $title = getWebsiteTitle($uri);
                            $displayUri = $uriArray["host"] . $uriArray["path"];
                            if ($title) {
                                $displayUri .= ':' . $title;
                            }
                            $name = $displayUri;
                        } else {
                            $name = $displayName;
                        }
                        $href = $uri;
                    }

                    if (!in_array($ext, $fileExtensionIcon)) {
                        $icon = "Unknown";
                    }

                    if (in_array($ext, $previewFiles)) {
                        $href = "preview.php?path=$cur/$file";
                    }
                    echo <<<EOT
                <li data-name="$name" data-href="$href">
                    <a href="$href" class="clearfix" data-name="$name">
                        <div class="row">
                            <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                <svg><use xlink:href="#$icon"/></svg>
                                $name
                            </span>
                            <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                $size
                            </span>
                            <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                $lastEditTime
                            </span>
                        </div>
                    </a>
                </li>
EOT;
                }
            }
            directory($dir);
            ?>
        </ul>
    </div>
</section>
<?php
footer:
echo <<<EOT
<footer>
    <p>
        <a href="https://github.com/Jasonzyt/File-Web-Repository">File-Web-Repository</a>&nbsp;v2.0.0
    </p>
    <p>&copy;2020-2022 All Rights Reserved.</p>
    <p>Powered By JasonZYT</p>
    <p id="hitokoto"></p>
    <script src="https://v1.hitokoto.cn/?encode=js&amp;select=%23hitokoto" defer=""></script>
</footer>
EOT;
?>

</body>

<script type="text/javascript" src="assets/js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.nav.js"></script>
<script type="text/javascript" src="assets/js/i18n.js"></script>
<script>
    <?php
    if (isset($_COOKIE["lang"])) echo "langCode = '" . $_COOKIE["lang"] . "';";
    ?>
    fullLang = <?php include "I18N.json"; ?>;
    do_i18n();
</script>

</html>