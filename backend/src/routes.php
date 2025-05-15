<?php
/**
 * routes.php - ルート定義
 * 
 * APIエンドポイントとハンドラのマッピングを定義します。
 */

use Slim\Routing\RouteCollectorProxy;
use App\Controller\VideoController;
use App\Controller\StatsController;

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
});

// ヘルスチェック用エンドポイント
$app->get('/health', function ($request, $response) {
    return $response->withJson(['status' => 'ok', 'timestamp' => time()]);
});

// OPTIONSリクエスト対応（CORS用）
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});
