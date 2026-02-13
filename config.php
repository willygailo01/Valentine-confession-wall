<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | Database (XAMPP local defaults)
 |--------------------------------------------------------------------------
 */
const DB_HOST = '127.0.0.1';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'valentine_confession';
const DB_PORT = 3306;

/*
 |--------------------------------------------------------------------------
 | Admin account
 |--------------------------------------------------------------------------
 | Username can be changed directly.
 | Password is stored as hash; generate a new one with:
 | php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT), PHP_EOL;"
 */
const ADMIN_USERNAME = 'willy';
const ADMIN_PASSWORD_HASH = '$2y$12$lUt5YIHn.HSkdrnr.CWh0exiKi3GfV8Dac9eQ6R2Z392FdMcQ0ZL6'; // willy29

/*
 |--------------------------------------------------------------------------
 | App settings
 |--------------------------------------------------------------------------
 */
const RATE_LIMIT_SECONDS = 30;
const MESSAGE_MAX_LENGTH = 500;
const FEEDBACK_RATE_LIMIT_SECONDS = 20;
const FEEDBACK_MAX_LENGTH = 700;
const AUTO_DELETE_DAYS = 30;
