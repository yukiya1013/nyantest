<?php
namespace App\Routes;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Controller\VideoController;
use App\Controller\StatsController;
use App\Middleware\CorsMiddleware;

/**
 * ApiRoutes - APIルートを登録するクラス
 */
class ApiRoutes
{
    /**
     * ルートを登録する
     *
     * @param App $app Slimアプリケーション
     * @return void
     */
    public static function register(App $app): void
    {
        // APIルートグループ
        $app->group('/api', function (RouteCollectorProxy $group) {
            // 動画関連エンドポイント
            $group->group('/videos', function (RouteCollectorProxy $group) {
                // ランダム動画取得 API
                $group->get('/random', VideoController::class . ':getRandomVideos');
                
                // 検索 API
                $group->get('', VideoController::class . ':searchVideos');
            });
            
            // マスターリスト取得 API
            $group->get('/masterlists', VideoController::class . ':getMasterlists');
            
            // 統計関連エンドポイント
            $group->group('/stats', function (RouteCollectorProxy $group) {
                // 視聴統計記録 API
                $group->post('/view', StatsController::class . ':recordView');
                
                // クリック統計記録 API
                $group->post('/click', StatsController::class . ':recordClick');
            });
        })->add(new CorsMiddleware());
        
        // ヘルスチェック用エンドポイント
        $app->get('/health', function ($request, $response) {
            $response->getBody()->write(json_encode(['status' => 'ok', 'timestamp' => time()]));
            return $response->withHeader('Content-Type', 'application/json');
        });
        
        // OPTIONSリクエスト対応（CORS用）
        $app->options('/{routes:.+}', function ($request, $response) {
            return $response;
        });
    }
}
