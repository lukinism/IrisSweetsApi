# Exchange API - Работа с биржей

Модуль Exchange предоставляет функционал для работы с биржей Iris, включая получение стакана заявок и анализ торговых данных.

## Инициализация

```php
use IrisSweetsApi\IrisSweets;

$api = new IrisSweets();
$exchange = $api->exchange();
```

## Основные методы

### Получение стакана заявок

```php
// Получить полный стакан заявок
$orderBook = $exchange->getOrderBook();
// Возвращает: ['buy' => [...], 'sell' => [...]]

// Получить только заявки на покупку
$buyOrders = $exchange->getBuyOrders();

// Получить только заявки на продажу
$sellOrders = $exchange->getSellOrders();
```

### Анализ цен

```php
// Лучшая цена покупки (самая высокая среди заявок на покупку)
$bestBid = $exchange->getBestBidPrice();

// Лучшая цена продажи (самая низкая среди заявок на продажу)
$bestAsk = $exchange->getBestAskPrice();

// Спред (разница между лучшей ценой продажи и покупки)
$spread = $exchange->getSpread();
```

## Расширенная работа с OrderBook

Для более детального анализа используйте объект OrderBook:

```php
$orderBook = $exchange->orderBook();

// Общие объемы
$totalBuyVolume = $orderBook->getTotalBuyVolume();
$totalSellVolume = $orderBook->getTotalSellVolume();

// Отсортированные заявки
$buyOrdersSorted = $orderBook->getBuyOrdersSorted(); // По убыванию цены
$sellOrdersSorted = $orderBook->getSellOrdersSorted(); // По возрастанию цены

// Поиск заявок в диапазоне цен
$filteredOrders = $orderBook->getOrdersInPriceRange(0.85, 0.95);
```

## Структура данных

### Заявка
```php
[
    'price' => 0.92,    // Цена
    'volume' => 2852    // Объем
]
```

### Стакан заявок
```php
[
    'buy' => [          // Заявки на покупку
        ['price' => 0.92, 'volume' => 2852],
        ['price' => 0.91, 'volume' => 7048],
        // ...
    ],
    'sell' => [         // Заявки на продажу
        ['price' => 0.94, 'volume' => 10269],
        ['price' => 0.95, 'volume' => 11004],
        // ...
    ]
]
```

## Примеры использования

### Базовый анализ рынка

```php
$exchange = $api->exchange();

// Получаем основные показатели
$bestBid = $exchange->getBestBidPrice();
$bestAsk = $exchange->getBestAskPrice();
$spread = $exchange->getSpread();

echo "Лучшая цена покупки: $bestBid\n";
echo "Лучшая цена продажи: $bestAsk\n";
echo "Спред: $spread\n";
```

### Анализ ликвидности

```php
$orderBook = $exchange->orderBook();

$totalBuyVolume = $orderBook->getTotalBuyVolume();
$totalSellVolume = $orderBook->getTotalSellVolume();

echo "Общий объем заявок на покупку: $totalBuyVolume\n";
echo "Общий объем заявок на продажу: $totalSellVolume\n";

// Соотношение объемов
$ratio = $totalBuyVolume / $totalSellVolume;
echo "Соотношение покупка/продажа: " . round($ratio, 2) . "\n";
```

### Поиск лучших цен в диапазоне

```php
$orderBook = $exchange->orderBook();

// Ищем заявки в диапазоне 0.8-1.0
$filteredOrders = $orderBook->getOrdersInPriceRange(0.8, 1.0);

echo "Заявок на покупку в диапазоне: " . count($filteredOrders['buy']) . "\n";
echo "Заявок на продажу в диапазоне: " . count($filteredOrders['sell']) . "\n";
```

### Мониторинг топ-заявок

```php
$orderBook = $exchange->orderBook();

// Топ-5 заявок на покупку
$topBuyOrders = array_slice($orderBook->getBuyOrdersSorted(), 0, 5);
echo "Топ-5 заявок на покупку:\n";
foreach ($topBuyOrders as $order) {
    echo "Цена: {$order['price']}, Объем: {$order['volume']}\n";
}

// Топ-5 заявок на продажу
$topSellOrders = array_slice($orderBook->getSellOrdersSorted(), 0, 5);
echo "\nТоп-5 заявок на продажу:\n";
foreach ($topSellOrders as $order) {
    echo "Цена: {$order['price']}, Объем: {$order['volume']}\n";
}
```

## Обработка ошибок

```php
try {
    $orderBook = $exchange->getOrderBook();
    // Работа с данными
} catch (ApiException $e) {
    echo "Ошибка API: " . $e->getMessage() . "\n";
    echo "Код ошибки: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "Общая ошибка: " . $e->getMessage() . "\n";
}
```

## Работа с историей сделок

### Получение сделок

```php
// Получить все последние сделки
$deals = $exchange->getDeals();

// Получить сделки с определенного ID
$deals = $exchange->getDeals(644700);

// Получить объект для детальной работы с историей
$dealsObj = $exchange->deals();
```

### Фильтрация сделок

```php
// Сделки за последние N минут/часов/дней
$recentDeals = $dealsObj->getDealsForLastMinutes(30);
$hourlyDeals = $dealsObj->getDealsForLastHours(2);
$dailyDeals = $dealsObj->getDealsForLastDays(1);

// Сделки по типу
$buyDeals = $dealsObj->getBuyDeals();
$sellDeals = $dealsObj->getSellDeals();

// Сделки в диапазоне цен
$filteredDeals = $dealsObj->getDealsInPriceRange(0.9, 1.0);

// Крупные сделки
$largeDeals = $dealsObj->getDealsWithMinVolume(100);
```

### Сортировка сделок

```php
// По цене
$dealsByPrice = $dealsObj->getDealsSortedByPrice('desc'); // убывание
$dealsByPrice = $dealsObj->getDealsSortedByPrice('asc');  // возрастание

// По объему
$dealsByVolume = $dealsObj->getDealsSortedByVolume('desc');

// По времени
$dealsByTime = $dealsObj->getDealsSortedByTime('desc');
```

### Статистика

```php
// Получить статистику по всем сделкам
$stats = $exchange->getDealsStats();

// Статистика по сделкам с определенного ID
$stats = $dealsObj->getDealsStats(644700);
```

### Структура сделки

```php
[
    'id' => 644756,           // ID сделки
    'group_id' => 0,          // ID группы (0 если не групповая)
    'date' => 1757231536,     // Unix timestamp
    'price' => 0.94,          // Цена сделки
    'volume' => 50,           // Объем
    'type' => 'buy'           // Тип: 'buy' или 'sell'
]
```

### Статистика сделок

```php
[
    'total_deals' => 100,     // Общее количество сделок
    'total_volume' => 5000,   // Общий объем
    'total_value' => 4700.0,  // Общая стоимость
    'avg_price' => 0.94,      // Средняя цена
    'min_price' => 0.85,      // Минимальная цена
    'max_price' => 1.05,      // Максимальная цена
    'buy_deals' => 60,        // Количество сделок на покупку
    'sell_deals' => 40,       // Количество сделок на продажу
    'buy_volume' => 3000,     // Объем покупок
    'sell_volume' => 2000     // Объем продаж
]
```

## Примечания

- API биржи не требует аутентификации (botId и irisToken)
- Данные обновляются в реальном времени
- Все цены возвращаются в виде float
- Объемы возвращаются в виде int
- При отсутствии заявок методы могут возвращать null или пустые массивы
- Сделки возвращаются в порядке убывания ID (новые сначала)
- Время сделок указано в Unix timestamp
