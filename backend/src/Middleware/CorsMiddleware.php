<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * CorsMiddleware - CORSヘッダーを設定するミドルウェア
 */
class CorsMiddleware implements MiddlewareInterface
{
    /**
     * ミドルウェア処理
     *
     * @param Request $request リクエスト
     * @param RequestHandler $handler リクエストハンドラ
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Cache-Control', 'max-age=86400') // 24時間のキャッシュ
            ->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-Content-Type-Options', 'nosniff');
    }
}
