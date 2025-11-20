# Примеры использования Wallet & Account API

## 💼 WalletService - Управление кошельком

### Депозиты

```php
use Tigusigalpa\BingX\Facades\Bingx;

// История депозитов с фильтрацией
$deposits = Bingx::wallet()->getDepositHistory(
    coin: 'USDT',
    status: 1,  // 0: pending, 6: credited but cannot withdraw, 1: success
    startTime: strtotime('2024-01-01') * 1000,
    endTime: strtotime('2024-01-31') * 1000,
    offset: 0,
    limit: 100
);

// Получить адрес для депозита
$address = Bingx::wallet()->getDepositAddress('USDT', 'TRC20');
echo "Адрес депозита: " . $address['address'];

// Записи контроля рисков депозитов
$riskRecords = Bingx::wallet()->getDepositRiskRecords(
    coin: 'USDT',
    startTime: strtotime('2024-01-01') * 1000,
    endTime: strtotime('2024-01-31') * 1000
);
```

### Выводы

```php
// История выводов
$withdrawals = Bingx::wallet()->getWithdrawalHistory(
    coin: 'USDT',
    status: 6,  // 6: Completed
    startTime: strtotime('2024-01-01') * 1000,
    endTime: strtotime('2024-01-31') * 1000,
    limit: 100
);

// Создать запрос на вывод
$withdrawal = Bingx::wallet()->withdraw(
    coin: 'USDT',
    address: 'TXxx...xxx',
    amount: 100.0,
    network: 'TRC20',
    addressTag: null,
    walletType: '0'  // 0: spot wallet, 1: fund wallet
);

echo "ID вывода: " . $withdrawal['id'];
```

### Информация о монетах

```php
// Получить информацию о всех доступных монетах
$coins = Bingx::wallet()->getAllCoinInfo();

foreach ($coins as $coin) {
    echo "{$coin['coin']}: {$coin['name']}\n";
    echo "Сети: " . implode(', ', array_column($coin['networkList'], 'network')) . "\n";
}
```

## 💰 SpotAccountService - Спотовый аккаунт

### Баланс

```php
// Получить баланс спотового аккаунта
$balance = Bingx::spotAccount()->getBalance(recvWindow: 60000);

foreach ($balance['balances'] as $asset) {
    if ($asset['free'] > 0 || $asset['locked'] > 0) {
        echo "{$asset['asset']}: Free={$asset['free']}, Locked={$asset['locked']}\n";
    }
}

// Получить баланс фонда
$fundBalance = Bingx::spotAccount()->getFundBalance();

// Получить балансы всех аккаунтов (спот, фьючерсы, фонд)
$allBalances = Bingx::spotAccount()->getAllAccountBalances();
```

### Универсальные трансферы

```php
// Перевод из фонда в фьючерсы
$transfer = Bingx::spotAccount()->universalTransfer(
    type: 'FUND_PFUTURES',
    asset: 'USDT',
    amount: 100.0
);

echo "Трансфер ID: " . $transfer['tranId'];

// Типы трансферов:
// FUND_SFUTURES - Фонд -> Стандартные фьючерсы
// SFUTURES_FUND - Стандартные фьючерсы -> Фонд
// FUND_PFUTURES - Фонд -> Бессрочные фьючерсы
// PFUTURES_FUND - Бессрочные фьючерсы -> Фонд

// История трансферов
$history = Bingx::spotAccount()->getAssetTransferRecords(
    type: 'FUND_PFUTURES',
    startTime: strtotime('-30 days') * 1000,
    endTime: time() * 1000,
    current: 1,
    size: 50
);
```

### Внутренние переводы (между основным и суб-аккаунтами)

```php
// Перевод с основного на суб-аккаунт
$internalTransfer = Bingx::spotAccount()->internalTransfer(
    coin: 'USDT',
    walletType: 'SPOT',
    amount: 50.0,
    transferType: 'FROM_MAIN_TO_SUB',
    subUid: '123456',
    clientId: 'my-transfer-001'
);

// Перевод с суб-аккаунта на основной
$reverseTransfer = Bingx::spotAccount()->internalTransfer(
    coin: 'USDT',
    walletType: 'PERPETUAL',
    amount: 25.0,
    transferType: 'FROM_SUB_TO_MAIN',
    subUid: '123456',
    clientId: 'my-transfer-002'
);

// История внутренних переводов
$internalHistory = Bingx::spotAccount()->getInternalTransferRecords(
    clientId: 'my-transfer-001',
    startTime: strtotime('-7 days') * 1000,
    endTime: time() * 1000
);

// История внутренних переводов основного аккаунта
$mainAccountHistory = Bingx::spotAccount()->getMainAccountInternalTransferRecords(
    startTime: strtotime('-30 days') * 1000,
    endTime: time() * 1000,
    current: 1,
    size: 50
);
```

### Поддерживаемые монеты для трансфера

```php
// Получить список монет, доступных для трансфера
$supportedCoins = Bingx::spotAccount()->getSupportedTransferCoins();

foreach ($supportedCoins as $coin) {
    echo "{$coin['coin']}: {$coin['name']}\n";
}
```

## 🔄 Комплексный пример: Автоматизация депозита и трансфера

```php
use Tigusigalpa\BingX\Facades\Bingx;

// 1. Получить адрес депозита
$depositAddress = Bingx::wallet()->getDepositAddress('USDT', 'TRC20');
echo "Отправьте USDT на адрес: {$depositAddress['address']}\n";

// 2. Мониторинг депозита (в цикле или через webhook)
do {
    sleep(60); // Проверяем каждую минуту
    
    $deposits = Bingx::wallet()->getDepositHistory(
        coin: 'USDT',
        status: 1,
        startTime: strtotime('-1 hour') * 1000,
        limit: 10
    );
    
    $newDeposit = null;
    foreach ($deposits as $deposit) {
        if ($deposit['status'] == 1 && $deposit['amount'] > 0) {
            $newDeposit = $deposit;
            break;
        }
    }
    
} while (!$newDeposit);

echo "Депозит получен: {$newDeposit['amount']} USDT\n";

// 3. Автоматический трансфер на фьючерсный аккаунт
$transfer = Bingx::spotAccount()->universalTransfer(
    type: 'FUND_PFUTURES',
    asset: 'USDT',
    amount: $newDeposit['amount']
);

echo "Средства переведены на фьючерсный аккаунт\n";

// 4. Проверка баланса
$balance = Bingx::account()->getBalance();
echo "Баланс фьючерсов: {$balance['balance']['balance']} USDT\n";
```

## 🛡️ Обработка ошибок

```php
use Tigusigalpa\BingX\Facades\Bingx;
use Tigusigalpa\BingX\Exceptions\ApiException;
use Tigusigalpa\BingX\Exceptions\InsufficientBalanceException;

try {
    // Попытка вывода
    $withdrawal = Bingx::wallet()->withdraw(
        coin: 'USDT',
        address: 'TXxx...xxx',
        amount: 1000.0,
        network: 'TRC20'
    );
    
    echo "Вывод создан: {$withdrawal['id']}\n";
    
} catch (InsufficientBalanceException $e) {
    echo "Недостаточно средств для вывода\n";
    
} catch (ApiException $e) {
    echo "Ошибка API: {$e->getMessage()}\n";
    echo "Код ошибки: {$e->getCode()}\n";
    
} catch (\Exception $e) {
    echo "Общая ошибка: {$e->getMessage()}\n";
}
```

## 📊 Мониторинг и отчетность

```php
// Генерация отчета по депозитам за месяц
$startTime = strtotime('first day of last month') * 1000;
$endTime = strtotime('last day of last month') * 1000;

$deposits = Bingx::wallet()->getDepositHistory(
    startTime: $startTime,
    endTime: $endTime,
    limit: 1000
);

$totalDeposited = 0;
$depositsByCoin = [];

foreach ($deposits as $deposit) {
    if ($deposit['status'] == 1) {
        $coin = $deposit['coin'];
        $amount = $deposit['amount'];
        
        $totalDeposited += $amount;
        $depositsByCoin[$coin] = ($depositsByCoin[$coin] ?? 0) + $amount;
    }
}

echo "Отчет по депозитам за прошлый месяц:\n";
echo "Всего депозитов: " . count($deposits) . "\n";
foreach ($depositsByCoin as $coin => $amount) {
    echo "{$coin}: {$amount}\n";
}

// Аналогично для выводов
$withdrawals = Bingx::wallet()->getWithdrawalHistory(
    startTime: $startTime,
    endTime: $endTime,
    limit: 1000
);

$totalWithdrawn = 0;
foreach ($withdrawals as $withdrawal) {
    if ($withdrawal['status'] == 6) {
        $totalWithdrawn += $withdrawal['amount'];
    }
}

echo "\nВсего выведено: {$totalWithdrawn}\n";
echo "Чистый приток: " . ($totalDeposited - $totalWithdrawn) . "\n";
```
