<?php
/**
 * dependencies.php - DIコンテナの定義
 * 
 * PHP-DIを使用してサービスをコンテナにバインドします。
 */

use Psr\Container\ContainerInterface;
use App\Controller\VideoController;
use App\Controller\StatsController;
use App\Service\VideoService;
use App\Service\StatsService;
use App\Infrastructure\Repository\VideoRepository;
use App\Infrastructure\Repository\StatsRepository;

$containerBuilder->addDefinitions([
    // PDO
    PDO::class => function (ContainerInterface $c) {
        $settings = $c->get('db');
        $pdo = new PDO($settings['dsn'], $settings['user'], $settings['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    },
    
    // リポジトリ
    VideoRepository::class => function (ContainerInterface $c) {
        return new VideoRepository($c->get(PDO::class));
    },
    StatsRepository::class => function (ContainerInterface $c) {
        return new StatsRepository($c->get(PDO::class));
    },
    
    // サービス
    VideoService::class => function (ContainerInterface $c) {
        return new VideoService(
            $c->get(VideoRepository::class),
            $c->get('affiliate')['id']
        );
    },
    StatsService::class => function (ContainerInterface $c) {
        return new StatsService($c->get(StatsRepository::class));
    },
    
    // コントローラ
    VideoController::class => function (ContainerInterface $c) {
        return new VideoController($c->get(VideoService::class));
    },
    StatsController::class => function (ContainerInterface $c) {
        return new StatsController($c->get(StatsService::class));
    },
]);
