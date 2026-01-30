<?php

namespace App\Core;

class Config {
    private static $instance = null;
    private $settings = [];

    private function __construct() {
        // در یک پروژه واقعی از PHP DotEnv استفاده می‌شود.
        // برای سادگی و عدم نیاز به کامپوزر در اینجا، مقادیر را از فایل .env دستی می‌خوانیم یا از متغیرهای محیطی استفاده می‌کنیم.
        $this->loadFromEnv();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadFromEnv() {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                list($name, $value) = explode('=', $line, 2);
                $this->settings[trim($name)] = trim($value);
            }
        }
    }

    public function get($key, $default = null) {
        return $this->settings[$key] ?? $default;
    }
}
