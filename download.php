<?php
// 初始化会话
session_start();

// 定义常量
define('MAX_REDIRECTS', 10);
define('TIMEOUT_SECONDS', 30);

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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // 不获取页面内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回数据
        curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // 创建日志字符串
        $logEntry = "[$datetime] IP: $ip URL: $url User-Agent: $userAgent HTTP Code: $httpStatusCode Token: $token\n";

        // 获取或创建日志文件夹
        $logDirectory = "log/$date/";
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        // 获取或创建日志文件名
        $logCountFile = "log/count-$date.txt";

        // 确保计数文件存在
        if (!file_exists($logCountFile)) {
            file_put_contents($logCountFile, 0);
        }

        // 读取今天的请求数量
        $requestCount = (int)file_get_contents($logCountFile);

        // 更新请求数量
        file_put_contents($logCountFile, $requestCount + 1);

        // 创建完整的日志文件名
        $logFilename = "log-$date-" . str_pad($requestCount, 4, '0', STR_PAD_LEFT) . '.txt';

        // 写入日志文件
        file_put_contents("$logDirectory$logFilename", $logEntry, FILE_APPEND | LOCK_EX);

        // 清理旧日志文件
        $logDirectories = glob('log/*', GLOB_ONLYDIR);
        foreach ($logDirectories as $logDir) {
            $dirDate = basename($logDir);
            $dirTimestamp = strtotime($dirDate);
            if ($dirTimestamp < time() - 30 * 24 * 60 * 60) { // 30 days
                $files = glob("$logDir/*");
                foreach ($files as $file) {
                    unlink($file);
                }
                rmdir($logDir);
            }
        }

        // 使用 cURL 获取远程文件
        $ch = curl_init();

        // 设置cURL选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // 不返回数据，直接输出
        curl_setopt($ch, CURLOPT_HEADER, false); // 不返回头部信息
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 关键：跟随重定向
        curl_setopt($ch, CURLOPT_MAXREDIRS, MAX_REDIRECTS); // 限制最大重定向次数
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');

        // 支持使用个人访问令牌的身份验证
        if (!empty($token)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: token $token"));
        }

        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT_SECONDS);

        // 获取远程文件的 MIME 类型
        $headers = get_headers($url, 1);
        $content_type = isset($headers['Content-Type']) ? $headers['Content-Type'] : 'application/octet-stream';

        // 获取远程文件的文件名
        $file_name = basename(parse_url($url, PHP_URL_PATH));

        // 检查 Content-Type 是否是压缩格式，并尝试自动修正
        $expected_mime_types = ['application/zip', 'application/x-zip-compressed', 'application/x-tar', 'application/gzip'];
        if (!in_array($content_type, $expected_mime_types)) {
            // 如果 Content-Type 不符合预期，则使用默认的压缩格式
            $content_type = 'application/zip';
        }

        // 设置 Content-Type 和 Content-Disposition 头部
        header("Content-Type: $content_type");
        header("Content-Disposition: attachment; filename=\"$file_name\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");

        // 设置流式传输
        $fp = fopen('php://output', 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);

        // 执行 cURL 请求
        curl_exec($ch);

        if (curl_errno($ch)) {
            echo "Error: " . curl_error($ch);
        }

        // 关闭文件指针
        fclose($fp);
        curl_close($ch);
    } else {
        echo "Invalid URL";
    }
}
?>