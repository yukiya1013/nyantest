<?php
/**
 * bootstrap.php - アプリケーションの初期化
 * 
 * Slim アプリケーションのインスタンスを生成し、ミドルウェアを登録します。
 */

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

// 設定ファイルの読み込み
require_once __DIR__ . '/settings.php';

// DIコンテナの構築
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions($settings);
require_once __DIR__ . '/dependencies.php';
$container = $containerBuilder->build();

// Slimアプリケーションの作成
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// ミドルウェアの登録
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// CORSミドルウェアの追加
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader('Cache-Control', 'max-age=86400'); // 24時間のキャッシュ
});

// エラーミドルウェアの追加
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// ルートの登録
require_once __DIR__ . '/routes.php';

return $app;
