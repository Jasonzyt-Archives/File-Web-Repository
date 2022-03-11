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
            <h1 class="i18n" style="margin-bottom: 0.6em;">Login</h1>
            <input type="hidden" name="type" value="login"/>
            <svg style="margin-top: 0.3em"><use xlink:href="#Username"/></svg>
            <input type="text" name="username" placeholder="Username" class="form-control placeholder-i18n" required/>
            <svg><use xlink:href="#Password"/></svg>
            <input type="password" name="password" placeholder="Password" class="form-control placeholder-i18n" required autocomplete/>
            <svg><use xlink:href="#Captcha"/></svg>
            <input type="text" name="verification" placeholder="Verification Code" class="form-control placeholder-i18n" required>
                <img src="api/captcha.php" alt="Verification Code" class="captcha title-i18n" title="Click to change the code" onclick="changeVerificationCode()"/>
            </input>
            <input type="submit" value="Login" class="btn btn-primary btn-block"/>
            <span>
                <a class="link i18n" onclick="displayRegister()" style="margin-top: 0.5em; float: right;">Register</a>
            </span>
        </form>
        <form id="register-form" method="post" action="">
            <h1 class="i18n" style="margin-bottom: 0.6em;">Register</h1>
            <input type="hidden" name="type" value="register"/>
            <svg style="margin-top: 0.3em"><use xlink:href="#Username"/></svg>
            <input type="text" name="username" placeholder="Username" class="form-control placeholder-i18n" required/>
            <svg><use xlink:href="#Password"/></svg>
            <input type="password" name="password" placeholder="Password" class="form-control placeholder-i18n" required autocomplete/>
            <svg><use xlink:href="#Captcha"/></svg>
            <input type="text" name="verification" placeholder="Verification Code" class="form-control placeholder-i18n" required>
                <img src="api/captcha.php" alt="Verification Code" class="captcha title-i18n" title="Click to change the code" onclick="changeVerificationCode()"/>
            </input>
            <input type="submit" value="Register" class="btn btn-primary btn-block"/>
            <span>
                <a class="link i18n" onclick="displayLogin()" style="margin-top: 0.5em; float: right;">Login</a>
            </span>
        </form>
    </div>
</section>
<div id="bg-copyright"></div>

</body>

<script type="text/javascript" src="assets/js/i18n.js"></script>
<script>
    <?php
    if (isset($_COOKIE["lang"])) echo "langCode = '" . $_COOKIE["lang"] . "';";
    ?>
    fullLang = <?php include "I18N.json"; ?>;
    do_i18n();

    // Background
    const bgUrl = `api/background/random.php?width=${window.screen.availWidth}&height=${window.screen.availHeight}`;
    let bg = document.getElementById("random-bg");
    bg.style.background = `url('${bgUrl}') no-repeat center`;
    // Wait for background loaded
    let bgImg = new Image();
    bgImg.src = bgUrl;
    bgImg.onload = () => {
        let req = new XMLHttpRequest();
        req.open('GET', `${window.location.origin}/api/background/current.php`);
        req.onload = () => {
            if (req.status === 200) {
                if (req.responseText !== "{}") {
                    let info = JSON.parse(req.responseText);
                    let dWidth = window.screen.availWidth / info.width;
                    let dHeight = window.screen.availHeight / info.height;
                    let d = Math.max(dHeight, dWidth);
                    bg.style.zoom = d.toString();
                    copyrightInfo(info["copyright"]);
                }
            }
        };
        req.send();
    };
    // Register or Login
    let isLogin = true;
    if (window.location.hash === "#register") {
        displayRegister();
    } else {
        displayLogin();
    }
    let form = document.getElementById(isLogin ? "login-form" : "register-form");
    let formX = form.offsetLeft;
    let formY = form.offsetTop;
    let mouseX = 0;
    let mouseY = 0;
    form.onmousedown = (ev) => {
        formX = form.offsetLeft;
        formY = form.offsetTop;
        mouseX = ev.clientX;
        mouseY = ev.clientY;
    };
    form.onmousemove = (ev) => {
        if (ev.buttons === 1) {
            form.style.left = (formX + ev.clientX - mouseX) + "px";
            form.style.top = (formY + ev.clientY - mouseY) + "px";
        }
    };
    form.onmouseup = (ev) => {
        formX = form.offsetLeft;
        formY = form.offsetTop;
        mouseX = ev.clientX;
        mouseY = ev.clientY;
    };
    // Display login form
    function displayLogin() {
        document.getElementById("register-form").style.display = "none";
        document.getElementById("login-form").style.display = "block";
        isLogin = true;
    }
    // Display register form
    function displayRegister() {
        document.getElementById("login-form").style.display = "none";
        document.getElementById("register-form").style.display = "block";
        isLogin = false;
    }
    // Change verification code
    function changeVerificationCode() {
        document.getElementsByClassName("captcha")[0].src = `api/captcha.php?${Math.random()}`;
    }
    // Display copyright info
    function copyrightInfo(info) {
        let div = document.getElementById("bg-copyright");
        switch (info.preset) {
            case "none":
                div.style.display = "none";
                break;
            case "custom":
                div.style.display = "block";
                div.innerHTML = "Background " + info.html;
                break;
            case "bilibili:video":
                div.style.display = "block";
                div.innerHTML = `Background &copy;<a class="link" href="${info.author.link}">${info.author.name}</a> / BilibiliVideo: <a class="link" href="https://bilibili.com/video/${info.bvid}?t=${info.time}" target="_blank">${info.bvid}</a>`;
                break;
            case "pixiv":
                div.style.display = "block";
                div.innerHTML = `Background &copy;<a class="link" href="${info.author.link}">${info.author.name}</a> / Pixiv Illustration: <a class="link" href="https://www.pixiv.net/artworks/${info.id}" target="_blank">${info.id}</a>`;
                break;
        }
    }
</script>

</html>