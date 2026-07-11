<?php

declare(strict_types=1);

define('APP_NAME', 'Minha Letra');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8085');

define('DB_HOST', getenv('DB_HOST') ?: 'minhaletra_db');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_DATABASE', getenv('DB_DATABASE') ?: '');
define('DB_USERNAME', getenv('DB_USERNAME') ?: '');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_AUTO_INIT_SCHEMA', getenv('DB_AUTO_INIT_SCHEMA') ?: '0');
