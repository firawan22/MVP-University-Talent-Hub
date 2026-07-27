<?php

// Catch all errors and display details for debugging
try {
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
    putenv('APP_KEY=base64:wQcNO5snKm8givcJU8BtcRBaF0akrfjNzezdQ3ax6MA=');

    $_ENV['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_ENV['CACHE_STORE'] = 'array';
    $_ENV['LOG_CHANNEL'] = 'stderr';
    $_ENV['APP_DEBUG'] = 'true';
    $_ENV['APP_KEY'] = 'base64:wQcNO5snKm8givcJU8BtcRBaF0akrfjNzezdQ3ax6MA=';

    $_SERVER['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
    $_SERVER['SESSION_DRIVER'] = 'cookie';
    $_SERVER['CACHE_STORE'] = 'array';
    $_SERVER['LOG_CHANNEL'] = 'stderr';
    $_SERVER['APP_DEBUG'] = 'true';
    $_SERVER['APP_KEY'] = 'base64:wQcNO5snKm8givcJU8BtcRBaF0akrfjNzezdQ3ax6MA=';

    // Bridge Vercel serverless request to Laravel's public/index.php
    require __DIR__ . '/../public/index.php';

} catch (\Throwable $e) {
    http_response_code(200);
    echo '<div style="font-family: sans-serif; padding: 20px; background: #fff3f3; color: #900; border: 1px solid #fcc; border-radius: 8px;">';
    echo '<h2>Laravel Serverless Diagnostic Handler</h2>';
    echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' on line ' . $e->getLine() . '</p>';
    echo '<pre style="background: #111; color: #0f0; padding: 15px; overflow: auto; border-radius: 6px;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
