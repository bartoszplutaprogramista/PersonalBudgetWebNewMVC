<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */

class Config
{
    private static function env(string $key, bool $required = true): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($required && ($value === false || $value === null)) {
            throw new \RuntimeException("Brak wymaganej zmiennej środowiskowej: {$key}");
        }

        return $value;
    }


    public static function DB_HOST(): string
    {
        return self::env('DB_HOST');
    }

    public static function DB_NAME(): string
    {
        return self::env('DB_NAME');
    }

    public static function DB_USER(): string
    {
        return self::env('DB_USER');
    }

    public static function DB_PASSWORD(): string
    {
        return self::env('DB_PASSWORD');
    }

    public static function SHOW_ERRORS(): bool
    {
        return filter_var(self::env('SHOW_ERRORS'), FILTER_VALIDATE_BOOL);
    }

    public static function SECRET_KEY(): string
    {
        return self::env('APP_SECRET_KEY');
    }

    public static function ENVIRONMENT(): string
    {
        return self::env('APP_ENV', false) ?? 'production';
    }

    //////////////////MAIL//////////////////////
    public static function MAIL_HOST(): string 
    { 
        return self::env('MAIL_HOST'); 
    }
    public static function MAIL_PORT(): int 
    { 
        return (int) self::env('MAIL_PORT'); 
    }
    public static function MAIL_USERNAME(): string 
    { 
        return self::env('MAIL_USERNAME'); 
    }
    public static function MAIL_PASSWORD(): string 
    { 
        return self::env('MAIL_PASSWORD'); 
    }
    public static function MAIL_FROM(): string 
    { 
        return self::env('MAIL_FROM'); 
    }
    public static function MAIL_FROM_NAME(): string 
    { 
        return self::env('MAIL_FROM_NAME'); 
    }
    public static function MAIL_SECURE(): string 
    { 
        return self::env('MAIL_SECURE'); 
    }

    /////////////////CAPTCHA/////////////////////
    public static function CAPTCHA_SECRET_KEY(): string
    {
        return self::env('CAPTCHA_SECRET_KEY');
    }
    public static function CAPTCHA_SITE_KEY(): string
    {
        return self::env('CAPTCHA_SITE_KEY');
    }
}
