# 🍬 Iris Sweets API

**PHP библиотека для работы с Iris Sweets API**

## 🚀 Возможности

- **Управление голдой** - выдача, получение истории операций
- **Управление ирисками** - выдача, получение истории операций  
- **Управление мешком** - возможность запрета переводить вам и смотреть ваш мешок.
- **Получение баланса** - информация о балансе

## 📦 Установка

```bash
# Клонируйте репозиторий
git clone [https://github.com/lukinism/IrisSweetsApi](https://github.com/lukinism/IrisSweetsApi.git)
cd IrisSweetsLibrary

# Установите зависимости
composer install
```

## ⚙️ Настройка

### Способ 1: Через файл .env (рекомендуется)

1. **Скопируйте шаблон конфигурации:**
```bash
cp env.example .env
```

2. **Отредактируйте файл `.env`:**
```env
# ID вашего бота
IRIS_BOT_ID=your_bot_id_here

# Токен
IRIS_TOKEN=your_iris_token_here
```

### Способ 2: Через параметры конструктора

```php
$api = new IrisSweets($botId, $irisToken, $baseUrl);
```

## 🔧 Использование

### Инициализация

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use IrisSweetsApi\IrisSweets;
use IrisSweetsApi\Exception\ApiException;

// Автоматически загружает настройки из .env
$api = new IrisSweets();

// Или передайте параметры напрямую
$api = new IrisSweets('your-bot-id', 'your-iris-token');
```

### 💰 Получение баланса

```php
try {
    $balance = $api->balance()->getBalance();
    echo "Голд: " . $balance['gold'] . "\n";
    echo "Ириски: " . $balance['sweets'] . "\n";
    echo "Донат очки: " . $balance['donate_score'] . "\n";
} catch (ApiException $e) {
    echo "Ошибка API: " . $e->getMessage() . "\n";
}
```

### 🍬 Операции с ирисками

```php
try {
    // Выдать 10 ирисок пользователю
    $result = $api->sweets()->give(10, 123456);
    echo "✅ Ириски успешно выданы\n";
    
    // Выдать 5.5 ирисок с комментарием
    $result = $api->sweets()->give(5.5, 123456, 'Награда за активность');
    echo "✅ Ириски успешно выданы\n";
    
    // Получить историю операций
    $history = $api->sweets()->getHistory(0);
    echo "История операций: " . count($history) . " записей\n";
    
} catch (ApiException $e) {
    echo "❌ Ошибка API: " . $e->getMessage() . "\n";
}
```

### 🪙 Операции с голдой

```php
try {
    // Выдать 100 голды пользователю
    $result = $api->gold()->give(100, 123456);
    echo "✅ Голда успешно выдано\n";
    
    // Выдать 50.5 голды с комментарием
    $result = $api->gold()->give(50.5, 123456, 'Награда за активность');
    echo "✅ Голда успешно выдано\n";
    
    // Получить общую историю операций
    $history = $api->gold()->getHistory(0);
    echo "Общая история: " . count($history) . " записей\n";
    
} catch (ApiException $e) {
    echo "❌ Ошибка API: " . $e->getMessage() . "\n";
}
```

### 👜 Управление мешком

```php
try {
    // Управление доступом к мешку
    $api->pocket()->enable();      // Открыть доступ
    $api->pocket()->disable();     // Закрыть доступ
    
    // Управление общими разрешениями на переводы
    $api->pocket()->allow_all();   // Разрешить всем переводить
    $api->pocket()->deny_all();    // Запретить всем переводить
    
    // Управление конкретными пользователями
    $api->pocket()->allow_user(123456);  // Разрешить пользователю
    $api->pocket()->deny_user(123456);   // Запретить пользователю
    
    echo "✅ Настройки мешка обновлены\n";
    
} catch (ApiException $e) {
    echo "❌ Ошибка API: " . $e->getMessage() . "\n";
}
```

## 📚 API Справочник

### Баланс
- `balance()->getBalance()` - получение баланса

### Ириски
- `sweets()->give($sweets, $userId, $comment)` - выдача ирисок
  - `$sweets` (int|float) - количество ирисок
  - `$userId` (int) - ID пользователя
  - `$comment` (string, опционально) - комментарий к выдаче
- `sweets()->getHistory($offset)` - получение истории операций
  - `$offset` (int) - ID записи для смещения (по умолчанию 0)

### Голда
- `gold()->give($gold, $userId, $comment)` - выдача голды
  - `$gold` (int|float) - количество голды
  - `$userId` (int) - ID пользователя
  - `$comment` (string, опционально) - комментарий к выдаче
- `gold()->getHistory($offset)` - получение общей истории операций

### Управление мешком
- `pocket()->enable()` - открыть доступ к мешку
- `pocket()->disable()` - закрыть доступ к мешку
- `pocket()->allow_all()` - разрешить всем переводить в мешок
- `pocket()->deny_all()` - запретить всем переводить в мешок
- `pocket()->allow_user($userId)` - разрешить конкретному пользователю
- `pocket()->deny_user($userId)` - запретить конкретному пользователю

## ⚠️ Обработка ошибок

Библиотека использует специальный класс `ApiException` для обработки ошибок API.

### Типы ошибок

- **ApiException** - ошибки от API (недостаток валюты, проблема с пользователем и т.д.)
- **Exception** - общие ошибки (сетевые проблемы, валидация параметров)

### Примеры ошибок

```json
{
    "error": {
        "code": 0,
        "description": "Not enough gold. Need 1"
    }
}
```

```json
{
    "error": {
        "code": 1,
        "description": "User not found"
    }
}
```

### Обработка ошибок

Библиотека предоставляет мощную систему обработки ошибок с автоматическими повторными попытками и детальным логированием.

#### Базовый пример

```php
try {
    $api->gold()->give(100, 123456);
} catch (ApiException $e) {
    echo "Ошибка API: " . $e->getMessage() . "\n";
    echo "Код ошибки: " . $e->getErrorCode() . "\n";
    echo "Описание: " . $e->getErrorDescription() . "\n";
} catch (Exception $e) {
    echo "Общая ошибка: " . $e->getMessage() . "\n";
}
```

#### Расширенная обработка ошибок

```php
use IrisSweetsApi\Exception\ErrorHandler;

try {
    $api->sweets()->give(10, 123456);
} catch (ApiException $e) {
    // Автоматическое логирование
    $message = ErrorHandler::handle($e);
    
    // Проверка типа ошибки
    if ($e->isRetryable()) {
        echo "Временная ошибка, попробуйте позже\n";
        echo "Рекомендуемая задержка: " . $e->getRetryDelay() . " сек\n";
    }
    
    // Пользовательское сообщение
    echo "Ошибка: " . $e->getUserFriendlyMessage() . "\n";
    
    // Рекомендации по исправлению
    $recommendations = ErrorHandler::getRecommendations($e);
    foreach ($recommendations as $rec) {
        echo "- " . $rec . "\n";
    }
}
```

#### Автоматические повторные попытки

```php
// Настройка повторных попыток для конкретного API
$api->sweets()->setRetrySettings(3, 2, 1.5); // 3 попытки, базовая задержка 2с

// Автоматически повторяются только временные ошибки (HTTP 500, 409)
```

#### Режим отладки

```php
// Включить детальное логирование
define('IRIS_DEBUG', true);
$api->sweets()->enableDebug();

// Получить детальную информацию об ошибке
$detailedInfo = $e->getDetailedInfo();
print_r($detailedInfo);
```

**📚 Подробнее:** См. [документацию по обработке ошибок](docs/ERROR_HANDLING.md)

## 🔒 Безопасность

### Хранение конфигурации

- **Токены и ID** хранятся в переменных окружения
- **Используйте `env.example`** как шаблон для настройки


## 📝 Примеры использования

### Простой бот для выдачи наград

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use IrisSweetsApi\IrisSweets;
use IrisSweetsApi\Exception\ApiException;

$api = new IrisSweets();

function giveReward($userId, $activity) {
    global $api;
    
    try {
        switch ($activity) {
            case 'daily_login':
                $api->sweets()->give(5, $userId, 'Ежедневный вход');
                $api->gold()->give(10, $userId, 'Ежедневный вход');
                break;
                
            case 'referral':
                $api->sweets()->give(20, $userId, 'Приглашение друга');
                $api->gold()->give(50, $userId, 'Приглашение друга');
                break;
                
            case 'achievement':
                $api->sweets()->give(15, $userId, 'Достижение');
                $api->gold()->give(25, $userId, 'Достижение');
                break;
        }
        
        return true;
    } catch (ApiException $e) {
        error_log("Ошибка выдачи награды: " . $e->getMessage());
        return false;
    }
}

// Использование
giveReward(123456, 'daily_login');
```

### Мониторинг баланса

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use IrisSweetsApi\IrisSweets;

$api = new IrisSweets();

function checkBalance() {
    global $api;
    
    try {
        $balance = $api->balance()->getBalance();
        
        echo "=== Баланс мешка ===\n";
        echo "Голда: " . $balance['gold'] . "\n";
        echo "Ириски: " . $balance['sweets'] . "\n";
        echo "Донат очки: " . $balance['donate_score'] . "\n";
        
        // Проверяем, достаточно ли ресурсов
        if ($balance['gold'] < 100) {
            echo "⚠️  Внимание: мало голды!\n";
        }
        
        if ($balance['sweets'] < 50) {
            echo "⚠️  Внимание: мало ирисок!\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Ошибка получения баланса: " . $e->getMessage() . "\n";
    }
}

checkBalance();
```

## 📄 Лицензия

Этот проект распространяется под лицензией MIT. См. файл `LICENSE` для получения дополнительной информации.

## 🔄 Обновления

Следите за обновлениями библиотеки:

```bash
git pull origin main
composer update
```

---

**Сделано с ❤️ для сообщества Iris Чат-менеджер**
