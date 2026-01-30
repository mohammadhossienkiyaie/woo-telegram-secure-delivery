<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ⚠️ حذف محدودیت زمانی برای این فرایند طولانی (مهم)
ini_set('max_execution_time', 0);
set_time_limit(0);

// --- 1. تنظیمات و متغیرهای اصلی ---
$bot_token = '8268122920:AAFIp_KPlAIUlC5FqRhuIK1Th9aRMu4cC44';
$telegram_api = "https://api.telegram.org/bot$bot_token/";
$log_file = __DIR__ . '/telegram_debug.log';
$SOURCE_CHANNEL_ID = '-1003012137752'; 


// --- 2. توابع تلگرام و وردپرس ---

function send_telegram_request($method, $params = array(), $timeout = 5) {
    global $telegram_api, $log_file;
    $url = $telegram_api . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // تایم‌آوت دینامیک: برای ارسال فایل طولانی‌تر است
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - CURL Error (send_telegram_request - $method): " . curl_error($ch) . "\n", FILE_APPEND);
    }
    curl_close($ch);
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Telegram Response ($method): " . $result . "\n", FILE_APPEND);
    return json_decode($result, true);
}

function send_telegram_copy($chat_id, $from_chat_id, $message_id) {
    // تایم‌آوت طولانی‌تر برای اطمینان از ارسال فایل
    $params = [
        'chat_id' => $chat_id, 'from_chat_id' => $from_chat_id, 'message_id' => $message_id,
        'disable_notification' => true, 'protect_content' => true, 'allow_sending_without_reply' => true,
    ];
    // استفاده از 30 ثانیه تایم‌آوت برای ارسال فایل
    return send_telegram_request('copyMessage', $params, 30); 
}

function load_wordpress_environment() {
    if (defined('ABSPATH')) { return true; }
    $wp_load_path = dirname( __FILE__ ) . '/wp-load.php';
    if (file_exists($wp_load_path)) {
        @require_once($wp_load_path);
        return defined('ABSPATH');
    }
    return false;
}

function get_token_status($order_id) {
    if (!load_wordpress_environment()) { return 'error'; }
    global $wpdb;
    $table_name = $wpdb->prefix . 'telegram_tokens';
    $result = $wpdb->get_row( $wpdb->prepare("SELECT status FROM $table_name WHERE token = %s", $order_id) );
    if (!$result) { return 'not_found'; }
    return $result->status;
}

function lock_token_for_processing($order_id) {
    if (!load_wordpress_environment()) { return false; }
    global $wpdb;
    $table_name = $wpdb->prefix . 'telegram_tokens';
    $updated = $wpdb->update( 
        $table_name, 
        ['status' => 'processing'], 
        ['token' => $order_id, 'status' => 'valid'], 
        ['%s'], 
        ['%s', '%s'] 
    );
    return $updated > 0;
}

function mark_token_as_used($order_id) {
    if (!load_wordpress_environment()) { return false; }
    global $wpdb;
    $table_name = $wpdb->prefix . 'telegram_tokens';
    $updated = $wpdb->update( $table_name, ['status' => 'used'], ['token' => $order_id], ['%s'], ['%s'] );
    return $updated !== false;
}

function get_purchased_product_ids($order_id) {
    // اطمینان از اینکه توابع ووکامرس وجود دارند
    if (!function_exists('wc_get_order')) { return []; } 
    $order = wc_get_order($order_id);
    if (!$order) { return []; }
    $purchased_product_ids = [];
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
        if ($product_id) {
            $purchased_product_ids[] = $product_id;
        }
    }
    return array_unique($purchased_product_ids);
}


// --- 3. آرایه مپینگ فایل‌ها ---
$product_file_map = array(
    1647 => [
        '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
        '21', '23', '24', '25', '26', '27', '28', '94', '95', '96', '97', '98', '99', '100', '101',
        '102', '103', '104', '105', '106', '107', '204', '205', '206', '207', '208', '209', '210',
        '211', '212', '68', '69', '129', '130', '61', '131', '132', '133', '70', '71', '72', '73',
        '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88',
        '29', '31', '30', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43',
        '44', '46', '45', '112', '113', '114', '115', '116', '117', '118', '119', '120', '121', '122',
        '123', '124', '125', '126', '129', '130', '136', '137', '138', '141', '140', '139', '142',
        '143', '144', '146', '145', '147', '148', '149', '150', '151', '152', '153', '154', '155', '156',
        '157', '158', '159', '160', '161', '162', '163', '164', '165', '166', '167', '168', '169', '170', '171',
        '172', '173', '174', '175', '176', '177', '178', '179', '180', '181', '182', '183', '184',
        '185', '186', '187', '188', '189', '190', '191', '192', '193', '194', '195', '196',
        '198', '199', '200', '201', '203', '218', '219', '220', '221', '222', '223', '224', '225',
        '226', '227', '228', '229', '230', '231', '232', '234', '235', '237', '238', '239', '240',
        '241', '242', '243', '244', '246', '247', '245', '248','62', '63', '64', '65', '65', '67' , '213' , '214' , '215' , '216', '217' ,
         '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59' , '60' , '249'
    ],
    69 => ['4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '23', '24', '25', '26', '27', '28'],
    1323 => ['94', '95', '96', '97', '98', '99', '100', '101', '102', '103', '104', '105', '106', '107' , '204' , '205', '206', '207', '208', '209', '210', '211' , '212' ],
    1571 => [ '62', '63', '64', '65', '65', '67' ,'213' , '214' , '215' , '216' , '217' ],
    1304 => ['68', '69'],
    1381 => ['129', '130'],
    1170 => ['4', '5'],
    1141 => ['61'],
    1387 => ['131', '132', '133'],
    1378 => ['70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88'],
    1345 => ['108', '109', '110', '111'],
    1948 => ['250', '251', '252', '253' , '254' , '255' , '256' , '257' , '258' , '259' , '260' , '261' , '262' , '263' , '264' , '265' , '266' , '267' , '268' , '269' , '270' , '271' , '272' , '273' , '274' , '275' , '276' , '277' , '278' , '279' , '280' , '281'],
    1697 => ['136', '137', '138', '141' , '140' , '139' , '271' , '272' ],
    1102 => ['29', '31', '30', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '46', '45'],
    2025 => ['112', '113', '114', '115', '116', '117', '118', '119', '120', '121', '122', '123', '124', '125', '126', '282' , '283' , '284' , '285' , '286' , '287' ],
    1488 => ['4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '23', '24', '25', '26', '27', '28', '61', '68', '69', '134', '135'],
    1717 => ['154', '155', '156', '157', '158', '159', '160', '161', '162', '163', '164', '165', '166', '167', '168', '169', '170', '171', '172', '173', '174', '175', '176', '177', '178', '179', '180', '181', '182' ,
            '183', '184', '185', '186', '187', '188', '189', '190', '191', '192', '193', '194', '195', '196', '198', '199', '200', '201', '202', '203'],
            
);

// --- 4. تابع اصلی: پردازش توکن و ارسال فایل (اجرای مستقیم و سنگین) ---

function process_token_execution($order_token, $chat_id, $product_file_map) {
    global $SOURCE_CHANNEL_ID, $log_file;

    // 4.1. اتصال به وردپرس 
    if (!load_wordpress_environment()) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - HANDLER FATAL (DIRECT): Failed to load WordPress environment.\n", FILE_APPEND);
        send_telegram_request('sendMessage', [ 'chat_id' => $chat_id, 'text' => '❌ خطای سیستم: اتصال به سایت برقرار نشد. فایل‌ها ارسال نشدند. لطفاً به پشتیبانی اطلاع دهید.', ], 10);
        return; 
    }
    
    // 4.2. دریافت محصولات
    $purchased_ids = get_purchased_product_ids($order_token);

    if (empty($purchased_ids)) {
        mark_token_as_used($order_token);
        send_telegram_request('sendMessage', [ 'chat_id' => $chat_id, 'text' => "سفارش شما ($order_token) تأیید شد، اما هیچ محصول دانلودی در آن وجود نداشت. این کد امنیتی دیگر قابل استفاده نیست.", ], 10);
        return;
    }

    $files_sent_count = 0;

    // *** تنظیمات ارسال دسته‌ای ***
    $batch_counter = 0;
    $BATCH_SIZE = 15; 
    $BATCH_DELAY = 3; 

    foreach ($purchased_ids as $p_id) {

        if (isset($product_file_map[$p_id]) && is_array($product_file_map[$p_id]) && !empty($product_file_map[$p_id])) {

            $product_name = function_exists('wc_get_product') ? (wc_get_product($p_id) ? wc_get_product($p_id)->get_name() : "محصول با شناسه $p_id") : "محصول با شناسه $p_id";
            $file_list = $product_file_map[$p_id];

            send_telegram_request('sendMessage', [
                'chat_id' => $chat_id, 'text' => "📦 **فایل‌های پکیج:** $product_name", 'parse_mode' => 'Markdown',
            ], 10); // پاسخ اولیه، تایم‌آوت کوتاه‌تر

            foreach ($file_list as $file_or_message_id) {

                // 1. ارسال فایل
                if (is_numeric($file_or_message_id) && strlen($file_or_message_id) < 10) {
                    $response = send_telegram_copy($chat_id, $SOURCE_CHANNEL_ID, $file_or_message_id);
                } else {
                    $params = ['chat_id' => $chat_id, 'document' => $file_or_message_id, 'parse_mode' => 'Markdown', 'disable_notification' => true, 'protect_content' => true,];
                    $response = send_telegram_request('sendDocument', $params, 30); // تایم‌آوت طولانی‌تر برای سند
                }

                if ($response && $response['ok']) {
                    $files_sent_count++;
                    $batch_counter++; 
                } else {
                    file_put_contents($log_file, date('Y-m-d H:i:s') . " - HANDLER ERROR (DIRECT): Failed to send file $file_or_message_id for token $order_token. Response: " . json_encode($response) . "\n", FILE_APPEND);
                    send_telegram_request('sendMessage', [ 'chat_id' => $chat_id, 'text' => "❌ خطایی در ارسال فایل با Message ID یا File ID: **$file_or_message_id** رخ داد. لطفاً به پشتیبانی اطلاع دهید.", 'parse_mode' => 'Markdown'], 10);
                }

                // 2. اعمال تاخیر دسته‌ای
                if ($batch_counter >= $BATCH_SIZE) {
                    sleep($BATCH_DELAY); 
                    $batch_counter = 0; 
                }
            }
        } else {
             send_telegram_request('sendMessage', [ 'chat_id' => $chat_id, 'text' => "❗️ فایل تلگرامی برای محصول با شناسه **$p_id** در سیستم تعریف نشده است. لطفاً به پشتیبانی اطلاع دهید.", 'parse_mode' => 'Markdown'], 10);
        }
    }

    // 4.3. نهایی کردن وضعیت توکن
    if ($files_sent_count > 0) {
        mark_token_as_used($order_token);
        send_telegram_request('sendMessage', ['chat_id' => $chat_id,'text' => "✅ فایل‌های خریداری شده شما به صورت کامل ارسال شدند.\nتوکن ($order_token) منقضی شد.",], 10);
    } else {
        // اگر هیچ فایلی ارسال نشد، توکن در وضعیت 'processing' باقی می‌ماند تا مدیر سایت آن را بررسی و وضعیت آن را اصلاح کند.
        send_telegram_request('sendMessage', [ 'chat_id' => $chat_id, 'text' => "⚠️ فرآیند ارسال فایل‌ها موفقیت‌آمیز نبود و هیچ فایلی ارسال نشد. لطفاً به پشتیبانی اطلاع دهید تا وضعیت توکن شما را بررسی کنند.", ], 10);
    }
    
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - HANDLER (DIRECT): Execution finished for token $order_token.\n", FILE_APPEND);
}


// --- 5. WEBHOOK PROCESSING (نقطه ورود) ---
$content = file_get_contents("php://input");
file_put_contents($log_file, "\n\n" . date('Y-m-d H:i:s') . " - Input Received: " . $content . "\n", FILE_APPEND);

$update = json_decode($content, true);

// خروج اگر پیام معتبری دریافت نشده باشد
if (!isset($update['message']['chat']['id'])) { exit; }

$chat_id = $update['message']['chat']['id'];
$text = isset($update['message']['text']) ? trim($update['message']['text']) : '';

// پاسخ به /start
if (strpos($text, '/start') === 0) {
    send_telegram_request('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "سلام 👋\nبه ربات دانلود مرووی خوش اومدی\nبا وارد کردن توکن خرید، فایل‌های دانلود رو دریافت کنید.\n\n(عدد توکن را به صورت انگلیسی بفرستید)",
    ]);
    exit;
}
// پردازش توکن
else if (is_numeric($text) && strlen($text) > 0) {
    $order_token = $text;

    $token_status = get_token_status($order_token);

    if ($token_status === 'error') {
        send_telegram_request('sendMessage', [ 'chat_id' => $chat_id, 'text' => '❌ خطای سیستم: اتصال به سایت برقرار نشد. لطفاً به پشتیبانی اطلاع دهید.', ]);
        exit;
    }
    if ($token_status !== 'valid') {
        switch ($token_status) {
            case 'used':
                send_telegram_request('sendMessage', [ 'chat_id' => $chat_id, 'text' => "توکن $order_token قبلاً استفاده شده و منقضی شده است. این کد تنها یک بار قابل استفاده است.", ]);
                break;
            case 'not_found':
                send_telegram_request('sendMessage', [ 'chat_id' => $chat_id, 'text' => "توکن $order_token نامعتبر است. لطفاً توکن را به درستی وارد کنید.", ]);
                break;
        }
        exit;
    }

    // A. قفل کردن توکن
    if (!lock_token_for_processing($order_token)) {
        send_telegram_request('sendMessage', [ 'chat_id' => $chat_id, 'text' => "خطا در قفل کردن توکن. لطفاً مجدداً تلاش کنید.", ]);
        exit;
    }

    // B. شروع پردازش مستقیم (Sync) و ارسال پیام تایید ساده
    send_telegram_request('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "توکن شما تایید شد ✅\nفایل‌های دانلود در حال ارسال هستند. لطفاً تا پایان عملیات، صبور باشید.",
    ]);

    // C. اجرای منطق اصلی
    process_token_execution($order_token, $chat_id, $product_file_map);

}
else {
     send_telegram_request('sendMessage', [
         'chat_id' => $chat_id,
         'text' => "لطفاً فقط توکن خود را برای دریافت فایل‌ها ارسال کنید.",
     ]);
}