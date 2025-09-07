# Система обработки ошибок Iris Sweets API

## Обзор

Библиотека Iris Sweets API предоставляет мощную и гибкую систему обработки ошибок, которая позволяет:

- Автоматически определять типы ошибок на основе ответов API
- Получать детальную информацию об ошибках
- Автоматически повторять операции при временных ошибках
- Логировать ошибки для отладки
- Предоставлять пользовательские сообщения об ошибках

## Структура ошибок API

### Формат ответа с ошибкой

```json
{
    "error": {
        "description": "Error description",
        "code": 400
    }
}
```

### Типы ошибок и HTTP статус коды

| Описание ошибки | HTTP статус | Тип ошибки |
|----------------|-------------|------------|
| "Sweets/Gold is 0" | 400 | SWEETS_GOLD_ZERO |
| "User not found" | 404 | USER_NOT_FOUND |
| "Account is not user" | 403 | ACCOUNT_NOT_USER |
| "Ошибка в расчёте сжигании ирисок..." | 500 | CALCULATION_ERROR |
| "Not enough sweets/gold. Need $value" | 409 | NOT_ENOUGH_RESOURCES |
| "Unsuccessful donate score decrease" | 409 | UNSUCCESSFUL_DECREASE |
| "Unsuccessful sweets/gold decrease" | 409 | UNSUCCESSFUL_DECREASE |

## Основные классы

### 1. ApiException

Основной класс исключения для ошибок API.

```php
use IrisSweetsApi\Exception\ApiException;

try {
    $api->sweets()->give(10, 123456);
} catch (ApiException $e) {
    echo "Ошибка: " . $e->getMessage();
    echo "Тип: " . $e->getErrorType();
    echo "HTTP статус: " . $e->getHttpStatusCode()->value;
    echo "Код ошибки: " . $e->getErrorCode();
}
```

#### Методы ApiException

- `getErrorType()` - получить тип ошибки
- `getHttpStatusCode()` - получить HTTP статус код
- `getErrorCode()` - получить код ошибки от API
- `getErrorDescription()` - получить описание ошибки от API
- `getUserFriendlyMessage()` - получить человекочитаемое сообщение
- `getDetailedInfo()` - получить детальную информацию для логирования
- `isRetryable()` - проверить, можно ли повторить операцию
- `getRetryDelay()` - получить рекомендуемую задержку перед повтором

#### Проверка типов ошибок

```php
if ($e->isValidationError()) {
    echo "Ошибка валидации";
}

if ($e->isAuthorizationError()) {
    echo "Ошибка авторизации";
}

if ($e->isServerError()) {
    echo "Серверная ошибка";
}

if ($e->isInsufficientResourceError()) {
    echo "Недостаточно ресурсов";
}

if ($e->isInvalidUserError()) {
    echo "Ошибка пользователя";
}
```

### 2. ErrorHandler

Утилитарный класс для обработки и форматирования ошибок.

```php
use IrisSweetsApi\Exception\ErrorHandler;

try {
    $api->sweets()->give(10, 123456);
} catch (ApiException $e) {
    // Автоматическое логирование
    ErrorHandler::handle($e);
    
    // Форматирование для пользователя
    $formattedError = ErrorHandler::formatForUser($e);
    
    echo "Сообщение: " . $formattedError['message'];
    echo "Рекомендации:";
    foreach ($formattedError['recommendations'] as $rec) {
        echo "- " . $rec;
    }
}
```

#### Методы ErrorHandler

- `handle(ApiException $e)` - обработать ошибку и вернуть сообщение
- `logError(ApiException $e)` - залогировать ошибку
- `shouldRetry(ApiException $e)` - проверить, нужно ли повторить
- `getRetryDelay(ApiException $e)` - получить задержку для повтора
- `createUserMessage(ApiException $e)` - создать пользовательское сообщение
- `getRecommendations(ApiException $e)` - получить рекомендации по исправлению
- `formatForUser(ApiException $e)` - отформатировать ошибку для пользователя

### 3. RetryHandler

Класс для автоматических повторных попыток при временных ошибках.

```php
use IrisSweetsApi\Exception\RetryHandler;

$retryHandler = new RetryHandler(3, 2, 1.5); // 3 попытки, базовая задержка 2с, множитель 1.5

try {
    $result = $retryHandler->execute(
        fn() => $api->sweets()->give(10, 123456),
        ['operation' => 'give_sweets']
    );
} catch (ApiException $e) {
    echo "Операция не удалась после всех попыток";
}
```

#### Настройки RetryHandler

- `maxRetries` - максимальное количество попыток
- `baseDelay` - базовая задержка в секундах
- `backoffMultiplier` - множитель для экспоненциальной задержки

### 4. HttpStatusCode

Enum для HTTP статус кодов.

```php
use IrisSweetsApi\Exception\HttpStatusCode;

$status = HttpStatusCode::fromApiError("User not found");
echo $status->value; // 404
echo $status->getDescription(); // "Не найдено"
```

### 5. ErrorType

Константы для типов ошибок.

```php
use IrisSweetsApi\Exception\ErrorType;

$errorType = ErrorType::fromApiDescription("Not enough sweets. Need 5");
echo $errorType; // NOT_ENOUGH_RESOURCES
echo ErrorType::getDescription($errorType); // "Недостаточно ресурсов"
```

## Автоматические повторные попытки

### Настройка в AbstractApi

```php
// Установить настройки повторных попыток для конкретного API
$api->sweets()->setRetrySettings(5, 1, 2.0);

// Получить статистику
$stats = $api->sweets()->getRetryStats();
echo "Максимум попыток: " . $stats['max_retries'];
```

### Логика повторных попыток

Автоматически повторяются только следующие типы ошибок:
- **HTTP 500** (Internal Server Error) - задержка 5 секунд
- **HTTP 409** (Conflict) - задержка 1 секунда

### Настройка задержек

```php
$retryHandler = new RetryHandler();
$retryHandler->setMaxRetries(5);
$retryHandler->setBaseDelay(3);
$retryHandler->setBackoffMultiplier(2.0);
```

## Логирование ошибок

### Автоматическое логирование

Все ошибки автоматически логируются через `ErrorHandler::logError()`.

### Формат лога

```
[2024-01-15 10:30:45] API Error: User not found (Type: USER_NOT_FOUND, HTTP: 404, Code: 1) in /path/to/file.php:123
```

### Детальное логирование (в режиме отладки)

```php
define('IRIS_DEBUG', true);

// Включается детальное логирование с полной информацией об ошибке
```

## Режим отладки

### Включение отладки

```php
// Для конкретного API
$api->sweets()->enableDebug();

// Для HTTP клиента
$api->sweets()->getHttpClient()->setDebug(true);
```

### Что логируется в режиме отладки

- URL запросов
- HTTP статус коды
- Тела ответов
- Детальная информация об ошибках
- Стек вызовов

## Примеры использования

### Базовый пример

```php
try {
    $result = $api->sweets()->give(10, 123456);
    echo "Успешно!";
} catch (ApiException $e) {
    $message = ErrorHandler::handle($e);
    echo "Ошибка: " . $message;
}
```

### Расширенный пример

```php
try {
    $result = $api->gold()->give(100, 123456);
} catch (ApiException $e) {
    if ($e->isRetryable()) {
        echo "Временная ошибка, попробуйте позже";
        echo "Рекомендуемая задержка: " . $e->getRetryDelay() . " сек";
    } else {
        $formatted = ErrorHandler::formatForUser($e);
        echo "Ошибка: " . $formatted['message'];
        
        if (!empty($formatted['recommendations'])) {
            echo "Рекомендации:";
            foreach ($formatted['recommendations'] as $rec) {
                echo "- " . $rec;
            }
        }
    }
}
```

### Обработка с повторными попытками

```php
$retryHandler = new RetryHandler(3, 2, 1.5);

try {
    $result = $retryHandler->execute(
        fn() => $api->sweets()->give(10, 123456),
        ['user_id' => 123456, 'amount' => 10]
    );
    echo "Операция выполнена успешно";
} catch (ApiException $e) {
    echo "Операция не удалась после всех попыток: " . $e->getMessage();
}
```

## Лучшие практики

### 1. Всегда используйте try-catch

```php
// ❌ Плохо
$result = $api->sweets()->give(10, 123456);

// ✅ Хорошо
try {
    $result = $api->sweets()->give(10, 123456);
} catch (ApiException $e) {
    // Обработка ошибки
}
```

### 2. Используйте ErrorHandler для логирования

```php
try {
    $result = $api->sweets()->give(10, 123456);
} catch (ApiException $e) {
    ErrorHandler::logError($e); // Автоматическое логирование
    // Дополнительная обработка
}
```

### 3. Проверяйте возможность повтора

```php
if ($e->isRetryable()) {
    $delay = $e->getRetryDelay();
    sleep($delay);
    // Повторить операцию
}
```

### 4. Используйте пользовательские сообщения

```php
echo $e->getUserFriendlyMessage(); // Вместо $e->getMessage()
```

### 5. Настраивайте повторные попытки для критичных операций

```php
$api->balance()->setRetrySettings(5, 1, 2.0);
```

## Отладка

### Включение режима отладки

```php
define('IRIS_DEBUG', true);

$api = new IrisSweets();
$api->sweets()->enableDebug();
```

### Просмотр детальной информации

```php
$detailedInfo = $e->getDetailedInfo();
print_r($detailedInfo);
```

### Мониторинг повторных попыток

```php
$stats = $api->sweets()->getRetryStats();
print_r($stats);
```

## Заключение

Новая система обработки ошибок предоставляет:

- **Автоматическое определение типов ошибок**
- **Интеллектуальные повторные попытки**
- **Детальное логирование**
- **Пользовательские сообщения об ошибках**
- **Рекомендации по исправлению**
- **Гибкую настройку**

