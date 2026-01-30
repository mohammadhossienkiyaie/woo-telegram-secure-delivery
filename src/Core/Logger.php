<?php

namespace App\Core;

class Logger {
    public static function log($message, $level = 'INFO') {
        $config = Config::getInstance();
        $logFile = dirname(__DIR__, 2) . '/' . $config->get('LOG_FILE', 'logs/telegram.log');
        
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message\n";
        
        file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }

    public static function error($message) {
        self::log($message, 'ERROR');
    }
}
