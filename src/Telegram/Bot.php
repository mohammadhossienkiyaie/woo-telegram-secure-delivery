<?php

namespace App\Telegram;

use App\Core\Config;
use App\Core\Logger;

class Bot {
    private $api_url;
    private $token;

    public function __construct() {
        $config = Config::getInstance();
        $this->token = $config->get('TELEGRAM_BOT_TOKEN');
        $this->api_url = "https://api.telegram.org/bot{$this->token}/";
    }

    public function sendRequest($method, $params = [], $timeout = 30) {
        $url = $this->api_url . $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            Logger::error("CURL Error ($method): " . curl_error($ch));
        }
        curl_close($ch);
        
        $response = json_decode($result, true);
        if (!$response || !$response['ok']) {
            Logger::error("Telegram API Error ($method): " . $result);
        }
        
        return $response;
    }

    public function sendMessage($chat_id, $text, $parse_mode = 'Markdown') {
        return $this->sendRequest('sendMessage', [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => $parse_mode
        ]);
    }

    public function copyMessage($chat_id, $from_chat_id, $message_id) {
        return $this->sendRequest('copyMessage', [
            'chat_id' => $chat_id,
            'from_chat_id' => $from_chat_id,
            'message_id' => $message_id,
            'protect_content' => true
        ], 60);
    }
}
