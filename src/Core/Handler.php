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
            $this->bot->sendMessage($chat_id, "Please just send your token to receive the files.");
        }
    }

    private function handleStart($chat_id) {
        $this->bot->sendMessage($chat_id, "Hello 👋\nWelcome to the Merovi Download Robot\nReceive the download files by entering the purchase token.\n\n(Send the token number in English)");
    }

    private function handleToken($chat_id, $token) {
        $status = $this->orderManager->getTokenStatus($token);

        if ($status === 'error') {
            $this->bot->sendMessage($chat_id, "❌ System error: Connection to the site could not be established.");
            return;
        }

        if ($status !== 'valid') {
            $msg = ($status === 'used') ? "The token $token has already been used." : "The token $token is invalid.";
            $this->bot->sendMessage($chat_id, $msg);
            return;
        }

        if (!$this->orderManager->lockToken($token)) {
            $this->bot->sendMessage($chat_id, "Error processing token. Please try again.");
            return;
        }

        $this->bot->sendMessage($chat_id, "Token verified ✅\nSending files...");
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
            $this->bot->sendMessage($chat_id, "✅ Files sent successfully.");
        } else {
            $this->bot->sendMessage($chat_id, "⚠️ An error occurred while sending files.");
        }
    }
}
