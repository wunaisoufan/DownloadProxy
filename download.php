<?php
// 初始化会话
session_start();

// 定义常量
define('MAX_REDIRECTS', 10);
define('TIMEOUT_SECONDS', 30);
define('LOG_RETENTION_DAYS', 7); // 日志保留天数

// 检查是否提交了表单
if (isset($_POST['url'])) {

    // 输入验证
    $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);

    // 检查 URL 是否有效
    if (filter_var($url, FILTER_VALIDATE_URL)) {

        // 记录日志
        $ip = $_SERVER['REMOTE_ADDR']; // 获取客户端IP地址
        $datetime = date('Y-m-d H:i:s'); // 获取当前日期和时间
        $date = date('Y-m-d'); // 当前日期用于文件名
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'; // 用户代理

        // 获取 HTTP 状态码
        $ch = curl_init(); // 初始化cURL会话
        curl_setopt($ch, CURLOPT_URL, $url); // 设置要访问的URL
        curl_setopt($ch, CURLOPT_NOBODY, true); // 只检查头部，不获取页面内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 将结果作为字符串返回
        curl_exec($ch); // 执行cURL会话
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // 获取HTTP状态码
        curl_close($ch); // 关闭cURL会话

        // 创建日志条目字符串
        $logEntry = "[$datetime] IP: $ip URL: $url User-Agent: $userAgent HTTP Code: $httpStatusCode Token: $token\n";

        // 获取或创建日志文件夹
        $logDirectory = "log/download/$date/"; // 日志目录路径
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true); // 创建目录
        }

        // 获取或创建日志计数文件
        $logCountFile = "log/download/count-$date.txt";

        // 确保计数文件存在
        if (!file_exists($logCountFile)) {
            file_put_contents($logCountFile, 0); // 创建并初始化计数文件
        }

        // 读取今天的请求数量
        $requestCount = (int)file_get_contents($logCountFile); // 将文件内容转换为整数

        // 更新请求数量
        file_put_contents($logCountFile, $requestCount + 1); // 增加计数并保存

        // 创建完整的日志文件名
        $logFilename = "log-$date-" . str_pad($requestCount, 4, '0', STR_PAD_LEFT) . '.txt'; // 文件名格式化

        // 写入日志文件
        file_put_contents("$logDirectory$logFilename", $logEntry, FILE_APPEND | LOCK_EX); // 追加到文件

        // 清理旧日志文件
        $logDirectories = glob('log/download/*', GLOB_ONLYDIR); // 获取所有子目录
        foreach ($logDirectories as $logDir) {
            $dirDate = basename($logDir); // 获取目录名称
            $dirTimestamp = strtotime($dirDate); // 转换为时间戳
            if ($dirTimestamp < time() - LOG_RETENTION_DAYS * 24 * 60 * 60) { // 判断是否超过7天
                $files = glob("$logDir/*"); // 获取目录下所有文件
                foreach ($files as $file) {
                    unlink($file); // 删除文件
                }
                rmdir($logDir); // 删除目录
            }
        }

        // 使用 cURL 获取远程文件
        $ch = curl_init(); // 初始化cURL会话

        // 设置cURL选项
        curl_setopt($ch, CURLOPT_URL, $url); // 设置URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // 直接输出，不返回数据
        curl_setopt($ch, CURLOPT_HEADER, false); // 不返回头部信息
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向
        curl_setopt($ch, CURLOPT_MAXREDIRS, MAX_REDIRECTS); // 限制最大重定向次数
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'); // 设置用户代理
        curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT_SECONDS); // 设置超时时间

        // 支持使用个人访问令牌的身份验证
        if (!empty($token)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: token $token")); // 设置HTTP头
        }

        // 获取远程文件的 MIME 类型
        $headers = get_headers($url, 1);
        $content_type = isset($headers['Content-Type']) ? $headers['Content-Type'] : 'application/octet-stream'; // 获取或默认MIME类型

        // 获取远程文件的文件名
        $file_name = basename(parse_url($url, PHP_URL_PATH)); // 解析URL并获取文件名

        // 检查 Content-Type 是否是压缩格式，并尝试自动修正
        $expected_mime_types = ['application/zip', 'application/x-zip-compressed', 'application/x-tar', 'application/gzip'];
        if (!in_array($content_type, $expected_mime_types)) {
            // 如果 Content-Type 不符合预期，则使用默认的压缩格式
            $content_type = 'application/zip';
        }

        // 设置 Content-Type 和 Content-Disposition 头部
        header("Content-Type: $content_type"); // 设置MIME类型
        header("Content-Disposition: attachment; filename=\"$file_name\""); // 设置下载方式和文件名
        header("Expires: 0"); // 设置过期时间
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); // 设置缓存控制
        header("Pragma: public"); // 设置Pragma

        // 分块下载设置
        $bufferSize = 1024 * 1024; // 设定缓冲区大小为1MB
        $fp = fopen('php://output', 'wb'); // 打开输出流
        curl_setopt($ch, CURLOPT_FILE, $fp); // 设置输出文件句柄
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) use (&$fp, $bufferSize) {
            fwrite($fp, $data); // 将数据写入文件
            return strlen($data); // 返回写入的数据长度
        });

        // 添加断点续传支持
        $size = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD); // 获取文件大小
        $currentPos = ftell($fp); // 获取当前文件位置
        if ($size > 0 && $size != $currentPos) {
            curl_setopt($ch, CURLOPT_RANGE, "$currentPos-"); // 设置范围请求
        }

        curl_exec($ch); // 执行cURL会话

        if (curl_errno($ch)) { // 检查错误
            echo "Error: " . curl_error($ch); // 输出错误信息
        }

        fclose($fp); // 关闭文件句柄
        curl_close($ch); // 关闭cURL会话
    } else {
        echo "Invalid URL"; // 输出无效URL信息
    }
}
?>