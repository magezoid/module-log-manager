# Magento 2 Log Manager
Magezoid Log Manager is an advanced admin utility designed to help Magento developers and store owners effortlessly view, manage, and debug log files directly from the Magento Admin Panel — without requiring file system or server access.

# ✅ Compatibility

Magento Open Source: 2.3.x – 2.4.x
Adobe Commerce (on-prem): 2.3.x – 2.4.x
Adobe Commerce (cloud): 2.3.x – 2.4.x



# Key Features
Browse and read log files from var/log/ via the admin panel

View the latest log entries with "Load Previous" support

Search and filter log files by filename

Sort logs by name or last modified time

Download or delete logs with a single click

Pagination support for large volumes of log data

Full admin configuration including:

Enable or disable the module

Set number of log lines to display per file

Configure how many files to list per page

Control default sort behavior

Restrict allowed log file types

Enable or restrict log file deletion and download


# Installation  steps

### 1. Install via composer (Recommended)

Run the following Magento CLI commands:

```
composer require magezoid/module-log-manager
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

### 2. Manual Installation

Copy the content of the repo to the Magento 2 `app/code/Magezoid/LogManager` directory in your Magento installation.

Run the following Magento CLI commands:
```
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

# 🤝 Contribution

We welcome contributions to improve this module!
You can open a pull request or submit issues and suggestions.


# 🛠 Support

If you encounter any problems or bugs, please <a href="https://github.com/magezoid/module-log-manager/issues">open an issue</a> on GitHub.
