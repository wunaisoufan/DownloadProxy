<?php
// 启动会话
session_start();

// 如果已经验证过，重定向至受保护页面
if (isset($_SESSION['verified']) && $_SESSION['verified'] === true) {
    header('Location: protected');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 下载代理</title>
    <!-- 引入Bootstrap CSS，使用jsDelivr CDN以提升性能 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- 引入Font Awesome CSS，使用jsDelivr CDN以提升性能 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <!-- 自定义样式 -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            animation: fadeIn 1s ease-in-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-form h1 {
            margin-bottom: 2rem;
            color: #343a40;
        }
        .login-form label {
            color: #6c757d;
        }
        .login-form input {
            border-color: #ced4da;
        }
        .login-form button {
            width: 100%;
            background-color: #007bff;
            border-color: #007bff;
            transition: all 0.3s ease-in-out;
        }
        .login-form button:hover {
            background-color: #0062cc;
            border-color: #005cbf;
        }
        .footer-links {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
        }
        .footer-links a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease-in-out;
        }
        .footer-links a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h1>下载代理</h1>
        <form action="verify.php" method="POST">
            <p class="text-center">请输入密码以访问下载功能：</p>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" required>
                <label for="password">密码</label>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-unlock-alt"></i> 解锁</button>
        </form>
    </div>
    <div class="footer-links">
        <p>
            <a href="https://sfwww.cn/" target="_blank">
                <i class="fas fa-link mr-1"></i>馊了的饭
            </a>
            <br>
            <a href="https://github.com/wunaisoufan/DownloadProxy" target="_blank">
                <i class="fab fa-github-square mr-1"></i>DownloadProxy-Github
            </a>
        </p>
    </div>
</body>
</html>