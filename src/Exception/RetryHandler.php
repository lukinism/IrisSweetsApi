<?php

namespace IrisSweetsApi\Exception;

/**
 * Класс для обработки повторных попыток при ошибках API
 */
class RetryHandler
{
    private int $maxRetries;
    private int $baseDelay;
    private float $backoffMultiplier;

    public function __construct(int $maxRetries = 3, int $baseDelay = 1, float $backoffMultiplier = 2.0)
    {
        $this->maxRetries = $maxRetries;
        $this->baseDelay = $baseDelay;
        $this->backoffMultiplier = $backoffMultiplier;
    }

    /**
     * Выполнить операцию с повторными попытками
     */
    public function execute(callable $operation, array $context = []): mixed
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts <= $this->maxRetries) {
            try {
                return $operation();
            } catch (ApiException $exception) {
                $lastException = $exception;

                if (!$exception->isRetryable()) {
                    throw $exception;
                }

                if ($attempts >= $this->maxRetries) {
                    break;
                }

                $this->logRetryAttempt($attempts + 1, $exception, $context);

                $delay = $this->calculateDelay($attempts, $exception);

                if ($delay > 0) {
                    sleep($delay);
                }
                
                $attempts++;
            }
        }

        throw $lastException;
    }

    /**
     * Вычислить задержку перед следующей попыткой
     */
    private function calculateDelay(int $attempt, ApiException $exception): int
    {
        $apiDelay = $exception->getRetryDelay();
        if ($apiDelay > 0) {
            return $apiDelay;
        }

        $delay = $this->baseDelay * pow($this->backoffMultiplier, $attempt);

        return min((int)$delay, 30);
    }

    /**
     * Логировать попытку повтора
     */
    private function logRetryAttempt(int $attemptNumber, ApiException $exception, array $context): void
    {
        $logMessage = sprintf(
            "[%s] Retry attempt %d/%d for operation. Error: %s (Type: %s, HTTP: %d)",
            date('Y-m-d H:i:s'),
            $attemptNumber,
            $this->maxRetries + 1,
            $exception->getErrorDescription(),
            $exception->getErrorType(),
            $exception->getHttpStatusCode()->value
        );

        if (!empty($context)) {
            $logMessage .= ' Context: ' . json_encode($context);
        }

        error_log($logMessage);
    }

    /**
     * Получить статистику повторных попыток
     */
    public function getStats(): array
    {
        return [
            'max_retries' => $this->maxRetries,
            'base_delay' => $this->baseDelay,
            'backoff_multiplier' => $this->backoffMultiplier,
        ];
    }

    /**
     * Установить максимальное количество повторных попыток
     */
    public function setMaxRetries(int $maxRetries): void
    {
        $this->maxRetries = $maxRetries;
    }

    /**
     * Установить базовую задержку
     */
    public function setBaseDelay(int $baseDelay): void
    {
        $this->baseDelay = $baseDelay;
    }

    /**
     * Установить множитель задержки
     */
    public function setBackoffMultiplier(float $multiplier): void
    {
        $this->backoffMultiplier = $multiplier;
    }
}




