<html lang="zh-CN">

<head>
    <?php
    if (!file_exists('HEAD.json')) {
        $array = array(
            'title' => 'File Repository',
            'icon' => './assets/img/icon.ico',
            'meta' => array(
                'description' => 'File-Repository',
                'keywords' => 'File-Repository,Webdisk'
            )
        );
    }
    else {
        $json = json_decode(file_get_contents("HEAD.json"),true);
        if (isset($json['title'])) {
            $json_t = $json['title'];
        }
        else {
            $json_t = 'File Repository';
        }
        if (isset($json['icon'])) {
            $json_i = $json['icon'];
        }
        else {
            $json_i = './assets/img/icon.ico';
        }
        if (isset($json['meta']['description'])) {
            $json_d = $json['meta']['description'];
        }
        else {
            $json_d = 'File-Repository';
        }
        if (isset($json['meta']['keywords'])) {
            $json_k = $json['meta']['keywords'];
        }
        else {
            $json_k = 'File-Repository,Webdisk';
        }
        $array = array(
            'title' => $json_t,
            'icon' => $json_i,
            'meta' => array(
                'description' => $json_d,
                'keywords' => $json_k
            )
        );
    }
    $title = $array['title'];
    $description = $array['meta']['description'];
    $keywords = $array['meta']['keywords'];
    $icon = $array['icon'];
    ?>
    <!-- META -->
    <meta charset="UTF-8" />
    <meta name="description" content=<?php echo '"' . $description . '"';?> />
    <meta name="keywords" content=<?php echo '"' . $keywords . '"';?> />
    <meta name="author" content="JasonZYT" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- TITLE -->
    <title><?php echo $title;?></title>
    <!-- ICON -->
    <link href=<?php echo '"' . $icon . '"';?> rel="shortcut icon">
    <link rel="bookmark" href=<?php echo '"' . $icon . '"';?> />
    <!-- LINK-CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font.css">
    <link rel="stylesheet" href="assets/css/style.min.css">
</head>

<body style="overflow: visible;background:#f6f6f6;">
    <?php include "assets/svg/icon.svg" ?>
    <nav id="navbar" style="display:block;">
        <div class="row">
            <div class="container">
                <div class="logo unit">
                    <!-- <img src="" alt="logo"> -->
                    <span>SKYTown</span>
                </div>
                <ul class="nav-menu">
                    <?php
					function convertUriQuery($query)
			        {
				        $queryParts = explode('&', $query);
				        $params = array();
				        foreach ($queryParts as $param) {
					        $item = explode('=', $param);
					        $params[$item[0]] = $item[1];
				        }
				    return $params;
			        }
			        $host = $_SERVER['HTTP_HOST'];
			        $lic = $_SERVER['REQUEST_SCHEME'];
			        $port = $_SERVER['SERVER_PORT'];
			        $uri = $lic . "://" . $host;
                    $query = $_SERVER['QUERY_STRING'];
			        $parUri = convertUriQuery($query);
                    $parameter3 = urldecode($parUri["dir"]);
                    $parameter2 = str_replace("\\","/",$parameter3);
                    $parameter1 = str_replace("//","/",$parameter2);
                    $parLen1 = mb_strlen($parameter1);
			        if (mb_substr($parameter1,$parLen-1,1)=="/") {
                        $parameter = mb_substr($parameter1,0,$parLen-1);
                    }
                    else {
                        $parameter = $parameter1;
                    }
                    $parLen = mb_strlen($parameter);
                    $parLastSlashPos = mb_strrpos($parameter,"/");
                    $currentDirName = mb_substr($parameter,$parLastSlashPos+1,$parLen-1);
                    if ($parLastSlashPos==false) {
                        $currentDirName = $parameter;
                    }
                    if (file_exists("./Filedir/" . $parameter)==false){
                        echo '<script language="javascript"> window.location.href="error/FILE404.php?dir=' . $parameter . '"</script>';
                    }
                    if ($parLen==0) {
				        echo '<li><a href="#">首页</a></li>';
                    }
			        else {
                        echo '<li><a href="' . $uri . '/index.php">首页</a><svg><use xlink:href="#AngleBracket-R" /></svg></li>';
				        for ($i=0;$i<=$parLen-1;$i++) {
				    	    if (mb_substr($parameter,$i,1)=="/") {
                                $dir = mb_substr($parameter,0,$i);
                                if (mb_substr($dir,0,1)!="/") {
                                    $dirSlash = "/" . $dir;
                                }
                                else {
                                    $dirSlash = $dir;
                                }
                                $lastSlashPos = mb_strrpos($dirSlash,"/");
                                $dirLen = mb_strlen($dirSlash);
                                $dirName = mb_substr($dirSlash,$lastSlashPos+1,$dirLen-1);
				    		    echo '<li><a href="' . $uri . '/index.php?dir=' . $dir . '">' . $dirName . '</a><svg><use xlink:href="#AngleBracket-R"></use></svg></li>';
                            }
                        }
				    	echo '<li><a style="color:#707070;">' . $currentDirName . '</a></li>';
			        }
			        ?>
                </ul>
            </div>
        </div>
    </nav>
    <section id="list" class="services-section spad">
        <div class="container">
            <div id="dir-list-header">
                <div class="row">
                    <div class="file-name col-md-7 col-sm-6 col-xs-9">文件</div>
                    <div class="file-size col-md-2 col-sm-2 col-xs-3 text-right">大小</div>
                    <div class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">上次修改时间</div>
                </div>
            </div>
            <ul id="dir-list" class="nav nav-pills nav-stacked">
                <?php
                function isEmptyFolder($path)
                {
                    $array = array_diff(scandir($path),array('..','.'));
                    return empty($array);
                }
                function getLine($file, $line, $length = 4096){
                    $returnTxt = null; 
                    $i = 1; 
                    $handle = @fopen($file, "r");
                    if ($handle) {
                        while (!feof($handle)) {
                            $buffer = fgets($handle, $length);
                            if($line == $i) $returnTxt = $buffer;
                            $i++;
                        }
                        fclose($handle);
                    }
                    return $returnTxt;
                }                
                function getFileSizeStr($fileSize) {
                    if ($fileSize >= 1024 && $fileSize < 1048576)
                    {
                        return round($fileSize / 1024,1) . "KB";
                    }
                    else if ($fileSize >= 1048576 && $fileSize < 1073741824)
                    {
                        return round($fileSize / 1048576,1) . "MB";
                    }
                    else if ($fileSize >=1073741824  && $fileSize < 1099511627776)
                    {  
                        return round($fileSize / 1073741824,2) . "GB";
                    }
                    else
                    {
                        return $fileSize . "Byte";
                    }
                }
                function ergodicDir($inDir) {
                    $dir = "./Filedir/" . $inDir;
                    $files = array_diff(scandir($dir),array('..','.'));
                    $filesArray = array(
                            "DIR" => array(),
                            "FILE" => array()
                    );
                    foreach ($files as $file) {
                        if (is_dir($dir . "/" . $file)) {
                            array_push($filesArray["DIR"],$file);
                        }
                        else {
                            array_push($filesArray["FILE"],$file);
                        }
                    }
                    foreach ($filesArray["DIR"] as $file) {
                        $lastEditTimeStamp = filemtime ($dir . '/' . $file);
                        $lastEditTime = date("Y-m-d H:i:s",$lastEditTimeStamp);
                        if ($inDir == "") {
                            echo <<<EOF
                                <li data-name="$file" data-href="?dir=$file">
                                    <a href="?dir=$file" class="clearfix" data-name="$file">
                                        <div class="row">
                                            <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                                <svg><use xlink:href="#Folder"/></svg>
                                                $file
                                            </span>
                                            <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                                -
                                            </span>
                                            <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                                $lastEditTime
                                            </span>
                                        </div>
                                    </a>
                                </li>
EOF;
                        }
                        else {
                            echo <<<EOF
                                <li data-name="$file" data-href="?dir=$inDir/$file">
                                    <a href="?dir=$inDir/$file" class="clearfix" data-name="$file">
                                        <div class="row">
                                            <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                                <svg><use xlink:href="#Folder"/></svg>
                                                $file
                                            </span>
                                            <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                                -
                                            </span>
                                            <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                                $lastEditTime
                                            </span>
                                        </div>
                                    </a>
                                </li>
EOF;
                        }
                    }
                    foreach ($filesArray["FILE"] as $file) {
                        $lastEditTimeStamp = filemtime ($dir . '/' . $file);
                        $lastEditTime = date("Y-m-d H:i:s",$lastEditTimeStamp);
                        $fileSize = filesize($dir . "/" . $file);
                        $FS = getFileSizeStr($fileSize);
                        $FE2 = mb_substr(mb_strrchr($file, '.'), 1);
                        $FE = mb_strtolower($FE2);
                        if ( // 处理拓展名区分大小写的文件(Linux)
                        $FE2 == "C"
                        ) {
                            echo <<<EOF
                                <li data-name="$file" data-href="?dir=$inDir/$file">
                                    <a href="$dir/$file" class="clearfix" data-name="$file">
                                        <div class="row">
                                            <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                                <svg><use xlink:href="#.$FE2"/></svg>
                                                $file
                                            </span>                                                <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                                 $FS
                                            </span>
                                            <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                                $lastEditTime
                                            </span>
                                        </div>
                                    </a>
                                </li>
EOF;
                        }
                        elseif ( // 其他文件
                        $FE=="c"||$FE=="i"||$FE=="s"||$FE=="o"||$FE=="out"||$FE=="cxx"||$FE=="cc"||$FE=="c++"||$FE=="C"||$FE=="cpp"||
                        $FE=="inl"||$FE=="hpp"||$FE=="hxx"||$FE=="h++"||$FE=="h"||$FE=="cs"||$FE=="aspx"||$FE=="resx"||$FE=="json"||$FE=="md"||
                        $FE=="py"||$FE=="pyo"||$FE=="pyw"||$FE=="pyc"||$FE=="pyd"||$FE=="php"||$FE=="phps"||$FE=="lua"||$FE=="go"||$FE=="sln"||
                        $FE=="ttf"||$FE=="otf"||$FE=="woff"||$FE=="woff2"||$FE=="eot"||$FE=="apk"||$FE=="xapk"||$FE=="css"||$FE=="less"||$FE=="js"||
                        $FE=="exe"||$FE=="log"||$FE=="doc"||$FE=="docx"||$FE=="docm"||$FE=="dot"||$FE=="dotx"||$FE=="dotm"||$FE=="jpg"||$FE=="png"||
                        $FE=="jpeg"||$FE=="bmp"||$FE=="gif"||$FE=="tif"||$FE=="pcx"||$FE=="tga"||$FE=="exif"||$FE=="fpx"||$FE=="ai"||$FE=="raw"||
                        $FE=="webp"||$FE=="pdf"||$FE=="ppt"||$FE=="pptx"||$FE=="pptm"||$FE=="potx"||$FE=="potm"||$FE=="pot"||$FE=="ppsx"||$FE=="ppsm"||
                        $FE=="ppa"||$FE=="ppam"||$FE=="zip"||$FE=="xml"||$FE=="ini"||$FE=="cfg"||$FE=="config"||$FE=="conf"||$FE=="propreties"||$FE=="ipa"||
                        $FE=="plist"||$FE=="applescript"||$FE=="ps1"||$FE=="bat"||$FE=="sh"||$FE=="bash"||$FE=="html"||$FE=="htm"||$FE=="dll"||$FE=="lib"||
                        $FE=="txt"||$FE=="gitignore"||$FE=="mcpack"||$FE=="mcaddon"||$FE=="mcworld"||$FE=="cer"||$FE=="p12"||$FE=="p7b"||$FE=="pfx"||$FE=="sst"
                        ) {
                            echo <<<EOF
                                <li data-name="$file" data-href="?dir=$inDir/$file">
                                    <a href="$dir/$file" class="clearfix" data-name="$file">
                                        <div class="row">
                                            <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                                <svg><use xlink:href="#.$FE"/></svg>
                                                $file
                                            </span>
                                            <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                                $FS
                                            </span>
                                            <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                                $lastEditTime
                                            </span>
                                        </div>
                                    </a>
                                </li>
EOF;
                        }
                        elseif ($FE == "url") { // 处理URL文件
                            $uri = getLine($dir . "/" . $file,1);
                            $displayName = getLine($dir . "/" . $file,2);
                            if ($displayName == null) {
                                $uriArray = parse_uri($uri);
								$displayUri = $uriArray["host"] . $uriArray["path"];
                                echo <<<EOF
                                    <li data-name="$file" data-href="?dir=$inDir/$file">
                                        <a href="$uri" class="clearfix" data-name="$file">
                                            <div class="row">
                                                <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                                    <svg><use xlink:href="#Uri"/></svg>
                                                    $displayUri
                                                </span>
                                                <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                                    -
                                                </span>
                                                <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                                    $lastEditTime
                                                </span>
                                            </div>
                                        </a>
                                    </li>
EOF;
                            }
                            else {
                                echo <<<EOF
                                    <li data-name="$file" data-href="?dir=$inDir/$file">
                                        <a href="$uri" class="clearfix" data-name="$file">
                                            <div class="row">
                                                <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                                    <svg><use xlink:href="#Uri"/></svg>
                                                    $displayName
                                                </span>
                                                <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                                    -
                                                </span>
                                                <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                                    $lastEditTime
                                                </span>
                                            </div>
                                        </a>
                                    </li>
EOF;
                            }
                        }
                        else { // 处理未知(无图标)文件
                            echo <<<EOF
                                <li data-name="$file" data-href="?dir=$inDir/$file">
                                    <a href="$dir/$file" class="clearfix" data-name="$file">
                                        <div class="row">
                                            <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                                <svg><use xlink:href="#Unknown"/></svg>
                                                $file
                                            </span>
                                            <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                                $FS
                                            </span>
                                            <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                                $lastEditTime
                                            </span>
                                        </div>
                                    </a>
                                </li>
EOF;
                        }
                    }
                }
                if (isEmptyFolder("./Filedir/" . $parameter)) {
                    echo '<h1 style="text-align: center;font-family: BlackItalic;">This is an empty folder</h1>';
                }
                else {
                    ergodicDir($parameter);
                }
                ?>
            </ul>
        </div>
    </section>
    <footer>
        <p>
            <?php include 'Edition'; ?>
        </p>
        <p>Copyright ©2020-2021</p>
        <p>SKYTown Server All Rights Reserved.</p>
        <p>Power By JasonZYT</p>
        <p id="hitokoto"></p>
        <script src="https://v1.hitokoto.cn/?encode=js&amp;select=%23hitokoto" defer=""></script>
    </footer>

    <script type="text/javascript" src="assets/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.nav.js"></script>

</body>

</html>