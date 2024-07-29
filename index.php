<?php
// 清除任何之前可能存在的 session 数据
session_start();
if (isset($_SESSION['verified'])) {
    unset($_SESSION['verified']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>下载代理（且用且珍惜） - SouFan</title>
    <link rel="stylesheet" href="https://jsdelivr.alimama.uk/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <script src="https://jsdelivr.alimama.uk/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        }
        /* Add some styles for the link */
        .text-dark:hover {
            color: #007bff !important;
            text-decoration: none;
        }
        .text-dark i {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="text-center mb-0">下载代理</h1>
                    </div>
                    <div class="card-body">
                        <form action="verify.php" method="POST">
                            <p>请输入密码以访问下载功能：</p>
                            <div class="form-group">
                                <label for="password">密码：</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-unlock-alt"></i> 解锁</button>
                        </form>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <p>
                        <a href="https://sfwww.cn/" target="_blank" class="text-dark">
                            <i class="fas fa-link mr-1"></i>SouFan的博客
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>