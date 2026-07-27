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

$_ENV['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
$_ENV['SESSION_DRIVER'] = $_ENV['SESSION_DRIVER'] ?? 'cookie';
$_ENV['LOG_CHANNEL'] = 'stderr';

// Bridge Vercel serverless request to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
