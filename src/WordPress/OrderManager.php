<?php

namespace App\WordPress;

use App\Core\Config;
use App\Core\Logger;

class OrderManager {
    public function __construct() {
        $this->loadWordPress();
    }

    private function loadWordPress() {
        if (defined('ABSPATH')) return;
        
        $config = Config::getInstance();
        $wp_path = dirname(__DIR__, 2) . '/' . $config->get('WP_LOAD_PATH', '../wp-load.php');
        
        if (file_exists($wp_path)) {
            @require_once($wp_path);
        } else {
            Logger::error("WordPress environment not found at: $wp_path");
        }
    }

    public function getTokenStatus($token) {
        if (!defined('ABSPATH')) return 'error';
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'telegram_tokens';
        $result = $wpdb->get_row($wpdb->prepare("SELECT status FROM $table_name WHERE token = %s", $token));
        
        return $result ? $result->status : 'not_found';
    }

    public function lockToken($token) {
        if (!defined('ABSPATH')) return false;
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'telegram_tokens';
        return $wpdb->update(
            $table_name,
            ['status' => 'processing'],
            ['token' => $token, 'status' => 'valid'],
            ['%s'],
            ['%s', '%s']
        ) > 0;
    }

    public function markAsUsed($token) {
        if (!defined('ABSPATH')) return false;
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'telegram_tokens';
        return $wpdb->update($table_name, ['status' => 'used'], ['token' => $token]) !== false;
    }

    public function getPurchasedProductIds($order_id) {
        if (!function_exists('wc_get_order')) return [];
        
        $order = wc_get_order($order_id);
        if (!$order) return [];
        
        $ids = [];
        foreach ($order->get_items() as $item) {
            $ids[] = $item->get_variation_id() ?: $item->get_product_id();
        }
        return array_unique(array_filter($ids));
    }
}
