<?php

include "api/internal.php";

$uploading = isset($_POST["fileName"]);
$fileName = $_POST["fileName"] ?? null;
$file = $_FILES["file"] ?? null;
$dir = $_REQUEST["dir"] ?? "";

?>
<html lang="zh-CN">

<head>
    <!-- META -->
    <meta charset="UTF-8"/>
    <meta name="description" content="Upload single file"/>
    <meta name="author" content="JasonZYT"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- TITLE -->
    <title>Upload File - <?php echo config->head->title; ?></title>
    <!-- ICON -->
    <link rel="shortcut icon" href="<?php echo config->head->icon; ?>"/>
    <link rel="bookmark" href="<?php echo config->head->icon; ?>"/>
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
                <span><?php echo config->textOnTopLeft; ?></span>
            </div>
            <ul class="nav-menu">
                <?php
                // Path on the top
                echo '<li class="ignore"><a href="/"><span class="i18n">Home</span><svg><use xlink:href="#AngleBracket-R" /></svg></a></li>';
                $dirs = explode("/", $dir);
                $path = "";
                foreach ($dirs as $d) {
                    if ($d == "") {
                        continue;
                    }
                    $path .= $d;
                    echo '<li><a href="/?dir=' . $path . '">' . $d . '<svg><use xlink:href="#AngleBracket-R"></use></svg></a></li>';
                    $path .= "/";
                }
                if ($uploading) {
                    echo '<li><a style="margin-top:0.15em;color:#000;">' . $fileName . '</a></li>';
                } else {
                    echo '<li class="ignore"><input class="variable-width-input" id="navbar-upload-file-name" type="text" placeholder="..." readonly/></li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
<section class="services-section spad">
    <div class="container">
        <h2 class="i18n">Upload Single File</h2>
        <form id="upload-form" action="upload.php" method="post" enctype="multipart/form-data">
                <span id="upload-path"><span class="i18n ignore">Upload to </span><a class="i18n ignore"
                                                                                     onclick="backTo('');">Home</a> /<?php
                    $dirs = explode("/", $dir);
                    $path = "";
                    foreach ($dirs as $d) {
                        if ($d == "") {
                            continue;
                        }
                        $path .= $d;
                        echo " <a onclick=\"backTo('$path');\">$d</a> /";
                        $path .= "/";
                    }
                    ?> <input class="variable-width-input ignore" id="input-file-name" type="text" name="fileName"
                              placeholder="File name"/>
                </span>
            <br/>
            <div id="upload-file">
                <button id="fake-file-btn" type="button" onclick="document.getElementById('real-file-btn').click()">
                    <svg>
                        <use xlink:href="#Upload"/>
                    </svg>
                    <br/>
                    <span id="select-text" class="i18n">Click here or drop your file to here to select a file</span>
                </button>
                <input id="real-file-btn" type="file" name="file" style="display: none;"/>
            </div>
            <br/>
            <input type="hidden" id="upload-dir" name="dir" value="<?php echo $dir; ?>"/>
            <input class="value-i18n" id="submit-btn" type="submit" value="Upload"/>
        </form>
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

<script type="text/javascript" src="assets/js/i18n.js"></script>
<script>
    <?php
    if (isset($_COOKIE["lang"])) echo "langCode = '" . $_COOKIE["lang"] . "';";
    ?>
    fullLang = <?php include "I18N.json"; ?>;
    do_i18n();
    let uploading = <?php echo $uploading ? "true" : "false"; ?>;
    if (uploading) {
        document.getElementById("upload-form").style.display = "none";
    }
    const $_GET = (function () {
        let url = window.document.location.href.toString();
        let u = url.split("?");
        if (typeof (u[1]) == "string") {
            u = u[1].split("&");
            let get = {};
            for (let i in u) {
                let j = u[i].split("=");
                get[j[0]] = j[1];
            }
            return get;
        } else {
            return {};
        }
    })();
    let curDir = $_GET["dir"] ? $_GET["dir"] : "";
    disableEnterSubmit();
    // Upload form
    let form = document.getElementById("upload-form")
    form.onsubmit = function () {
        if (document.getElementById("input-file-name").value === "") {
            alert(tr("Please input file name"));
            return false;
        }
        if (document.getElementById("real-file-btn").files.length !== 1) {
            alert(tr("Only one file can be uploaded at a time!\nPlease choose one file and try again."));
            return false;
        }
        document.getElementById('upload-dir').value = curDir;
        document.getElementById("submit-btn").value = tr("Uploading...");
    };
    // Fake file button (drag/drop)
    let fakeFileButton = document.getElementById("fake-file-btn");
    fakeFileButton.ondragover = function (ev) {
        ev.preventDefault();
        fakeFileButton.style.border = "3px dashed #0a7cef";
    }
    fakeFileButton.ondragleave = function () {
        fakeFileButton.style.border = "3px dashed #707070";
    }
    fakeFileButton.ondrop = function (ev) {
        fakeFileButton.style.border = "3px dashed #707070";
        ev.preventDefault();
        let files = ev.dataTransfer.files;
        if (files.length > 1) {
            alert(tr("Only one file can be uploaded at a time!\nPlease choose one file and try again."));
            return;
        } else if (files.length === 0) {
            return;
        } else if (!checkFileName(files[0].name)) {
            alert(tr("File name contains illegal characters!"));
            return;
        }
        let file = files[0];
        setFileName(file.name);
        document.getElementById("real-file-btn").files = files;
        document.getElementById("select-text").innerText = tr("File selected: ") + file.name;
    }
    // Real file button
    let realFileButton = document.getElementById("real-file-btn");
    realFileButton.onchange = function () {
        let file = realFileButton.files[0];
        setFileName(file.name);
        document.getElementById("select-text").innerText = tr("File selected: ") + file.name;
    }
    let variableWidthInputs = document.getElementsByClassName("variable-width-input");
    for (let i = 0; i < variableWidthInputs.length; i++) {
        variableWidthInputs[i].onkeydown = function (ev) {
            if (ev.keyCode === 8) {
                setFileName(this.value);
            }
        }
        variableWidthInputs[i].oninput = function () {
            this.placeholder = "";
            setFileName(this.value);
        }
    }
    let inputFileName = document.getElementById("input-file-name");
    inputFileName.onkeydown = function (ev) {
        if (ev.key === "Backspace") {
            if (this.value === "") {
                ev.preventDefault();
                setFileName(curDir.substr(curDir.lastIndexOf("/") + 1));
                backTo(curDir.substr(0, curDir.lastIndexOf("/")));
            }
            setFileName(this.value);
        } else if (ev.key === "/" || ev.key === "\\") {
            ev.preventDefault();
            let dirName = this.value;
            if (dirName !== "") {
                setFileName("");
                backTo(curDir + '/' + dirName);
            }
        }
    }

    // Set the file name in inputs
    function setFileName(name) {
        // 1em padding
        document.getElementById("navbar-upload-file-name").style.width = (getStringWidth(name, document.getElementById("navbar-upload-file-name")) + em2pixel(document.getElementById("navbar-upload-file-name"), 1)) + "px";
        document.getElementById("navbar-upload-file-name").value = name;
        document.getElementById("input-file-name").style.width = (getStringWidth(name, document.getElementById("input-file-name")) + em2pixel(document.getElementById("input-file-name"), 1)) + "px";
        document.getElementById("input-file-name").value = name;
    }

    // Convert em to pixel
    function em2pixel(element, em = 1) {
        let fontSize = window.getComputedStyle(element, null).getPropertyValue('font-size');
        return parseFloat(fontSize) * em;
    }

    function getStringWidth(str, element) {
        let span = document.createElement("span");
        span.style.fontSize = em2pixel(element) + "px";
        span.innerHTML = str;
        document.body.appendChild(span);
        let width = span.offsetWidth;
        document.body.removeChild(span);
        return width;
    }

    // Set the current upload path to {path}
    function backTo(path) {
        if (curDir === path) return;
        curDir = path;
        let navMenu = document.getElementsByClassName("nav-menu")[0];
        // Warning: Don't remove any child in forEach
        for (let i = 0; i < navMenu.childNodes.length; i++) {
            const el = navMenu.childNodes[i];
            if (!el.classList?.contains("ignore")) {
                navMenu.removeChild(el);
                i--;
            }
        }
        let uploadPath = document.getElementById("upload-path");
        for (let i = 0; i < uploadPath.childNodes.length; i++) {
            const el = uploadPath.childNodes[i];
            if (el.nodeName !== "#text" && !el.classList?.contains("ignore")) {
                uploadPath.removeChild(el);
                i--;
                continue;
            }
            if (el.textContent === ' / ') {
                uploadPath.removeChild(el);
                i--;
            }
        }
        let navbarInput = document.getElementById("navbar-upload-file-name");
        let formInput = document.getElementById("input-file-name");
        uploadPath.insertBefore(document.createTextNode(" / "), formInput);
        let cur = "";
        path.split("/").forEach(function (item) {
            if (item === "") return;
            cur += item;
            let li = document.createElement("li");
            let a = document.createElement("a");
            a.innerText = "" + item;
            a.href = "/?dir=" + cur;
            a.innerHTML += '<svg><use xlink:href="#AngleBracket-R" /></svg>';
            li.appendChild(a);
            navMenu.insertBefore(li, navbarInput.parentNode);
            a = document.createElement("a");
            a.innerText = "" + item;
            a.setAttribute("onclick", `backTo('${cur}')`);
            uploadPath.insertBefore(a, formInput);
            uploadPath.insertBefore(document.createTextNode(" / "), formInput);
            cur += "/";
        });
    }

    // Check if the file name is valid
    function checkFileName(name) {
        let illegalChars = ["\\", "/", ":", "*", "?", "\"", "<", ">", "|"];
        if (name.length === 0) {
            return false;
        }
        for (const ch of illegalChars) {
            if (name.indexOf(ch) !== -1) {
                return false;
            }
        }
        return true;
    }

    // Disable submitting the form with Enter key
    function disableEnterSubmit() {
        let form = document.getElementById("upload-form");
        form.onkeydown = function (ev) {
            if (ev.key === "Enter") {
                ev.preventDefault();
            }
        }
    }
</script>

</html>
