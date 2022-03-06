<?php

include "config.php";

function getHead(): object {
    global $head;
    return $head;
}

function getIconPath(): string {
    return getHead()->icon;
}

function getTextOnTopLeft(): string {
    global $textOnTopLeft;
    return $textOnTopLeft;
}

function getFileDir(): string {
    global $fileDirectory;
    return $fileDirectory;
}

function getFullHostName(): string {
    $result = "http://";
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        $result = "https://";
    } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
        $result = "https://";
    } else if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        $result = "https://";
    }
    return $result . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . '/';
}

$path = $_REQUEST["path"] ?? null;

?>
<html lang="zh-CN">

<head>
    <!-- META -->
    <meta charset="UTF-8" />
    <meta name="description" content="Preview file" />
    <meta name="author" content="JasonZYT" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- TITLE -->
    <title>Preview - <?php getHead()->title ?></title>
    <!-- ICON -->
    <link rel="shortcut icon" href="<?php echo getIconPath(); ?>" />
    <link rel="bookmark" href="<?php echo getIconPath(); ?>" />
    <!-- LINK-CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include "assets/svg/icon.svg"; ?>
    <nav id="navbar" style="display:block;">
        <div class="row">
            <div class="container">
                <div class="logo unit">
                    <span><?php echo getTextOnTopLeft(); ?></span>
                </div>
                <ul class="nav-menu">
                    <?php
                    // Path on the top
                    if ($path != "") {
                        echo '<li><a href="/"><span class="i18n">Home</span><svg><use xlink:href="#AngleBracket-R" /></svg></a></li>';
                    }
                    else {
                        echo '<li><a class="i18n" style="margin-top:0.15em;color:#000;">Home</a></li>';
                    }
                    $dirs = explode("/", $path);
                    $curDir = $dirs[count($dirs) - 1];
                    array_pop($dirs);
                    $i = 0;
                    if ($curDir != "") {
                        foreach ($dirs as $d) {
                            if ($d == "") {
                                continue;
                            }
                            $i += strlen($d) + 1;
                            echo '<li><a href="/?dir=' . substr($path, 0, $i - 1) . '">' . $d . '<svg><use xlink:href="#AngleBracket-R"></use></svg></a></li>';
                        }
                        echo '<li><a style="margin-top:0.15em;color:#000;">' . $curDir . '</a></li>';
                    }
                    ?>
                </ul>
                <div class="download">
                    <a href="download.php?path=<?php echo $path; ?>">
                        <svg><use xlink:href="#Download" /></svg><span class="i18n">Download</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <section class="services-section spad">
        <?php
        $realPath = getFileDir() . $path;
        if ($path == "" || !file_exists($realPath) || is_dir($realPath)) {
            echo <<<EOT
        <div class="not-found">
            <svg><use xlink:href="#Warning"/></svg>
            <span class="i18n">File&nbsp;<b>$path</b>&nbsp;NOT FOUND!</span>
        </div>
EOT;
            goto footer;
        }
        ?>
        <iframe src="http://www.xdocin.com/xdoc?_func=to&_format=html&_cache=1&_xdoc=<?php echo getFullHostName() . getFileDir() . $path; ?>&embedded=true" style="width:100%;height:100%;"></iframe>
    </section>
    <?php
    footer:
    echo<<<EOT
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

<script type="text/javascript" src="assets/js/i18n.js"></script>
<script>
    <?php
    if (isset($_COOKIE["lang"])) echo "langCode = '" . $_COOKIE["lang"] . "';";
    ?>
    fullLang = <?php include "I18N.json"; ?>;
    do_i18n();
</script>

</html>
