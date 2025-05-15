<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Service\VideoService;

/**
 * VideoController - 動画関連のAPIエンドポイントを処理するコントローラ
 */
class VideoController
{
    private $videoService;

    /**
     * コンストラクタ
     *
     * @param VideoService $videoService 動画サービス
     */
    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * ランダムな動画を取得するAPI
     *
     * @param Request $request リクエスト
     * @param Response $response レスポンス
     * @param array $args 引数
     * @return Response
     */
    public function getRandomVideos(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();
        $limit = isset($params['limit']) ? (int)$params['limit'] : 3;
        $genre = $params['genre'] ?? null;
        $tag = $params['tag'] ?? null;
        $actress = $params['actress'] ?? null;

        $videos = $this->videoService->getRandomVideos($limit, $genre, $tag, $actress);
        
        $payload = json_encode($videos);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * 動画を検索するAPI
     *
     * @param Request $request リクエスト
     * @param Response $response レスポンス
     * @param array $args 引数
     * @return Response
     */
    public function searchVideos(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();
        $actress = $params['actress'] ?? null;
        $tag = $params['tag'] ?? null;
        $genre = $params['genre'] ?? null;
        $q = $params['q'] ?? null;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
        $offset = isset($params['offset']) ? (int)$params['offset'] : 0;

        $result = $this->videoService->searchVideos($actress, $tag, $genre, $q, $limit, $offset);
        
        $payload = json_encode($result);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * マスターリスト（ジャンル、タグ、女優一覧）を取得するAPI
     *
     * @param Request $request リクエスト
     * @param Response $response レスポンス
     * @param array $args 引数
     * @return Response
     */
    public function getMasterlists(Request $request, Response $response, array $args): Response
    {
        $masterlists = $this->videoService->getMasterlists();
        
        $payload = json_encode($masterlists);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
