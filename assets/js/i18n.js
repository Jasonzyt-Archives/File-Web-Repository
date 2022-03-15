let lang = null;
let langCode = navigator.language;
let fullLang = null;

function _autoTranslate(className, prop) {
    let i18nElements = document.getElementsByClassName(className);
    if (i18nElements !== null) {
        for (let i = 0; i < i18nElements.length; i++) {
            let el = i18nElements[i];
            if (!el[prop]) {
                continue;
            }
            let text = el[prop];
            if (Object.keys(lang).indexOf(text) !== -1) {
                el[prop] = lang[text];
            } else {
                for (let i1 = 0; i1 < Object.keys(lang).length; i1++){
                    let key = Object.keys(lang)[i1];
                    if (!key.startsWith("regex:")) continue;
                    key = key.substring(6);
                    let reg = new RegExp(key);
                    if (reg.test(text) && text.match(reg) !== null) {
                        let res = reg.exec(text);
                        if (res != null) {
                            let val = lang["regex:" + key];
                            for (let i = 0; i < res.length; i++) {
                                val = val.replace("$" + i, res[i]);
                            }
                            el[prop] = val;
                            break;
                        }
                    }
                }

            }
        }
    }
}

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
        _autoTranslate("i18n", "innerHTML");
        _autoTranslate("value-i18n", "value");
        _autoTranslate("placeholder-i18n", "placeholder");
        _autoTranslate("title-i18n", "title");
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