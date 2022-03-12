# File-Web-Repository

## Requirements
- Web server (Apache, Nginx, etc.)
- PHP v8.1+ with extensions:
  - gd - for image manipulation (captcha)
  - curl - for remote file access
  - mbstring - for multi-byte string support
  - mysqli - for database access
  - zip - for zip preview
- MySQL v5.5+

## Installation
1. Download source code from [Releases](https://github.com/Jasonzyt/File-Web-Repository/releases) or [MineBBS](https://www.minebbs.com/threads/php-file-web-repository.5606/)
2. Unzip the source code
3. Copy the source code to your web server
4. Modify the configuration file(`config.php`)
5. Open the web server
6. Put your files into `$fileDirectory` folder

## Third-party Background Used
[copyrights.json](assets/img/backgrounds/copyrights.json)

# Web文件仓库

## 依赖
- Web服务器 (Apache, Nginx等)
- PHP v8.1+, 并开启以下扩展：
  - gd - 为图像处理 (验证码)
  - curl - 为远程文件访问
  - mbstring - 为多字节字符串支持
  - mysqli - 为数据库访问
  - zip - 为压缩文件预览
- MySQL v5.5+

## 安装
1. 从[Release](https://github.com/Jasonzyt/File-Web-Repository/releases)或[MineBBS](https://www.minebbs.com/threads/php-file-web-repository.5606/) 下载源代码
2. 将打包的源代码解压到某个文件夹
3. 将源代码复制到你的Web服务器
4. 修改`config.php`文件
5. 打开Web服务器
6. 将文件放入`$fileDirsctory`文件夹

# TODO
- [X] I18N
- [ ] 支持Markdown/HTML解析并显示在下方
- [ ] 多级目录合并显示(类似GitHub)
- [ ] 上传文件
- [ ] 多文件上传
- [X] 上传文件按/自动转换为文件夹(类似GitHub)
- [X] ..上级文件夹显示
- [ ] 用户系统
- [ ] APIs
- [ ] Zip文件预览