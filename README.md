<div align="center">
  <img src="https://img.icons8.com/color/96/000000/telegram-app.png" width="80" alt="Telegram Logo" />
  <img src="https://img.icons8.com/color/96/000000/wordpress.png" width="80" alt="WordPress Logo" />
  
  <h1>🚀 Secure WooCommerce-Telegram Delivery System</h1>
  
  <p><b>An enterprise-grade PHP automation for protected digital product distribution via Telegram bots.</b></p>

  <p>
    <img src="https://img.shields.io/badge/PHP-7.4+-777bb4.svg?style=flat-square&logo=php" alt="PHP Version" />
    <img src="https://img.shields.io/badge/WordPress-Core-21759b.svg?style=flat-square&logo=wordpress" alt="WordPress Core" />
    <img src="https://img.shields.io/badge/Telegram-Bot_API-26A5E4.svg?style=flat-square&logo=telegram" alt="Telegram API" />
    <img src="https://img.shields.io/badge/Security-Protected-green.svg?style=flat-square" alt="Security" />
  </p>
</div>

---

## 📖 Introduction
This project provides a robust and secure solution for automating the delivery of WooCommerce digital products. By bridging the **WooCommerce Database** and **Telegram Bot API**, it allows customers to receive their purchased files securely within Telegram.

### 🔄 How It Works
1. **Purchase**: The customer buys a digital product on your WooCommerce site and receives an Order ID (Security Token).
2. **Verification**: The customer sends the Order ID to your Telegram bot.
3. **Validation**: The bot connects to WordPress, verifies the order status, and identifies the linked products.
4. **Protected Delivery**: The bot copies the file from your **Private Source Channel** to the user.
5. **Security Policy**: The `protect_content` feature is enabled, **preventing users from forwarding, saving, or capturing screenshots** of your premium content.

---

## ✨ Core Features
* **🛡️ Content Protection**: Prevents unauthorized distribution by disabling forwarding and saving features in Telegram.
* **🔑 Smart Token System**: Validates WooCommerce Order IDs and implements a "One-Time Use" policy to prevent abuse.
* **⚡ High Performance**: Uses `set_time_limit(0)` and native Telegram file copying to handle large files and prevent server timeouts.
* **☁️ Zero Bandwidth Cost**: Files are transferred directly between Telegram servers; your web host bandwidth is not consumed for file delivery.
* **📝 Comprehensive Logging**: Every transaction, API call, and error is recorded in a secure log file for easy debugging.

---

## 📂 Project Structure (OOP)
The project follows a modular Object-Oriented design for high maintainability:

```text
├── src/
│   ├── Core/
│   │   ├── Config.php       # Environment Variable & .env Management
│   │   ├── Logger.php       # Event Tracking and Error Logging
│   │   └── Handler.php      # Main Logic Engine
│   ├── Telegram/
│   │   └── Bot.php          # Telegram API Interaction Layer
│   └── WordPress/
│       └── OrderManager.php # WooCommerce Database Bridge
├── webhook.php              # Secure Webhook Entry Point
├── .env.example             # Template for Environment Configuration
├── .gitignore               # Prevents sensitive files from being public
└── logs/                    # Directory for log files (Writable)
🛠️ Step-by-Step Installation
1. Server Setup
Upload the project files to your server.

Ensure the logs/ directory has write permissions: chmod -R 775 logs.

2. Environment Configuration
Rename .env.example to .env and fill in your private credentials:

Code snippet
TELEGRAM_BOT_TOKEN=your_bot_token_here
SOURCE_CHANNEL_ID=-100xxxxxxxxxx
WEBHOOK_SECRET_TOKEN=your_secret_uuid
WP_LOAD_PATH=../wp-load.php
3. Product Mapping
In webhook.php, link your WooCommerce Product IDs to your Telegram Source Message IDs:

PHP
$productMap = [
    123 => ['1001', '1002'], // WooCommerce Product ID => [Telegram Message IDs]
];
4. Setting the Webhook
Execute this command in your terminal to activate the bot:

Bash
curl -F "url=[https://yourdomain.com/path/to/webhook.php](https://yourdomain.com/path/to/webhook.php)" \
     -F "secret_token=YOUR_SECRET_TOKEN" \
     [https://api.telegram.org/bot](https://api.telegram.org/bot)<YOUR_BOT_TOKEN>/setWebhook
🛡️ Security & Debugging
Webhook Security: Every incoming request is verified via the X-Telegram-Bot-Api-Secret-Token header.

Error Monitoring: Check logs/telegram.log for a detailed history of API interactions and server-side errors.

Database Integrity: All interactions with the WooCommerce database follow WordPress's security best practices.

<div align="center"> <p>Built for the <b>Meroviee</b> Ecosystem</p> <p><i>Empowering Digital Creators with Secure Automation.</i></p> </div>
