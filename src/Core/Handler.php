<?php

namespace App\Core;

use App\Telegram\Bot;
use App\WordPress\OrderManager;

class Handler {
    private $bot;
    private $orderManager;
    private $config;
    private $productMap;

    public function __construct($productMap) {
        $this->config = Config::getInstance();
        $this->bot = new Bot();
        $this->orderManager = new OrderManager();
        $this->productMap = $productMap;
    }

    public function handle($update) {
        if (!isset($update['message']['chat']['id'])) return;

        $chat_id = $update['message']['chat']['id'];
        $text = isset($update['message']['text']) ? trim($update['message']['text']) : '';

        if (strpos($text, '/start') === 0) {
            $this->handleStart($chat_id);
        } elseif (is_numeric($text)) {
            $this->handleToken($chat_id, $text);
        } else {
            $this->bot->sendMessage($chat_id, "لطفاً فقط توکن خود را برای دریافت فایل‌ها ارسال کنید.");
        }
    }

    private function handleStart($chat_id) {
        $this->bot->sendMessage($chat_id, "سلام 👋\nبه ربات دانلود مرووی خوش اومدی\nبا وارد کردن توکن خرید، فایل‌های دانلود رو دریافت کنید.\n\n(عدد توکن را به صورت انگلیسی بفرستید)");
    }

    private function handleToken($chat_id, $token) {
        $status = $this->orderManager->getTokenStatus($token);

        if ($status === 'error') {
            $this->bot->sendMessage($chat_id, "❌ خطای سیستم: اتصال به سایت برقرار نشد.");
            return;
        }

        if ($status !== 'valid') {
            $msg = ($status === 'used') ? "توکن $token قبلاً استفاده شده است." : "توکن $token نامعتبر است.";
            $this->bot->sendMessage($chat_id, $msg);
            return;
        }

        if (!$this->orderManager->lockToken($token)) {
            $this->bot->sendMessage($chat_id, "خطا در پردازش توکن. لطفاً مجدد تلاش کنید.");
            return;
        }

        $this->bot->sendMessage($chat_id, "توکن تایید شد ✅\nدر حال ارسال فایل‌ها...");
        $this->processDelivery($chat_id, $token);
    }

    private function processDelivery($chat_id, $token) {
        $productIds = $this->orderManager->getPurchasedProductIds($token);
        $sourceChannel = $this->config->get('SOURCE_CHANNEL_ID');
        $filesSent = 0;

        foreach ($productIds as $pId) {
            if (!isset($this->productMap[$pId])) continue;

            foreach ($this->productMap[$pId] as $fileId) {
                $response = $this->bot->copyMessage($chat_id, $sourceChannel, $fileId);
                if ($response && $response['ok']) {
                    $filesSent++;
                }
                usleep(500000); // 0.5s delay
            }
        }

        if ($filesSent > 0) {
            $this->orderManager->markAsUsed($token);
            $this->bot->sendMessage($chat_id, "✅ فایل‌ها با موفقیت ارسال شدند.");
        } else {
            $this->bot->sendMessage($chat_id, "⚠️ خطایی در ارسال فایل‌ها رخ داد.");
        }
    }
}
