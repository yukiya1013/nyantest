<?php
namespace App\Config;

/**
 * Container - DIコンテナの設定を管理するクラス
 */
class Container
{
    /**
     * DIコンテナの定義を取得する
     *
     * @return array コンテナ定義の配列
     */
    public static function getDefinitions(): array
    {
        return [
            'settings' => function () {
                return require __DIR__ . '/../settings.php';
            },
            
            // PDO
            PDO::class => function ($c) {
                $settings = $c->get('settings')['db'];
                $pdo = new PDO($settings['dsn'], $settings['user'], $settings['pass']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                return $pdo;
            },
            
            // リポジトリ
            'App\Infrastructure\Repository\VideoRepository' => function ($c) {
                return new \App\Infrastructure\Repository\VideoRepository($c->get(PDO::class));
            },
            'App\Infrastructure\Repository\StatsRepository' => function ($c) {
                return new \App\Infrastructure\Repository\StatsRepository($c->get(PDO::class));
            },
            
            // サービス
            'App\Service\VideoService' => function ($c) {
                return new \App\Service\VideoService(
                    $c->get('App\Infrastructure\Repository\VideoRepository'),
                    $c->get('settings')['affiliate']['id']
                );
            },
            'App\Service\StatsService' => function ($c) {
                return new \App\Service\StatsService($c->get('App\Infrastructure\Repository\StatsRepository'));
            },
            
            // コントローラ
            'App\Controller\VideoController' => function ($c) {
                return new \App\Controller\VideoController($c->get('App\Service\VideoService'));
            },
            'App\Controller\StatsController' => function ($c) {
                return new \App\Controller\StatsController($c->get('App\Service\StatsService'));
            },
        ];
    }
}
