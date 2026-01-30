<?php

require_once __DIR__ . '/src/Core/Config.php';
require_once __DIR__ . '/src/Core/Logger.php';
require_once __DIR__ . '/src/Core/Handler.php';
require_once __DIR__ . '/src/Telegram/Bot.php';
require_once __DIR__ . '/src/WordPress/OrderManager.php';

use App\Core\Config;
use App\Core\Handler;
use App\Core\Logger;

// 1. تنظیمات اولیه
$config = Config::getInstance();
$secretToken = $config->get('WEBHOOK_SECRET_TOKEN');

// 2. امنیت: تایید اصالت درخواست از طرف تلگرام
if ($secretToken) {
    $headerToken = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';
    if ($headerToken !== $secretToken) {
        Logger::error("Unauthorized access attempt from IP: " . $_SERVER['REMOTE_ADDR']);
        http_response_code(403);
        exit('Unauthorized');
    }
}

// product map 
$productMap = [

];


$content = file_get_contents("php://input");
$update = json_decode($content, true);

if ($update) {
    $handler = new Handler($productMap);
    $handler->handle($update);
}
