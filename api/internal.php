<?php
include "config.php";

$fileExtensionIcon = [
    "c", "i", "s", "o", "out", "cxx", "cc", "c++", "C", "cpp", "inl", "hpp", "hxx", "h++", "h",
    "cs", "aspx", "resx", "json", "md", "py", "pyo", "pyw", "pyc", "pyd", "php", "phps", "lua",
    "go", "sln", "ttf", "otf", "woff", "woff2", "eot", "apk", "xapk", "css", "less", "js", "exe",
    "log", "doc", "docx", "docm", "dot", "dotx", "dotm", "jpg", "png", "jpeg", "bmp", "gif", "tif",
    "pcx", "tga", "exif", "fpx", "ai", "raw", "webp", "pdf", "ppt", "pptx", "pptm", "potx", "potm",
    "pot", "ppsx", "ppsm", "ppa", "ppam", "zip", "xml", "ini", "cfg", "config", "conf", "propreties",
    "ipa", "plist", "applescript", "ps1", "bat", "sh", "bash", "html", "htm", "dll", "lib", "txt",
    "gitignore", "mcpack", "mcaddon", "mcworld", "cer", "p12", "p7b", "pfx", "sst",
    //"xls", "xlsx", "xlsm", "xltx", "xltm", "xlt", "xlsb"
];
$previewFiles = [
    "doc", "docx", "docm", "dot", "dotx", "dotm", "pdf", "ppt", "pptx", "pptm", "potx", "potm",
    "pot", "ppsx", "ppsm", "ppa", "ppam", "xls", "xlsx", "xlsm", "xltx", "xltm", "xlt", "xlsb"
];
define("config", getConfig());

function getConfig(): object
{
    global $head;
    global $enableMarkdown;
    global $textOnTopLeft;
    global $fileDirectory;
    global $encryptPasswordFrontend;
    global $dbConfig;
    return (object)[
        "head" => $head,
        "enableMarkdown" => $enableMarkdown,
        "textOnTopLeft" => $textOnTopLeft,
        "fileDirectory" => $fileDirectory,
        "encryptPasswordFrontend" => $encryptPasswordFrontend,
        "db" => $dbConfig
    ];
}

function getWebsiteTitle($uri): ?string
{
    $h = curl_init();
    curl_setopt($h, CURLOPT_URL, $uri);
    curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($h, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($h, CURLOPT_MAXREDIRS, 10);
    curl_setopt($h, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($h, CURLOPT_TIMEOUT, 10);
    curl_setopt($h, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
    curl_setopt($h, CURLOPT_SSL_VERIFYPEER, false);
    $content = curl_exec($h);
    curl_close($h);
    if (mb_strpos($content, "<title>") !== false) {
        $title = mb_substr($content, mb_strpos($content, "<title>") + 7);
        return mb_substr($title, 0, mb_strpos($title, "</title>"));
    }
    return null;
}

function getLine($file, $line, $length = 4096)
{
    $returnTxt = null;
    $i = 1;
    $handle = @fopen($file, "r");
    if ($handle) {
        while (!feof($handle)) {
            $buffer = fgets($handle, $length);
            if ($line == $i) $returnTxt = $buffer;
            $i++;
        }
        fclose($handle);
    }
    return $returnTxt;
}

function getFileSizeStr($fileSize): string
{
    if ($fileSize >= 1024 && $fileSize < 1048576) {
        return round($fileSize / 1024, 2) . "KB";
    } else if ($fileSize >= 1048576 && $fileSize < 1073741824) {
        return round($fileSize / 1048576, 2) . "MB";
    } else if ($fileSize >= 1073741824 && $fileSize < 1099511627776) {
        return round($fileSize / 1073741824, 2) . "GB";
    } else {
        return $fileSize . "B";
    }
}

function getFolderSize($path): int
{
    $result = 0;
    $files = glob($path . "/*");
    foreach ($files as $file) {
        if (is_dir($file)) {
            $result += getFolderSize($file);
        } else {
            $result += filesize($file);
        }
    }
    return $result;
}

function getFullHostName(): string
{
    $result = "http://";
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        $result = "https://";
    } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $result = "https://";
    } else if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        $result = "https://";
    }
    return $result . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . '/';
}

class UserRole
{
    const Guest = "Guest";
    const User = "User";
    const Admin = "Admin";
}

class User {
    var int $id;
    var string $username;
    var string $password_hash;
    var string $salt;
    var string $role;
    var string $token;
    var int $token_expire;

    function __construct(int $id, string $username, string $password_hash, string $salt, string $role, string $token, int $token_expire)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password_hash = $password_hash;
        $this->salt = $salt;
        $this->role = $role;
        $this->token = $token;
        $this->token_expire = $token_expire;
    }
}

class DBConnection extends mysqli {

    /**
     * @throws Exception
     */
    public function __construct($host, $user, $pass, $db, $port = 3306)
    {
        parent::__construct($host, $user, $pass, $db, $port);
        if ($this->connect_error) {
            throw new Exception("MySQL Connection Error: " . $this->connect_error);
        }
        $this->createTables();
    }

    public function __destruct()
    {
        $this->close();
    }

    public function createTables() {
        $this->query("CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(255) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `salt` varchar(255) NOT NULL,
            `role` varchar(255) NOT NULL,
            `token` varchar(255) NOT NULL,
            `token_expire` int(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        /*
        $this->query("CREATE TABLE IF NOT EXISTS `files` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `path` varchar(255) NOT NULL,
            `upload_time` int(11) NOT NULL,
            `upload_user` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        */
    }

    public function createUser($username, $password, $role): User {
        $salt = bin2hex(random_bytes(16));
        $password_hash = hash("sha256", $password . $salt);
        $token = bin2hex(random_bytes(32));
        $token_expire = time() + 5184000; // 60 days
        $stmt = $this->prepare("INSERT INTO users (username, password_hash, salt, role, token, token_expire) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $username, $password_hash, $salt, $role, $token, $token_expire);
        $stmt->execute();
        $stmt->close();
        return new User($this->insert_id, $username, $password_hash, $salt, $role, $token, $token_expire);
    }

    public function getUser($username): ?object
    {
        $stmt = $this->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            return $result->fetch_object();
        }
        return null;
    }

    public function getUserByToken($token): ?object
    {
        $stmt = $this->prepare("SELECT * FROM users WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            return $result->fetch_object();
        }
        return null;
    }

}

/**
 * @throws Exception
 */
function login($username, $password): ?User
{
    $db = new DBConnection(config->db->host, config->db->user, config->db->password, config->db->database);
    $user = $db->getUser($username);
    if ($user === null) {
        throw new Exception("User $username is not found");
    }
    $password_hash = hash("sha256", $password . $user->salt);
    if ($password_hash === $user->password_hash) {
        $token = bin2hex(random_bytes(32));
        $token_expire = time() + 5184000; // 60 days
        $stmt = $db->prepare("UPDATE users SET token = ?, token_expire = ? WHERE id = ?");
        $stmt->bind_param("sii", $token, $token_expire, $user->id);
        $stmt->execute();
        $stmt->close();
        return new User($user->id, $user->username, $user->password_hash, $user->salt, $user->role, $token, $token_expire);
    }
    throw new Exception("Password is incorrect");
}

/**
 * @throws Exception
 */
function register($username, $password, $role): ?User
{
    $db = new DBConnection(config->db->host, config->db->user, config->db->password, config->db->database);
    $user = $db->getUser($username);
    if ($user !== null) {
        throw new Exception("User $username already exists");
    }
    return $db->createUser($username, $password, $role);
}