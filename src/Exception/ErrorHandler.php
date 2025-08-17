<?php

namespace IrisSweetsApi\Exception;

/**
 * Утилитарный класс для обработки ошибок API
 */
class ErrorHandler
{
    /**
     * Обработать ошибку API и вернуть пользовательское сообщение
     */
    public static function handle(ApiException $exception): string
    {
        self::logError($exception);

        return $exception->getUserFriendlyMessage();
    }

    /**
     * Логировать ошибку
     */
    public static function logError(ApiException $exception): void
    {
        $logData = $exception->getDetailedInfo();

        $logMessage = sprintf(
            "[%s] API Error: %s (Type: %s, HTTP: %d, Code: %d) in %s:%d",
            date('Y-m-d H:i:s'),
            $exception->getErrorDescription(),
            $exception->getErrorType(),
            $exception->getHttpStatusCode()->value,
            $exception->getErrorCode(),
            $logData['file'],
            $logData['line']
        );

        error_log($logMessage);
        
        // Для отладки можно также вывести детальную информацию
        if (defined('IRIS_DEBUG') && IRIS_DEBUG) {
            error_log('Detailed error info: ' . json_encode($logData, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Проверить, нужно ли повторить операцию
     */
    public static function shouldRetry(ApiException $exception): bool
    {
        return $exception->isRetryable();
    }

    /**
     * Получить рекомендуемую задержку перед повторной попыткой
     */
    public static function getRetryDelay(ApiException $exception): int
    {
        return $exception->getRetryDelay();
    }

    /**
     * Пользовательское сообщение об ошибке
     */
    public static function createUserMessage(ApiException $exception): string
    {
        $errorType = $exception->getErrorType();
        
        return match($errorType) {
            ErrorType::NOT_ENOUGH_RESOURCES => 'Недостаточно ирисок или голды',
            ErrorType::USER_NOT_FOUND => 'Пользователь не найден',
            ErrorType::ACCOUNT_NOT_USER => 'Данный аккаунт не пользователь',
            ErrorType::CALCULATION_ERROR => 'Ошибка в расчёте сжигании ирисок. Обратитесь к агентам',
            ErrorType::UNSUCCESSFUL_DECREASE => 'Не удалось выполнить операцию',
            ErrorType::SWEETS_GOLD_ZERO => 'Количество ирисок или голды равно нулю',
            default => 'Произошла ошибка при выполнении операции',
        };
    }

    /**
     * Получить рекомендации по исправлению ошибки
     */
    public static function getRecommendations(ApiException $exception): array
    {
        $errorType = $exception->getErrorType();
        
        return match($errorType) {
            ErrorType::NOT_ENOUGH_RESOURCES => [
                'Проверьте баланс',
                'Убедитесь, что у вас достаточно средств',
                'Попробуйте выполнить операцию позже'
            ],
            ErrorType::USER_NOT_FOUND, ErrorType::ACCOUNT_NOT_USER => [
                'Проверьте правильность ID пользователя',
                'Убедитесь, что пользователь существует',
                'Проверьте права доступа'
            ],
            ErrorType::CALCULATION_ERROR => [
                'Ошибка, попробуйте позже.',
                'А так же обратитесь к агентам',
            ],
            ErrorType::UNSUCCESSFUL_DECREASE => [
                'Проверьте баланс',
                'Попробуйте выполнить операцию позже'
            ],
            ErrorType::SWEETS_GOLD_ZERO => [
                'Проверьте, что количество больше нуля',
                'Убедитесь в корректности входных данных',
                'Используйте положительные значения'
            ],
            default => [
                'Проверьте входные данные',
                'Убедитесь в корректности запроса',
                'Попробуйте выполнить операцию позже'
            ],
        };
    }

    /**
     * Форматировать ошибку для отображения пользователю
     */
    public static function formatForUser(ApiException $exception): array
    {
        return [
            'message' => self::createUserMessage($exception),
            'type' => $exception->getErrorType(),
            'http_status' => $exception->getHttpStatusCode()->value,
            'recommendations' => self::getRecommendations($exception),
            'retryable' => $exception->isRetryable(),
            'retry_delay' => $exception->getRetryDelay(),
        ];
    }
}
