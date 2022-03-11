const http = {
    get: (url, callback = null) => {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.onload = () => {
            if (callback) {
                callback(xhr);
            }
        };
        xhr.send();
    }
}