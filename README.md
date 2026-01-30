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
```

## 🛠️ Step-by-Step Installation

### 1️⃣ Server Setup
* **Upload Files**: Transfer all project files to a secure directory on your web server.
* **Permissions**: Ensure the `logs/` directory is writable by the web server. Execute the following command in your terminal:
  ```bash
  chmod -R 775 logs
2️⃣ Environment Configuration
Create a .env file in the root directory (you can rename .env.example) and fill in your private credentials:


TELEGRAM_BOT_TOKEN=your_bot_token_here
SOURCE_CHANNEL_ID=-100xxxxxxxxxx
WEBHOOK_SECRET_TOKEN=your_secret_uuid
WP_LOAD_PATH=../wp-load.php
LOG_FILE=logs/telegram.log

3️⃣ Product Mapping
Open webhook.php and configure the $productMap array. This links your WooCommerce Product IDs to the specific Telegram Message IDs from your source channel:


$productMap = [
    123 => ['1001', '1002'], // WooCommerce Product ID => [Telegram Msg IDs]
    456 => ['2005']          // Another Product
];
4️⃣ Setting the Webhook
Tell Telegram where to send your bot's updates by executing this cURL command (Replace <YOUR_BOT_TOKEN> and the URL with your actual data):


curl -F "url=[https://yourdomain.com/path/to/webhook.php](https://yourdomain.com/path/to/webhook.php)" \
     -F "secret_token=YOUR_SECRET_TOKEN" \
     [https://api.telegram.org/bot](https://api.telegram.org/bot)<YOUR_BOT_TOKEN>/setWebhook
     
🛡️ Security & Debugging
🔒 Webhook Security: The system automatically verifies the X-Telegram-Bot-Api-Secret-Token header for every incoming request to ensure it originates from Telegram.

🐞 Error Monitoring: Check logs/telegram.log for a detailed, real-time history of API interactions, purchase validations, and server-side errors.

🗄️ Database Integrity: All interactions with the WooCommerce database are performed using WordPress's native security best practices to prevent SQL injection and unauthorized access.
