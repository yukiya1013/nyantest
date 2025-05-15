<?php
/**
 * settings.php - アプリケーション設定
 * 
 * Slim アプリケーションの設定を定義します。
 */

// 環境変数の読み込み
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

// デフォルト設定
$settings = [
    'displayErrorDetails' => true,
    'logErrorDetails' => true,
    'logErrors' => true,
    'db' => [
        'dsn' => $_ENV['DB_DSN'] ?? 'mysql:host=localhost;dbname=yukiya1013_tiktokfanza;charset=utf8mb4',
        'user' => $_ENV['DB_USER'] ?? 'yukiya1013_user',
        'pass' => $_ENV['DB_PASS'] ?? '123newny',
    ],
    'affiliate' => [
        'id' => $_ENV['AFFILIATE_ID'] ?? 'AVcollecter-005',
    ],
    'video' => [
        'offset_sec' => $_ENV['VIDEO_OFFSET_SEC'] ?? 10,
    ],
];

return $settings;
