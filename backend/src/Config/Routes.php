<?php
namespace App\Config;

/**
 * Routes - ルート定義を管理するクラス
 */
class Routes
{
    /**
     * APIルートを定義する
     *
     * @return array ルート定義の配列
     */
    public static function getApiRoutes(): array
    {
        return [
            // 動画関連エンドポイント
            [
                'method' => 'GET',
                'pattern' => '/api/videos/random',
                'handler' => 'App\Controller\VideoController:getRandomVideos'
            ],
            [
                'method' => 'GET',
                'pattern' => '/api/videos',
                'handler' => 'App\Controller\VideoController:searchVideos'
            ],
            
            // マスターリスト取得 API
            [
                'method' => 'GET',
                'pattern' => '/api/masterlists',
                'handler' => 'App\Controller\VideoController:getMasterlists'
            ],
            
            // 統計関連エンドポイント
            [
                'method' => 'POST',
                'pattern' => '/api/stats/view',
                'handler' => 'App\Controller\StatsController:recordView'
            ],
            [
                'method' => 'POST',
                'pattern' => '/api/stats/click',
                'handler' => 'App\Controller\StatsController:recordClick'
            ],
        ];
    }
}
