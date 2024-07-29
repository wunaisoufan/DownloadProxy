# DownloadProxy

通过PHP搭建一个下载代理的WEB站点

*通过AI帮助弄出来的个小玩意，安全性不高，自己搭建了用一用吧*

访问站点有密码（安全性低、不需要自行删除即可），流式传输、支持Github个人令牌token、有log日志（会保存Token、IP、文件URL等）

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

## 反馈

个人博客：[sfwww.cn](htttps://sfwww.cn)

邮箱：soufan@aliyun.com
