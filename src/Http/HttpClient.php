<?php

namespace IrisSweetsApi\Http;

use IrisSweetsApi\Exception\ApiException;

class HttpClient
{
    private array $headers = [];
    private int $timeout = 30;
    private bool $debug = false;
    private ?string $proxy = null;

    public function __construct(array $headers = [], int $timeout = 30, bool $debug = false, ?string $proxy = null)
    {
        $this->headers = $headers;
        $this->timeout = $timeout;
        $this->debug = $debug;
        $this->proxy = $proxy;
    }

    public function get(string $url, array $queryParams = []): string|false
    {
        if (!empty($queryParams)) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($queryParams);
            if ($this->debug) {
                error_log("Request URL: " . $url);
            }
        }

        $ch = curl_init();

        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $this->formatHeaders($this->headers),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => true, // Получаем заголовки для анализа HTTP статуса
        ];

        // Настройка прокси, если указан
        if ($this->proxy !== null && $this->proxy !== '') {
            $curlOptions[CURLOPT_PROXY] = $this->proxy;
            
            if ($this->debug) {
                error_log("Using proxy: " . $this->proxy);
            }
        }

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        if (curl_errno($ch)) {
            $errorCode = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($this->debug) {
                error_log("cURL error: " . $error);
            }
            
            throw new ApiException("Ошибка cURL: " . $error, 0, null, [
                'error' => [
                    'code' => $errorCode,
                    'description' => $error
                ]
            ]);
        }

        curl_close($ch);

        // Разделяем заголовки и тело ответа
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        // Проверяем HTTP статус код
        if ($httpCode >= 400) {
            $this->handleHttpError($httpCode, $body, $url);
        }

        if ($this->debug) {
            error_log("Response HTTP Code: " . $httpCode);
            error_log("Response Body: " . $body);
        }

        return $body;
    }

    /**
     * Обработать HTTP ошибки
     */
    private function handleHttpError(int $httpCode, string $body, string $url): void
    {
        $decodedBody = json_decode($body, true) ?: [];
        
        // Если API вернул структурированную ошибку, используем её
        if (isset($decodedBody['error'])) {
            throw new ApiException(
                $decodedBody['error']['description'] ?? 'HTTP Error',
                $decodedBody['error']['code'] ?? $httpCode,
                null,
                $decodedBody
            );
        }

        // Иначе создаем стандартную ошибку на основе HTTP кода
        $errorDescription = match($httpCode) {
            400 => 'Bad Request - Некорректный запрос',
            401 => 'Unauthorized - Не авторизован',
            403 => 'Forbidden - Доступ запрещен',
            404 => 'Not Found - Ресурс не найден',
            409 => 'Conflict - Конфликт ресурсов',
            429 => 'Too Many Requests - Слишком много запросов',
            500 => 'Internal Server Error - Внутренняя ошибка сервера',
            502 => 'Bad Gateway - Ошибка шлюза',
            503 => 'Service Unavailable - Сервис недоступен',
            default => "HTTP Error {$httpCode} - Неизвестная ошибка"
        };

        throw new ApiException(
            $errorDescription,
            $httpCode,
            null,
            [
                'error' => [
                    'code' => $httpCode,
                    'description' => $errorDescription
                ],
                'url' => $url,
                'response_body' => $body
            ]
        );
    }

    private function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "$key: $value";
        }
        return $formatted;
    }

    /**
     * Включить/выключить режим отладки
     */
    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * Установить таймаут
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * Добавить заголовок
     */
    public function addHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    /**
     * Установить заголовки
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * Установить прокси
     * @param string|null $proxy Формат: http://user:pass@host:port или socks5://user:pass@host:port или host:port
     */
    public function setProxy(?string $proxy): void
    {
        $this->proxy = $proxy;
    }

    /**
     * Получить текущий прокси
     */
    public function getProxy(): ?string
    {
        return $this->proxy;
    }
}
