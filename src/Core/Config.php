<?php

namespace App\Core;

class Config {
    private static $instance = null;
    private $settings = [];

    private function __construct() {
    // In a real application php dotEnv is used.
    // for simplicity and no need for composer here , we read the values from the .env file manully or use environment variables.
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
