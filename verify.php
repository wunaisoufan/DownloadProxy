<?php
session_start();

// 替换为自己的密码
$correct_password = 'sfwww.cn';

// 日志基础目录，设置为当前脚本所在目录下的 /log/login
$logBaseDir = __DIR__ . '/log/login/';

// 检查并创建当日的日志目录
$logDir = $logBaseDir . date('Y-m-d') . '/';
ensureDirectoryExists($logDir);

// 请求计数文件路径
$requestCountFile = $logBaseDir . 'request_count.txt';
ensureFileExists($requestCountFile);

// 清理超过7天的日志文件和目录
cleanOldLogs($logBaseDir);

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_password = $_POST['password'];

    // 原子性增加请求计数并获取新的请求编号
    $requestNumber = incrementRequestCountAtomic($requestCountFile);

    // 构建日志文件名
    $logFileName = date('Ymd_His') . '_' . $requestNumber . '.log';

    // 登录尝试日志信息
    $logMessage = date('Y-m-d H:i:s') . ' - IP: ' . $_SERVER['REMOTE_ADDR'] .
                  ' - UA: ' . $_SERVER['HTTP_USER_AGENT'] .
                  ' - 密码尝试: ' . str_repeat('*', strlen($input_password)) .
                  ' - 登录成功状态: ';

    // 验证密码
    if ($input_password === $correct_password) {
        $_SESSION['verified'] = true;

        // 登录成功，更新日志信息
        $logMessage .= 'true';

        // 记录日志
        writeLogToFile($logDir . $logFileName, $logMessage);

        // 重定向到受保护页面
        header("Location: protected");
        exit();
    } else {
        // 登录失败，更新日志信息
        $logMessage .= 'false';

        // 记录日志
        writeLogToFile($logDir . $logFileName, $logMessage);

        // 提示错误信息
        echo "<p style='color:red;'>密码错误，请重试！<a href='javascript:history.back();'>返回登录页面</a></p>";

        // 定时重定向
        header("Refresh: 3; url=index");
        exit();
    }
}

// 确保目录存在
function ensureDirectoryExists($dirPath) {
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0755, true);
    }
}

// 确保文件存在
function ensureFileExists($filePath) {
    if (!file_exists($filePath)) {
        file_put_contents($filePath, 0);
    }
}

// 原子性增加请求计数
function incrementRequestCountAtomic($filePath) {
    $fp = fopen($filePath, 'c+');
    if ($fp === false) {
        throw new RuntimeException('无法打开文件: ' . $filePath);
    }
    flock($fp, LOCK_EX);
    $currentCount = (int)fread($fp, 1024); // 读取文件中的计数
    $newCount = $currentCount + 1;
    fseek($fp, 0);
    fwrite($fp, $newCount);
    ftruncate($fp, ftell($fp)); // 调整文件大小
    flock($fp, LOCK_UN);
    fclose($fp);
    return $newCount;
}

// 将日志写入文件
function writeLogToFile($filePath, $message) {
    file_put_contents($filePath, $message . PHP_EOL, FILE_APPEND);
}

// 清理过期日志文件和空目录
function cleanOldLogs($baseDir) {
    $files = glob($baseDir . '*/*'); // 获取所有日志文件
    foreach ($files as $file) {
        if (is_file($file) && time() - filemtime($file) > 7 * 24 * 60 * 60) {
            unlink($file); // 删除过期文件
        }
    }
    $dirs = glob($baseDir . '*'); // 获取所有日志目录
    foreach ($dirs as $dir) {
        if (is_dir($dir) && !scandir($dir)) { // 检查目录是否为空
            rmdir($dir); // 删除空目录
        }
    }
}
?>