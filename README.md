🚀 Secure WooCommerce-Telegram File Delivery System
A professional, enterprise-grade PHP solution designed to automate the secure delivery of WooCommerce digital products via Telegram bots. This system bypasses server bandwidth limits by using Telegram's native infrastructure while ensuring high-level content protection.

🌟 Key Features
Automated Order Verification: Real-time validation of WooCommerce purchase tokens (Order IDs).

Anti-Piracy Protection: Leverages Telegram's protect_content feature to prevent forwarding, saving, or capturing of digital assets.

Resource Efficiency: Files are copied directly from a private source channel to the user, saving your server's bandwidth.

Anti-Timeout Logic: Implements set_time_limit(0) to handle massive file batches without server crashes.

Security First: Uses a secret webhook token to verify that requests only come from official Telegram servers.


📂 Project Architecture (OOP)

The project is built with a modular, Object-Oriented approach for maximum maintainability:

src/Core/Config.php: Manages environment variables and .env loading.

src/Core/Logger.php: Comprehensive logging system for events and errors.

src/Telegram/Bot.php: Core class for Telegram API interactions (sending messages, copying files).

src/WordPress/OrderManager.php: Bridge to WooCommerce for verifying order status and purchase history.

src/Core/Handler.php: The "brain" of the application that processes incoming webhook requests.

webhook.php: The secure entry point for the Telegram Webhook.

🛠 Setup & Installation

1. Prerequisites

PHP 7.4+ and WordPress/WooCommerce installation.

A Private Telegram Channel containing your digital products.

A Telegram Bot token (from @BotFather).

2. Configuration (.env)

Rename .env.example to .env and configure your credentials:

TELEGRAM_BOT_TOKEN=YOUR_BOT_TOKEN
SOURCE_CHANNEL_ID=-100XXXXXXXXXX
WEBHOOK_SECRET_TOKEN=YOUR_SECRET_UUID
WP_LOAD_PATH=../wp-load.php
LOG_FILE=logs/telegram.log


3. Product Mapping
Open webhook.php and configure the $productMap array. This links your WooCommerce Product IDs to Telegram Message IDs:

$productMap = [
    123 => ['1001', '1002'], // Product ID => [Message ID 1, Message ID 2]
    456 => ['2001']         // Product ID => [Single File]
];

4. Setting the Webhook

Run the following cURL command to link Telegram to your script:


curl -F "url=https://yourdomain.com/path/to/webhook.php" \
     -F "secret_token=YOUR_SECRET_TOKEN" \
     https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook


🛡 Security & Debugging

Logging: Check logs/telegram.log for a detailed history of API responses and errors.

Permissions: Ensure the logs/ directory is writable (chmod -R 775).

Database: All queries use WordPress native database security standards.

🤝 Contributing

Contributions are welcome! feel free to submit a Pull Request.