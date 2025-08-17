<?php

namespace IrisSweetsApi\Exception;

class ApiException extends \Exception
{
    private int $errorCode;
    private string $errorDescription;
    private array $rawResponse;
    private HttpStatusCode $httpStatusCode;
    private string $errorType;

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, array $rawResponse = [])
    {
        parent::__construct($message, $code, $previous);
        $this->rawResponse = $rawResponse;

        if (isset($rawResponse['error'])) {
            $this->errorCode = $rawResponse['error']['code'] ?? 0;
            $this->errorDescription = $rawResponse['error']['description'] ?? '';

            $this->httpStatusCode = HttpStatusCode::fromApiError($this->errorDescription);
            $this->errorType = ErrorType::fromApiDescription($this->errorDescription);

            if (empty($message)) {
                $message = $this->errorDescription ?: 'API Error';
            }
            
            $this->message = $message;
            $this->code = $this->errorCode;
        } else {
            $this->errorCode = 0;
            $this->errorDescription = '';
            $this->httpStatusCode = HttpStatusCode::BAD_REQUEST;
            $this->errorType = ErrorType::INVALID_PARAMETERS;
        }
    }

    /**
     * Получить код ошибки от API
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * Получить описание ошибки от API
     */
    public function getErrorDescription(): string
    {
        return $this->errorDescription;
    }

    /**
     * Получить полный ответ от API
     */
    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    /**
     * Получить HTTP статус код
     */
    public function getHttpStatusCode(): HttpStatusCode
    {
        return $this->httpStatusCode;
    }

    /**
     * Получить тип ошибки
     */
    public function getErrorType(): string
    {
        return $this->errorType;
    }

    /**
     * Проверить, является ли ошибка связанной с недостатком ресурсов
     */
    public function isInsufficientResourceError(): bool
    {
        return $this->errorType === ErrorType::NOT_ENOUGH_RESOURCES ||
               str_contains(strtolower($this->errorDescription), 'not enough');
    }

    /**
     * Проверить, является ли ошибка связанной с неверным пользователем
     */
    public function isInvalidUserError(): bool
    {
        return $this->errorType === ErrorType::USER_NOT_FOUND ||
               $this->errorType === ErrorType::ACCOUNT_NOT_USER;
    }

    /**
     * Проверить, является ли ошибка связанной с валидацией
     */
    public function isValidationError(): bool
    {
        return $this->httpStatusCode === HttpStatusCode::BAD_REQUEST;
    }

    /**
     * Проверить, является ли ошибка связанной с авторизацией
     */
    public function isAuthorizationError(): bool
    {
        return $this->httpStatusCode === HttpStatusCode::UNAUTHORIZED ||
               $this->httpStatusCode === HttpStatusCode::FORBIDDEN;
    }

    /**
     * Проверить, является ли ошибка серверной
     */
    public function isServerError(): bool
    {
        return $this->httpStatusCode === HttpStatusCode::INTERNAL_SERVER_ERROR;
    }

    /**
     * Получить человекочитаемое сообщение об ошибке
     */
    public function getUserFriendlyMessage(): string
    {
        $typeDescription = ErrorType::getDescription($this->errorType);
        $httpDescription = $this->httpStatusCode->getDescription();
        
        return "{$typeDescription} ({$httpDescription}): {$this->errorDescription}";
    }

    /**
     * Получить детальную информацию об ошибке для логирования
     */
    public function getDetailedInfo(): array
    {
        return [
            'error_type' => $this->errorType,
            'error_code' => $this->errorCode,
            'error_description' => $this->errorDescription,
            'http_status_code' => $this->httpStatusCode->value,
            'http_status_description' => $this->httpStatusCode->getDescription(),
            'raw_response' => $this->rawResponse,
            'message' => $this->message,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString(),
        ];
    }

    /**
     * Проверить, можно ли повторить операцию
     */
    public function isRetryable(): bool
    {
        // Повторяем только серверные ошибки и ошибки конфликтов
        return $this->httpStatusCode === HttpStatusCode::INTERNAL_SERVER_ERROR ||
               $this->httpStatusCode === HttpStatusCode::CONFLICT;
    }

    /**
     * Получить рекомендуемую задержку перед повторной попыткой (в секундах)
     */
    public function getRetryDelay(): int
    {
        return match($this->httpStatusCode) {
            HttpStatusCode::INTERNAL_SERVER_ERROR => 5,  // 5 секунд для серверных ошибок
            HttpStatusCode::CONFLICT => 1,               // 1 секунда для конфликтов
            default => 0,                               // Не повторяем для других ошибок
        };
    }
}

