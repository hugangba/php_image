<?php
session_start();
require 'github_api.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    // 验证 CSRF 令牌
    $submitted_csrf_token = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || $submitted_csrf_token !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = "CSRF 验证失败，请重试";
        $_SESSION['error'] = true;
        header("Location: index.php");
        exit;
    }

    $file = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/x-icon'];
    $max_size = 10 * 1024 * 1024; // 10MB

    // 获取配置
    $config = include 'config.php';
    $turnstile_secret_key = $config['CLOUDFLARE_TURNSTILE_SECRET_KEY'];

    // 验证 Cloudflare Turnstile
    $turnstile_response = $_POST['cf-turnstile-response'] ?? '';
    if (empty($turnstile_response)) {
        $_SESSION['message'] = "请完成人机验证";
        $_SESSION['error'] = true;
        header("Location: index.php");
        exit;
    }

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret' => $turnstile_secret_key,
        'response' => $turnstile_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $turnstile_result = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!$turnstile_result['success']) {
        $_SESSION['message'] = "人机验证失败";
        $_SESSION['error'] = true;
        header("Location: index.php");
        exit;
    }

    // 验证文件
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = "上传失败：文件错误";
        $_SESSION['error'] = true;
    } elseif (!in_array($file['type'], $allowed_types)) {
        $_SESSION['message'] = "只允许上传 JPG、PNG、GIF 或 ICO 文件";
        $_SESSION['error'] = true;
    } elseif ($file['size'] > $max_size) {
        $_SESSION['message'] = "文件大小不能超过 10MB";
        $_SESSION['error'] = true;
    } else {
        // 生成随机文件名
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $random_string = bin2hex(random_bytes(4)); // 生成8字符随机字符串
        $file_name = time() . '_' . $random_string . '.' . strtolower($extension);
        $temp_path = sys_get_temp_dir() . '/' . $file_name;

        // 移动文件到临时目录
        if (move_uploaded_file($file['tmp_name'], $temp_path)) {
            $github = new GitHubAPI();
            $response = $github->uploadFile($temp_path, $file_name);

            // 删除临时文件
            unlink($temp_path);

            if (isset($response['content']['download_url'])) {
                $_SESSION['message'] = "图片上传成功！";
                $_SESSION['error'] = false;
                $_SESSION['direct_url'] = $response['direct_url'];
            } else {
                $_SESSION['message'] = "上传到 GitHub 失败：" . ($response['message'] ?? '未知错误');
                $_SESSION['error'] = true;
            }
        } else {
            $_SESSION['message'] = "文件处理失败";
            $_SESSION['error'] = true;
        }
    }
} else {
    $_SESSION['message'] = "无效的请求";
    $_SESSION['error'] = true;
}

header("Location: index.php");
exit;
?>
