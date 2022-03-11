<?php

include "api/internal.php";

?>
<html lang="zh-CN">

<head>
    <!-- META -->
    <meta charset="UTF-8"/>
    <meta name="description" content="Login"/>
    <meta name="author" content="JasonZYT"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- TITLE -->
    <title>Login - <?php echo config->head->title; ?></title>
    <!-- ICON -->
    <link rel="shortcut icon" href="<?php echo config->head->icon; ?>"/>
    <link rel="bookmark" href="<?php echo config->head->icon; ?>"/>
    <!-- LINK-CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body style="overflow: hidden;">
<?php include "assets/svg/icon.svg" ?>

<div id="random-bg" style="position: absolute"></div>
<section class="spad">
    <div class="text-center">
        <form id="login-form" method="post" action="">
            <h1 class="i18n" style="margin-bottom: 0.8em;">Login</h1>
            <svg style="margin-top: 0.3em"><use xlink:href="#Username"/></svg>
            <input type="text" name="username" placeholder="Username" class="form-control" required/>
            <svg><use xlink:href="#Password"/></svg>
            <input type="password" name="password" placeholder="Password" class="form-control" required/>
            <svg><use xlink:href="#Captcha"/></svg>
            <input type="text" name="verification" placeholder="Verification Code" class="form-control" required>
                <img src="api/captcha.php" alt="Verification Code" class="captcha" onclick="changeVerificationCode()"/>
            </input>
            <input type="submit" value="Login" class="btn btn-primary btn-block"/>
            <span>
                <a class="link i18n" onclick="displayRegister()" style="margin-top: 0.5em; float: right;">Register</a>
            </span>
        </form>
    </div>
</section>

</body>

<script type="text/javascript" src="assets/js/i18n.js"></script>
<script type="text/javascript" src="assets/js/utils.js"></script>
<script>
    <?php
    if (isset($_COOKIE["lang"])) echo "langCode = '" . $_COOKIE["lang"] . "';";
    ?>
    fullLang = <?php include "I18N.json"; ?>;
    do_i18n();

    // Background
    const bgUrl = `api/background/random.php?width=${window.screen.width}&height=${window.screen.height}`;
    let bg = document.getElementById("random-bg");
    bg.style.background = `url('${bgUrl}') no-repeat center`;
    // Wait for background loaded
    let bgImg = new Image();
    bgImg.src = bgUrl;
    bgImg.onload = () => {
        http.get(`${window.location.origin}/api/background/current.php`, (req) => {
            if (req.status === 200) {
                if (req.responseText !== "{}") {
                    let info = JSON.parse(req.responseText);
                    let dHeight = window.screen.height / info.height;
                    let dWidth = window.screen.width / info.width;
                    let d = Math.max(dHeight, dWidth);
                    bg.style.zoom = d.toString();
                }
            }
        });
    };
    // #register
    if (window.location.hash === "#register") {
        displayRegister();
    }
    // Display login form
    function displayLogin() {
        document.getElementById("register-form").style.display = "none";
        document.getElementById("login-form").style.display = "block";
    }
    // Display register form
    function displayRegister() {
        document.getElementById("login-form").style.display = "none";
        document.getElementById("register-form").style.display = "block";
    }
    function changeVerificationCode() {
        document.getElementsByClassName("captcha")[0].src = `api/captcha.php?${Math.random()}`;
    }
</script>

</html>