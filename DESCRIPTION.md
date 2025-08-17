# 🍬 Iris Sweets API

**PHP библиотека для работы с Iris Sweets API** - удобный инструмент для интеграции с системой выдачи наград в Telegram.

[![PHP Version](https://img.shields.io/badge/PHP-8.3+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Composer](https://img.shields.io/badge/Composer-Ready-orange.svg)](composer.json)

> 🚀 **Простая интеграция** • 🔒 **Безопасность** • 🛡️ **Умная обработка ошибок** • 🔄 **Автоматические повторы**

## ✨ Основные возможности

- 🪙 **Управление золотом** - выдача, получение, история операций
- 🍬 **Управление сладостями** - выдача, получение, история операций  
- 💰 **Баланс кошелька** - просмотр текущих ресурсов
- 📊 **История операций** - детальная аналитика
- 🛡️ **Умная обработка ошибок** - автоматическая классификация и рекомендации
- 🔄 **Автоматические повторы** - настраиваемая стратегия retry
- 📝 **Логирование** - детальная информация для отладки

## 🚀 Быстрый старт

```bash
composer require iris-sweets/api
```

```php
use IrisSweetsApi\IrisSweets;

$api = new IrisSweets();
$balance = $api->balance()->getBalance();
```

## 📚 Документация

Подробная документация доступна в [README.md](README.md) и [docs/](docs/) папке.

---

**Создано с ❤️ для Telegram сообщества**
