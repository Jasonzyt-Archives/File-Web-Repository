let lang = null;
let langCode = navigator.language;
let fullLang = null;

function i18n_init() {
    Object.keys(fullLang).forEach(function (key) {
        if (langCode.indexOf(key) !== -1) {
            lang = fullLang[key];
        }
    });
}
function do_i18n() {
    i18n_init();
    if (lang !== null) {
        let i18nElements = document.getElementsByClassName("i18n");
        if (i18nElements !== null) {
            for (let i = 0; i < i18nElements.length; i++) {
                let el = i18nElements[i];
                let text = el.innerHTML;
                if (Object.keys(lang).indexOf(text) !== -1) {
                    el.innerHTML = lang[text];
                } else {
                    for (let i1 = 0; i1 < Object.keys(lang).length; i1++){
                        let key = Object.keys(lang)[i1];
                        if (!key.startsWith("regex:")) continue;
                        key = key.substring(6);
                        let reg = new RegExp(key);
                        if (reg.test(text) && text.match(reg) !== null) {
                            console.log(text);
                            let res = reg.exec(text);
                            if (res != null) {
                                let val = lang["regex:" + key];
                                for (let i = 0; i < res.length; i++) {
                                    val = val.replace("$" + i, res[i]);
                                }
                                console.log(val);
                                el.innerHTML = val;
                                break;
                            }
                        }
                    }

                }
            }
        }
        i18nElements = document.getElementsByClassName("value-i18n");
        if (i18nElements !== null) {
            for (let i = 0; i < i18nElements.length; i++) {
                let el = i18nElements[i];
                let text = el.value;
                if (Object.keys(lang).indexOf(text) !== -1) {
                    el.value = lang[text];
                } else {
                    for (let i1 = 0; i1 < Object.keys(lang).length; i1++){
                        let key = Object.keys(lang)[i1];
                        if (!key.startsWith("regex:")) continue;
                        key = key.substring(6);
                        let reg = new RegExp(key);
                        if (reg.test(text) && text.match(reg) !== null) {
                            let res = reg.exec(text);
                            if (res != null) {
                                let val = lang[key];
                                for (let i = 0; i < res.length; i++) {
                                    val = val.replace("$" + i, res[i]);
                                }
                                el.value = val;
                                break;
                            }
                        }
                    }

                }
            }
        }
    }
}
function tr(key) {
    if (lang !== null) {
        if (Object.keys(lang).indexOf(key) !== -1) {
            return lang[key];
        }
    }
    return key;
}