<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Exception\ApiException;

/**
 * ErrorHandlerMiddleware - エラー処理を行うミドルウェア
 */
class ErrorHandlerMiddleware implements MiddlewareInterface
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
        try {
            return $handler->handle($request);
        } catch (ApiException $e) {
            $statusCode = $e->getStatusCode();
            $errorMessage = $e->getMessage();
            
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'error' => true,
                'message' => $errorMessage
            ]));
            
            return $response
                ->withStatus($statusCode)
                ->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'error' => true,
                'message' => 'Internal Server Error'
            ]));
            
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}
