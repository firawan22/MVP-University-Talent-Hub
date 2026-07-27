<?php

// Ensure /tmp storage directories exist for Vercel Serverless environment
$storagePath = '/tmp/storage';

foreach ([
    $storagePath . '/framework/views',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/cache',
    $storagePath . '/bootstrap/cache',
    $storagePath . '/logs',
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('LOG_CHANNEL=stderr');
putenv('APP_DEBUG=true');

$_ENV['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['CACHE_STORE'] = 'array';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['APP_DEBUG'] = 'true';

$_SERVER['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
$_SERVER['SESSION_DRIVER'] = 'cookie';
$_SERVER['CACHE_STORE'] = 'array';
$_SERVER['LOG_CHANNEL'] = 'stderr';
$_SERVER['APP_DEBUG'] = 'true';

// Bridge Vercel serverless request to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
