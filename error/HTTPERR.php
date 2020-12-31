<html lang="zh-CN">

<head>
    <?php
    if (!file_exists('../HEAD.json')) {
        $array = array(
            'title' => 'File Repository',
            'icon' => '../assets/img/icon.ico',
            'meta' => array(
                'description' => 'File-Repository',
                'keywords' => 'File-Repository,Webdisk'
            )
        );
    }
    else {
        $json = json_decode(file_get_contents("../HEAD.json"),true);
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
            $json_i = '../assets/img/icon.ico';
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
    <nav style="display:block;">
        <div class="row">
            <div class="container">
                <div class="logo unit">
                    <!-- <img src="" alt="logo"> -->
                    <span>SKYTown</span>
                </div>
            </div>
        </div>
    </nav>
    <section class="services-section spad">
        <div class="container">
            <h1 id="h1" style="font-family: BlackItalic;">HTTP-REQUEST ERROR 404</h1>
            <h2 id="h2" style="font-family: BlackItalic;">File/Folder NOT Exist</h2>
            <p style="font-family: BlackItalic;font-size:24px;">Please check the URL!!!</p>
            <p style="font-family: BlackItalic;font-size:24px;"><a href="../index.php">Click me to back to homepage</a></p>
        </div>
        <?php
		function convertUrlQuery($query)
		{
			$queryParts = explode('&', $query);
	        $params = array();
			foreach ($queryParts as $param) {
                $item = explode('=', $param);
		        $params[$item[0]] = $item[1];
	        }
		    return $params;
		}
		$query = $_SERVER['QUERY_STRING'];
        $array = convertUrlQuery($query);
        $errCode = $array["errcode"];
        
		?>
    </section>
    <footer>
        <p><?php include 'Edition'; ?></p>
        <p>Copyright ©2020</p>
        <p>SKYTown Server All Rights Reserved.</p>
        <p>Power By JasonZYT</p>
        <p id="hitokoto"></p>
        <script src="https://v1.hitokoto.cn/?encode=js&amp;select=%23hitokoto" defer=""></script>
    </footer>

    <script type="text/javascript" src="../assets/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../assets/js/jquery.nav.js"></script>

</body>

</html>