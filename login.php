<?php

include "api/internal.php";

if (isset($_POST['type'])) {
    if ($_POST['type'] == "login") {

    }
}

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
            <input type="submit" value="Login" class="btn btn-primary btn-block value-i18n"/>
            <span>
                <a class="link i18n" onclick="displayRegister()" style="margin-top: 0.5em; float: right;">Doesn't have an account? Register.</a>
            </span>
        </form>
        <form id="register-form" method="post" action="">
            <h1 class="i18n" style="margin-bottom: 0.6em;">Register</h1>
            <input type="hidden" name="type" value="register"/>
            <svg style="margin-top: 0.3em"><use xlink:href="#Username"/></svg>
            <input type="text" name="username" placeholder="Username" class="form-control placeholder-i18n" required/>
            <svg><use xlink:href="#Password"/></svg>
            <input type="password" name="password" placeholder="Password" class="form-control placeholder-i18n" required autocomplete/>
            <svg><use xlink:href="#Password"/></svg>
            <input type="password" name="password2" placeholder="Confirm Password" class="form-control placeholder-i18n" required autocomplete/>
            <svg><use xlink:href="#Captcha"/></svg>
            <input type="text" name="verification" placeholder="Verification Code" class="form-control placeholder-i18n" required>
                <img src="api/captcha.php" alt="Verification Code" class="captcha title-i18n" title="Click to change the code" onclick="changeVerificationCode()"/>
            </input>
            <input type="submit" value="Register" class="btn btn-primary btn-block value-i18n"/>
            <span>
                <a class="link i18n" onclick="displayLogin()" style="margin-top: 0.5em; float: right;">Have an account? Sign in.</a>
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
    // Convert em to pixel
    function em2pixel(element, em = 1) {
        let fontSize = window.getComputedStyle(element, null).getPropertyValue('font-size');
        return parseFloat(fontSize) * em;
    }

    /**
     * Get the main color of an image
     * @param img {HTMLImageElement} The image
     * @param x {number} The x-axis coordinate of the top-left corner of the rectangle from which the ImageData will be extracted.
     * @param y {number} The y-axis coordinate of the top-left corner of the rectangle from which the ImageData will be extracted.
     * @param w {number} The width of the rectangle from which the ImageData will be extracted. Positive values are to the right, and negative to the left.
     * @param h {number} The height of the rectangle from which the ImageData will be extracted. Positive values are down, and negative are up.
     * @returns {number[]} The RGB color of the main color
     */
    function getImageMainColor(img, x, y, w, h) {
        let canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;
        let ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        let imageData = ctx.getImageData(x, y, w, h);
        let data = imageData.data;
        let r = 0, g = 0, b = 0;
        for (let i = 0; i < data.length; i += 4) {
            r += data[i];
            g += data[i + 1];
            b += data[i + 2];
            // Note: a = data[i + 3];
        }
        r = Math.floor(r / (data.length / 4));
        g = Math.floor(g / (data.length / 4));
        b = Math.floor(b / (data.length / 4));
        return [r, g, b];
    }
    /**
     * @param rgb {array} [r, g, b]
     * @param bw {boolean} true for black and white, false for colorful
     * @returns {string} Hex color
     */
    function invertColor(rgb, bw) {
        /* Unused: Hex to RGB
        if (hex.indexOf('#') === 0) {
            hex = hex.slice(1);
        }
        // Convert 3-digit hex to 6-digits.
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        if (hex.length !== 6) {
            throw new Error('Invalid HEX color.');
        }
        let r = parseInt(hex.slice(0, 2), 16),
            g = parseInt(hex.slice(2, 4), 16),
            b = parseInt(hex.slice(4, 6), 16);
         */
        let r = rgb[0], g = rgb[1], b = rgb[2];
        if (bw) {
            // http://stackoverflow.com/a/3943023/112731
            return (r * 0.299 + g * 0.587 + b * 0.114) > 186
                ? '#000000'
                : '#FFFFFF';
        }
        // invert color components
        r = (255 - r).toString(16);
        g = (255 - g).toString(16);
        b = (255 - b).toString(16);
        // pad each with zeros and return
        return "#" + padZero(r) + padZero(g) + padZero(b);
    }
    // Append zero
    function padZero(str, len) {
        len = len || 2;
        let zeros = new Array(len).join('0');
        return (zeros + str).slice(-len);
    }
    // Display copyright info
    function copyrightInfo(info) {
        if (info === null) {
            return;
        }
        let bgCopyright = document.getElementById("bg-copyright");
        switch (info.preset) {
            case "none":
                bgCopyright.style.display = "none";
                return;
            case "custom-html":
                bgCopyright.style.display = "block";
                bgCopyright.innerHTML = "Background " + info.html;
                break;
            case "bilibili:video":
                bgCopyright.style.display = "block";
                bgCopyright.innerHTML = `Background &copy;<a class="link" href="${info.author.link}">${info.author.name}</a> / Video: <a class="link" href="https://bilibili.com/video/${info.bvid}?t=${info.time}" target="_blank">${info.bvid}</a>`;
                break;
            case "bilibili:post": // 动态官方译名为dynamic, 不合适, 改为post
                bgCopyright.style.display = "block";
                bgCopyright.innerHTML = `Background &copy;<a class="link" href="${info.author.link}">${info.author.name}</a> / <a class="link" href="${info.url}" target="_blank">Bilibili Post</a>`;
                break;
            case "bilibili:video&post":
                bgCopyright.style.display = "block";
                bgCopyright.innerHTML = `Background &copy;<a class="link" href="${info.author.link}">${info.author.name}</a> / <a class="link" href="${info.post}" target="_blank">Bilibili Post</a> / Video: <a class="link" href="https://bilibili.com/video/${info.bvid}?t=${info.time}" target="_blank">${info.bvid}</a>`;
                break;
            case "pixiv":
                let p = info.picture ?? 1;
                bgCopyright.style.display = "block";
                bgCopyright.innerHTML = `Background &copy;<a class="link" href="${info.author.link}">${info.author.name}</a> / Pixiv Illustration: <a class="link" href="https://www.pixiv.net/artworks/${info.id}#${p}" target="_blank">${info.id}</a>`;
                break;
        }
        let x = bgImg.width - bgCopyright.offsetWidth;
        let y = bgImg.height - bgCopyright.offsetHeight;
        let style = window.getComputedStyle(bgCopyright);
        let w = Math.ceil(Number.parseFloat(style.width.replace("px", "")));
        let h = Math.ceil(Number.parseFloat(style.height.replace("px", "")));
        if (w && h) {
            let rgb = getImageMainColor(bgImg, x < 0 ? 0 : x, y < 0 ? 0 : y, w, h);
            bgCopyright.style.color = invertColor(rgb, true);
        }
    }
</script>

</html>