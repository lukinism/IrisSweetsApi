<?php

namespace IrisSweetsApi\Exception;

enum HttpStatusCode: int
{
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case CONFLICT = 409;
    case INTERNAL_SERVER_ERROR = 500;
    
    /**
     * Получить человекочитаемое описание статус кода
     */
    public function getDescription(): string
    {
        return match($this) {
            self::BAD_REQUEST => 'Некорректный запрос',
            self::UNAUTHORIZED => 'Не авторизован',
            self::FORBIDDEN => 'Доступ запрещен',
            self::NOT_FOUND => 'Не найдено',
            self::CONFLICT => 'Недостаточно валюты',
            self::INTERNAL_SERVER_ERROR => 'Внутренняя ошибка сервера',
        };
    }
    
    /**
     * Получить статус код из строки ошибки API
     */
    public static function fromApiError(string $errorDescription): self
    {
        $errorLower = strtolower($errorDescription);
        
        return match(true) {
            str_contains($errorLower, 'user not found') => self::NOT_FOUND,
            str_contains($errorLower, 'account is not user') => self::FORBIDDEN,
            str_contains($errorLower, 'ошибка в расчёте сжигании ирисок') => self::INTERNAL_SERVER_ERROR,
            str_contains($errorLower, 'not enough'), str_contains($errorLower, 'unsuccessful') => self::CONFLICT,
            default => self::BAD_REQUEST,
        };
    }
}




