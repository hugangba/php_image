<?php
session_start();
$config = include 'config.php';
$turnstile_site_key = $config['CLOUDFLARE_TURNSTILE_SITE_KEY'];

// 生成 CSRF 令牌
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>图库系统</title>
    <link href="https://cdn.freenn.top/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full max-w-2xl">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">图库系统</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">上传图片</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data" class="space-y-4">
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/x-icon" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <div class="cf-turnstile" data-sitekey="<?php echo htmlspecialchars($turnstile_site_key); ?>" data-theme="light"></div>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">上传</button>
        </form>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="mt-4 text-center <?php echo $_SESSION['error'] ? 'text-red-500' : 'text-green-500'; ?>">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
            </p>
            <?php if (isset($_SESSION['direct_url'])): ?>
                <p class="mt-2 text-center text-gray-600 px-4 overflow-wrap break-word">
                    图片直链: <a href="<?php echo htmlspecialchars($_SESSION['direct_url']); ?>" target="_blank" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($_SESSION['direct_url']); ?></a>
                </p>
            <?php endif; ?>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['error']);
            unset($_SESSION['direct_url']);
            ?>
        <?php endif; ?>
    </div>
</body>
</html>
