<?php
session_start();
if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
    header("Location: index");
    exit();
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
                        <form action="download" method="post">
                            <div class="form-group">
                                <label for="url">输入URL：</label>
                                <input type="text" class="form-control" id="url" name="url" placeholder="示例：https://example.com/file.pdf" required>
                            </div>
                            <div class="form-group">
                                <label for="token">GitHub Personal Access Token (可选)：</label>
                                <input type="text" class="form-control" id="token" name="token">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> 下载</button>
                        </form>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <p>
                        <a href="https://sfwww.cn/" target="_blank" class="text-dark">
                            <i class="fas fa-link mr-1"></i>SouFan的博客
                        </a>
                        <a href="https://github.com/wunaisoufan/DownloadProxy" target="_blank" class="text-dark">
                            <i class="fas fa-link mr-1"></i>DownloadProxy-Github
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>