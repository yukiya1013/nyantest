<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Service\StatsService;

/**
 * StatsController - 統計情報関連のAPIエンドポイントを処理するコントローラ
 */
class StatsController
{
    private $statsService;

    /**
     * コンストラクタ
     *
     * @param StatsService $statsService 統計サービス
     */
    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * 視聴統計を記録するAPI
     *
     * @param Request $request リクエスト
     * @param Response $response レスポンス
     * @param array $args 引数
     * @return Response
     */
    public function recordView(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $videoId = $data['id'] ?? null;
        
        if (!$videoId) {
            $response->getBody()->write(json_encode(['error' => 'Video ID is required']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
        
        $result = $this->statsService->recordView($videoId);
        
        $payload = json_encode(['ok' => $result]);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * クリック統計を記録するAPI
     *
     * @param Request $request リクエスト
     * @param Response $response レスポンス
     * @param array $args 引数
     * @return Response
     */
    public function recordClick(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $videoId = $data['id'] ?? null;
        
        if (!$videoId) {
            $response->getBody()->write(json_encode(['error' => 'Video ID is required']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
        
        $result = $this->statsService->recordClick($videoId);
        
        $payload = json_encode(['ok' => $result]);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
