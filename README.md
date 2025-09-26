# 🍬 Iris Sweets API

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Composer](https://img.shields.io/badge/Composer-Ready-orange.svg)](composer.json)

**PHP библиотека для работы с Iris Sweets API**

## 🚀 Возможности

- **Управление голдой** - выдача, получение истории операций
- **Управление ирисками** - выдача, получение истории операций  
- **Управление мешком** - возможность запрета переводить вам и смотреть ваш мешок
- **Получение баланса** - информация о балансе
- **Работа с биржей** - стакан заявок, история сделок, анализ торговых данных
- **Торговля ирис-голд** - покупка, продажа, управление заявками на бирже
- **Информация о пользователях** - регистрация, активность, спам-статус, звёздность, мешок

## 📦 Установка

```bash
# Клонируйте репозиторий
git clone https://github.com/lukinism/IrisSweetsApi.git
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
    
    // Обработка различных типов операций
    foreach ($history as $transaction) {
        $type = match($transaction['type']) {
            'send' => 'Отправлено',
            'receive' => 'Получено',
            'dividends' => 'Дивиденды',
            'trade' => 'Торговля',
            default => $transaction['type']
        };
        
        echo "Транзакция #{$transaction['id']}: {$type} {$transaction['amount']} ирисок\n";
    }
    
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

### 💹 Работа с биржей

```php
try {
    // Получение стакана заявок
    $orderBook = $api->exchange()->getOrderBook();
    echo "Заявок на покупку: " . count($orderBook['buy']) . "\n";
    echo "Заявок на продажу: " . count($orderBook['sell']) . "\n";
    
    // Лучшие цены
    $bestBid = $api->exchange()->getBestBidPrice();
    $bestAsk = $api->exchange()->getBestAskPrice();
    $spread = $api->exchange()->getSpread();
    
    echo "Лучшая цена покупки: $bestBid\n";
    echo "Лучшая цена продажи: $bestAsk\n";
    echo "Спред: $spread\n";
    
    // История сделок
    $deals = $api->exchange()->getDeals();
    echo "Последних сделок: " . count($deals) . "\n";
    
    // Статистика по сделкам
    $stats = $api->exchange()->getDealsStats();
    echo "Общий объем: " . $stats['total_volume'] . "\n";
    echo "Средняя цена: " . round($stats['avg_price'], 4) . "\n";
    
} catch (ApiException $e) {
    echo "❌ Ошибка API: " . $e->getMessage() . "\n";
}
```

### 🏪 Торговля ирис-голд

```php
try {
    // Покупка ирис-голд
    $buyResult = $api->trade()->buy(1.5, 10); // купить 10 ирис-голд по цене 1.5
    echo "Куплено сразу: " . $buyResult['result']['done_volume'] . "\n";
    echo "Потрачено ирисок: " . $buyResult['result']['sweets_spent'] . "\n";
    
    if (isset($buyResult['result']['new_order'])) {
        echo "Создана заявка #" . $buyResult['result']['new_order']['id'] . "\n";
    }
    
    // Продажа ирис-голд
    $sellResult = $api->trade()->sell(1.4, 5); // продать 5 ирис-голд по цене 1.4
    echo "Продано сразу: " . $sellResult['result']['done_volume'] . "\n";
    echo "Получено ирисок: " . $sellResult['result']['sweets_received'] . "\n";
    
    // Получение активных заявок
    $orders = $api->trade()->getMyOrders();
    echo "Заявок на покупку: " . count($orders['result']['buy']) . "\n";
    echo "Заявок на продажу: " . count($orders['result']['sell']) . "\n";
    
    // Управление заявками
    if ($api->trade()->hasActiveOrders()) {
        // Отменить все заявки
        $cancelResult = $api->trade()->cancelAll();
        echo "Возвращено голды: " . $cancelResult['result']['gold'] . "\n";
        echo "Возвращено ирисок: " . $cancelResult['result']['sweets'] . "\n";
        
        // Или отменить заявки по конкретной цене
        $api->trade()->cancelByPrice(1.5);
        
        // Или отменить часть заявки
        $api->trade()->cancelPart(123, 5); // отменить 5 из заявки #123
    }
    
} catch (ApiException $e) {
    echo "❌ Ошибка API: " . $e->getMessage() . "\n";
}
```

### 👤 Информация о пользователях

```php
try {
    $userId = 123456;
    
    // Получение информации о регистрации
    $registration = $api->userInfo()->getRegistration($userId);
    echo "Пользователь зарегистрирован: " . date('Y-m-d H:i:s', $registration['result']) . "\n";
    
    // Статистика активности
    $activity = $api->userInfo()->getActivity($userId);
    echo "Активность за день: " . $activity['result']['day'] . "\n";
    echo "Активность за неделю: " . $activity['result']['week'] . "\n";
    echo "Активность за месяц: " . $activity['result']['month'] . "\n";
    echo "Общая активность: " . $activity['result']['total'] . "\n";
    
    // Проверка спам-статуса
    $spamInfo = $api->userInfo()->getSpamInfo($userId);
    echo "В спам-базе: " . ($spamInfo['result']['spam'] ? 'Да' : 'Нет') . "\n";
    echo "В скам-базе: " . ($spamInfo['result']['scam'] ? 'Да' : 'Нет') . "\n";
    echo "В игнор-базе: " . ($spamInfo['result']['ignore'] ? 'Да' : 'Нет') . "\n";
    
    // Звёздность пользователя
    $stars = $api->userInfo()->getStars($userId);
    echo "Звёздность: " . $stars['result'] . "\n";
    
    // Мешок пользователя
    $pocket = $api->userInfo()->getPocket($userId);
    echo "Ириски: " . $pocket['result']['sweets'] . "\n";
    echo "Голда: " . $pocket['result']['gold'] . "\n";
    echo "Звёзды: " . $pocket['result']['stars'] . "\n";
    echo "Монеты: " . $pocket['result']['coins'] . "\n";
    
    // Получение нескольких типов информации одновременно
    $multipleInfo = $api->userInfo()->getMultipleInfo($userId, ['reg', 'activity', 'spam']);
    echo "Комбинированная информация получена\n";
    
    // Получение всей доступной информации
    $allInfo = $api->userInfo()->getAllInfo($userId);
    echo "Вся информация о пользователе получена\n";
    
    // Быстрые проверки
    if ($api->userInfo()->isSpam($userId)) {
        echo "⚠️ Пользователь в спам-базе!\n";
    }
    
    if ($api->userInfo()->isBlacklisted($userId)) {
        echo "⚠️ Пользователь в чёрном списке!\n";
    }
    
    // Быстрое получение отдельных значений
    $dailyActivity = $api->userInfo()->getDailyActivity($userId);
    $pocketSweets = $api->userInfo()->getPocketSweets($userId);
    echo "Активность за день: $dailyActivity\n";
    echo "Ириски в мешке: $pocketSweets\n";
    
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
  - **Типы операций:** `send`, `receive`, `dividends`, `trade`

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
- `pocket()->giveDonateScore($amount, $userId, $comment)` - передать очки доната
  - `$amount` (int) - количество очков доната
  - `$userId` (int) - ID пользователя
  - `$comment` (string, опционально) - подпись к переводу
- `pocket()->getDonateScoreHistory($offset, $limit)` - история очков доната
  - `$offset` (int, опционально) - ID записи для смещения (по умолчанию 0)
  - `$limit` (int, опционально) - количество записей (по умолчанию 200)

### Биржа
- `exchange()->getOrderBook()` - получить стакан заявок
- `exchange()->getBestBidPrice()` - лучшая цена покупки
- `exchange()->getBestAskPrice()` - лучшая цена продажи
- `exchange()->getSpread()` - спред между лучшими ценами
- `exchange()->getDeals($fromId)` - получить историю сделок
- `exchange()->getDealsStats($fromId)` - статистика по сделкам

### Торговля ирис-голд
- `trade()->buy($price, $volume)` - заявка на покупку ирис-голд
  - `$price` (float) - цена покупки (от 0.01 до 1000000)
  - `$volume` (float) - количество голды для покупки
- `trade()->sell($price, $volume)` - заявка на продажу ирис-голд
  - `$price` (float) - цена продажи (от 0.01 до 1000000)
  - `$volume` (float) - количество голды для продажи
- `trade()->getMyOrders()` - список активных заявок бота
- `trade()->cancelByPrice($price)` - отменить все заявки по указанной цене
- `trade()->cancelAll()` - отменить все заявки бота
- `trade()->cancelPart($id, $volume)` - отменить часть заявки
  - `$id` (int) - ID заявки на бирже
  - `$volume` (float) - объем для отмены

#### Вспомогательные методы торговли
- `trade()->getBuyOrders()` - только заявки на покупку
- `trade()->getSellOrders()` - только заявки на продажу
- `trade()->getTotalBuyVolume()` - общий объем заявок на покупку
- `trade()->getTotalSellVolume()` - общий объем заявок на продажу
- `trade()->getOrdersCount()` - количество активных заявок
- `trade()->hasActiveOrders()` - проверка наличия активных заявок

### Информация о пользователях
- `userInfo()->getRegistration($userId)` - информация о регистрации
- `userInfo()->getActivity($userId)` - статистика активности
- `userInfo()->getSpamInfo($userId)` - информация о спам-статусе
- `userInfo()->getStars($userId)` - звёздность пользователя
- `userInfo()->getPocket($userId)` - мешок пользователя
- `userInfo()->getMultipleInfo($userId, $permissions)` - несколько типов информации (отдельные запросы)
- `userInfo()->getAllInfo($userId)` - вся доступная информация

#### Быстрые методы
- `userInfo()->isSpam($userId)` - проверка спам-статуса
- `userInfo()->isScam($userId)` - проверка скам-статуса
- `userInfo()->isIgnored($userId)` - проверка игнор-статуса
- `userInfo()->isBlacklisted($userId)` - проверка чёрного списка
- `userInfo()->getDailyActivity($userId)` - активность за день
- `userInfo()->getWeeklyActivity($userId)` - активность за неделю
- `userInfo()->getMonthlyActivity($userId)` - активность за месяц
- `userInfo()->getTotalActivity($userId)` - общая активность
- `userInfo()->getPocketSweets($userId)` - ириски в мешке
- `userInfo()->getPocketGold($userId)` - голда в мешке
- `userInfo()->getPocketStars($userId)` - звёзды в мешке
- `userInfo()->getPocketCoins($userId)` - монеты в мешке

#### Примечания о работе с API

**Структура ответов:**
Библиотека автоматически нормализует структуру ответов API для удобства использования. Например, для спам-информации API возвращает поля `is_spam`, `is_scam`, `is_ignore`, но библиотека преобразует их в `spam`, `scam`, `ignore` для единообразия.

**Комбинированные запросы:**
Метод `getMultipleInfo()` делает отдельные запросы для каждого разрешения и объединяет результаты.

### Обновления событий
- `updates()->getUpdates($offset)` - получение событий в реальном времени
  - `$offset` (int, опционально) - ID события для смещения (по умолчанию 0)

#### Типы событий:
- `sweets_log` - события с ирисками
- `gold_log` - события с голдой
- `donate_score_log` - события с очками доната

#### Структура события:
```php
[
    'id' => int,                    // ID события
    'type' => string,               // Тип события
    'date' => int,                  // UNIX-time
    'object' => array               // Объект события
]
```

#### Структура объекта события для очков доната:
```php
[
    'id' => int,                    // ID транзакции
    'type' => string,               // 'send', 'send_with', 'receive', 'receive_with'
    'date' => int,                  // UNIX-time
    'amount' => int,                // Количество очков доната
    'balance' => int,               // Новый баланс
    'peer_id' => int,               // ID контрагента
    'comment' => string,            // Комментарий к переводу
    'metadata' => array             // Дополнительные данные
]
```

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

```json
{
    "error": {
        "code": 403,
        "description": "Rights are not given"
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
    
    // Специальная обработка ошибки прав
    if ($e->isRightsError()) {
        echo "⚠️ Ошибка прав доступа!\n";
        echo "Проверьте настройки бота и права доступа к API\n";
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

### Проверка пользователей

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use IrisSweetsApi\IrisSweets;
use IrisSweetsApi\Exception\ApiException;

$api = new IrisSweets();

function checkUser($userId) {
    global $api;
    
    try {
        // Получаем всю информацию о пользователе
        $userInfo = $api->userInfo()->getAllInfo($userId);
        
        echo "=== Информация о пользователе $userId ===\n";
        
        // Регистрация
        $registration = $userInfo['result']['reg'];
        echo "Зарегистрирован: " . date('Y-m-d H:i:s', $registration) . "\n";
        
        // Активность
        $activity = $userInfo['result']['activity'];
        echo "Активность:\n";
        echo "  - За день: " . $activity['day'] . "\n";
        echo "  - За неделю: " . $activity['week'] . "\n";
        echo "  - За месяц: " . $activity['month'] . "\n";
        echo "  - Общая: " . $activity['total'] . "\n";
        
        // Спам-статус
        $spam = $userInfo['result']['spam'];
        echo "Статус:\n";
        echo "  - Спам: " . ($spam['spam'] ? 'Да' : 'Нет') . "\n";
        echo "  - Скам: " . ($spam['scam'] ? 'Да' : 'Нет') . "\n";
        echo "  - Игнор: " . ($spam['ignore'] ? 'Да' : 'Нет') . "\n";
        
        // Звёздность
        $stars = $userInfo['result']['stars'];
        echo "Звёздность: $stars\n";
        
        // Мешок
        $pocket = $userInfo['result']['pocket'];
        echo "Мешок:\n";
        echo "  - Ириски: " . $pocket['sweets'] . "\n";
        echo "  - Голда: " . $pocket['gold'] . "\n";
        echo "  - Звёзды: " . $pocket['stars'] . "\n";
        echo "  - Монеты: " . $pocket['coins'] . "\n";
        
        // Проверки безопасности
        if ($api->userInfo()->isBlacklisted($userId)) {
            echo "⚠️  ВНИМАНИЕ: Пользователь в чёрном списке!\n";
            return false;
        }
        
        // Проверка активности
        $dailyActivity = $api->userInfo()->getDailyActivity($userId);
        if ($dailyActivity < 5) {
            echo "⚠️  Низкая активность пользователя\n";
        }
        
        echo "✅ Пользователь проверен успешно\n";
        return true;
        
    } catch (ApiException $e) {
        echo "❌ Ошибка проверки пользователя: " . $e->getMessage() . "\n";
        return false;
    }
}

function checkMultipleUsers($userIds) {
    global $api;
    
    echo "=== Проверка нескольких пользователей ===\n";
    
    foreach ($userIds as $userId) {
        echo "\n--- Пользователь $userId ---\n";
        
        try {
            // Быстрая проверка только спам-статуса
            if ($api->userInfo()->isSpam($userId)) {
                echo "❌ Пользователь в спам-базе\n";
                continue;
            }
            
            // Получаем только активность и звёздность
            $info = $api->userInfo()->getMultipleInfo($userId, ['activity', 'stars']);
            
            $activity = $info['result']['activity'];
            $stars = $info['result']['stars'];
            
            echo "Активность: " . $activity['total'] . "\n";
            echo "Звёздность: $stars\n";
            
            if ($activity['total'] > 1000 && $stars > 3) {
                echo "✅ Активный пользователь с хорошей репутацией\n";
            } else {
                echo "⚠️  Обычный пользователь\n";
            }
            
        } catch (ApiException $e) {
            echo "❌ Ошибка: " . $e->getMessage() . "\n";
        }
    }
}

// Использование
checkUser(123456);
checkMultipleUsers([123456, 789012, 345678]);
```

### Торговый бот

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use IrisSweetsApi\IrisSweets;
use IrisSweetsApi\Exception\ApiException;

$api = new IrisSweets();

class TradingBot {
    private $api;
    private $minSpread = 0.05; // минимальный спред для торговли
    private $maxVolume = 100;  // максимальный объем заявки
    
    public function __construct($api) {
        $this->api = $api;
    }
    
    public function analyzeMarket() {
        try {
            // Получаем текущие цены
            $bestBid = $this->api->exchange()->getBestBidPrice();
            $bestAsk = $this->api->exchange()->getBestAskPrice();
            $spread = $this->api->exchange()->getSpread();
            
            echo "=== Анализ рынка ===\n";
            echo "Лучшая цена покупки: $bestBid\n";
            echo "Лучшая цена продажи: $bestAsk\n";
            echo "Спред: $spread\n";
            
            if ($spread >= $this->minSpread) {
                echo "✅ Хорошие условия для торговли\n";
                return ['bid' => $bestBid, 'ask' => $bestAsk, 'spread' => $spread];
            } else {
                echo "⚠️ Спред слишком мал для торговли\n";
                return null;
            }
            
        } catch (ApiException $e) {
            echo "❌ Ошибка анализа рынка: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    public function placeArbitrageOrders($marketData) {
        if (!$marketData) return;
        
        try {
            $bid = $marketData['bid'];
            $ask = $marketData['ask'];
            $spread = $marketData['spread'];
            
            // Проверяем активные заявки
            $orders = $this->api->trade()->getMyOrders();
            $activeBuyOrders = count($orders['result']['buy']);
            $activeSellOrders = count($orders['result']['sell']);
            
            echo "=== Размещение арбитражных заявок ===\n";
            echo "Активных заявок на покупку: $activeBuyOrders\n";
            echo "Активных заявок на продажу: $activeSellOrders\n";
            
            // Если нет активных заявок, размещаем новые
            if ($activeBuyOrders === 0 && $activeSellOrders === 0) {
                // Покупка по цене чуть выше лучшей
                $buyPrice = $bid + 0.01;
                $buyResult = $this->api->trade()->buy($buyPrice, 10);
                echo "✅ Размещена заявка на покупку по цене $buyPrice\n";
                
                // Продажа по цене чуть ниже лучшей
                $sellPrice = $ask - 0.01;
                $sellResult = $this->api->trade()->sell($sellPrice, 10);
                echo "✅ Размещена заявка на продажу по цене $sellPrice\n";
            }
            
        } catch (ApiException $e) {
            echo "❌ Ошибка размещения заявок: " . $e->getMessage() . "\n";
        }
    }
    
    public function manageOrders() {
        try {
            $orders = $this->api->trade()->getMyOrders();
            $buyOrders = $orders['result']['buy'];
            $sellOrders = $orders['result']['sell'];
            
            echo "=== Управление заявками ===\n";
            
            // Проверяем заявки на покупку
            foreach ($buyOrders as $order) {
                $orderPrice = $order['price'];
                $orderVolume = $order['volume'];
                $orderId = $order['id'];
                
                // Если цена заявки слишком далеко от рынка, отменяем
                $currentBid = $this->api->exchange()->getBestBidPrice();
                if ($orderPrice < $currentBid - 0.1) {
                    $this->api->trade()->cancelPart($orderId, $orderVolume);
                    echo "❌ Отменена заявка на покупку #$orderId (цена $orderPrice)\n";
                }
            }
            
            // Проверяем заявки на продажу
            foreach ($sellOrders as $order) {
                $orderPrice = $order['price'];
                $orderVolume = $order['volume'];
                $orderId = $order['id'];
                
                // Если цена заявки слишком далеко от рынка, отменяем
                $currentAsk = $this->api->exchange()->getBestAskPrice();
                if ($orderPrice > $currentAsk + 0.1) {
                    $this->api->trade()->cancelPart($orderId, $orderVolume);
                    echo "❌ Отменена заявка на продажу #$orderId (цена $orderPrice)\n";
                }
            }
            
        } catch (ApiException $e) {
            echo "❌ Ошибка управления заявками: " . $e->getMessage() . "\n";
        }
    }
    
    public function getTradingStats() {
        try {
            $orders = $this->api->trade()->getMyOrders();
            $count = $this->api->trade()->getOrdersCount();
            
            echo "=== Статистика торговли ===\n";
            echo "Всего заявок: " . $count['total_count'] . "\n";
            echo "Заявок на покупку: " . $count['buy_count'] . "\n";
            echo "Заявок на продажу: " . $count['sell_count'] . "\n";
            
            if ($count['total_count'] > 0) {
                $totalBuyVolume = $this->api->trade()->getTotalBuyVolume();
                $totalSellVolume = $this->api->trade()->getTotalSellVolume();
                
                echo "Общий объем покупок: $totalBuyVolume\n";
                echo "Общий объем продаж: $totalSellVolume\n";
            }
            
        } catch (ApiException $e) {
            echo "❌ Ошибка получения статистики: " . $e->getMessage() . "\n";
        }
    }
    
    public function run() {
        echo "🤖 Запуск торгового бота...\n\n";
        
        while (true) {
            // Анализируем рынок
            $marketData = $this->analyzeMarket();
            
            // Управляем существующими заявками
            $this->manageOrders();
            
            // Размещаем новые заявки при необходимости
            $this->placeArbitrageOrders($marketData);
            
            // Показываем статистику
            $this->getTradingStats();
            
            echo "\n⏰ Ожидание 30 секунд...\n";
            sleep(30);
        }
    }
}

// Использование
$bot = new TradingBot($api);
$bot->run();
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

