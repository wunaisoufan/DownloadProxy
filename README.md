# DownloadProxy

通过PHP+Nginx搭建一个简单下载代理的WEB站点

*通过AI帮助弄出来的个小玩意，安全性不高，自己搭建了用一用吧*

访问站点有密码（安全性低、不需要自行删除即可），流式传输、断点续传、支持Github个人令牌token、有登录和下载日志（会保存Token、IP、文件URL等）

## 使用

**所需PHP扩展**：cURL、fileinfo

**必需PHP函数**：curl_、filter_va、parse_url、header、filesize、exit

在`verify.php`文件里第四行

```php
$correct_password = '密码'; 	// 替换为你的密码
```

`robots.txt`不允许搜索引擎爬取，不需要删除即可。

**nginx安全配置：**

```nginx
#禁止访问log目录
location /log/ 
{
    return 403;
}
```

**log目录需要给用户执行权限**

### 伪静态

**Nginx**

```nginx
location / {
    if (!-e $request_filename) {
        rewrite ^/(.+)$ /$1.php last;
    }
    # 如果请求的资源存在，则直接提供，否则尝试重写规则
    try_files $uri $uri/ =404;
}
```

**Apache**

```
<IfModule mod_rewrite.c>
    RewriteEngine On

    # 如果请求的资源不存在，则尝试添加 .php 扩展名
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.+)$ $1.php [L]

    # 尝试直接提供请求的资源，如果资源不存在，则返回 404 错误
    RewriteRule ^(.*)$ - [L,NC,F=404]
</IfModule>
```



## 反馈

个人博客：[sfwww.cn](htttps://sfwww.cn)

邮箱：soufan@aliyun.com
