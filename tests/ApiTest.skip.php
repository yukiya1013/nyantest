<?php

declare(strict_types=1);
use Slim\Factory\AppFactory;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use DI\ContainerBuilder;

class ApiTest extends TestCase
{
    protected App $app;

    protected function setUp(): void
    {
        // Initialize the app
        $containerBuilder = new ContainerBuilder();
        // Add definitions
        // ... (if you have a container definitions file)
        $container = $containerBuilder->build();

        AppFactory::setContainer($container);
        $this->app = AppFactory::create();

        // Register routes
        $routes = require __DIR__ . '/../public/index.php'; // Assuming your routes are in this file
        $routes($this->app);

        // Load .env variables for testing
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../', '.env');
        $dotenv->load();
    }

    public function testGetRandomVideos(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/api/videos/random?limit=3');
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertJson($body);
        $data = json_decode($body, true);
        $this->assertIsArray($data);
        $this->assertLessThanOrEqual(3, count($data));
    }

    public function testGetRandomVideosWithGenre(): void
    {
        // Assuming 'サンプルジャンル1' exists from your masterlist API response
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/api/videos/random?limit=1&genre=' . urlencode('サンプルジャンル1'));
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertJson($body);
        $data = json_decode($body, true);
        if (!empty($data)) {
            $this->assertIsArray($data[0]['genre']);
            $this->assertContains('サンプルジャンル1', $data[0]['genre']);
        }
    }

    public function testGetMasterlists(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/api/masterlists');
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertJson($body);
        $data = json_decode($body, true);
        $this->assertArrayHasKey('genres', $data);
        $this->assertArrayHasKey('tags', $data);
        $this->assertArrayHasKey('actresses', $data);
        $this->assertIsArray($data['genres']);
        $this->assertIsArray($data['tags']);
        $this->assertIsArray($data['actresses']);
    }

    public function testPostStatsView(): void
    {
        // First, get a valid video ID
        $requestRandom = (new ServerRequestFactory())->createServerRequest('GET', '/api/videos/random?limit=1');
        $responseRandom = $this->app->handle($requestRandom);
        $bodyRandom = (string) $responseRandom->getBody();
        $dataRandom = json_decode($bodyRandom, true);
        
        if (empty($dataRandom) || !isset($dataRandom[0]['id'])) {
            $this->markTestSkipped('No video found to test stats view.');
            return;
        }
        $videoId = $dataRandom[0]['id'];

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/api/stats/view')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(['id' => $videoId]);
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertJson($body);
        $data = json_decode($body, true);
        $this->assertEquals(['ok' => true], $data);
    }

    public function testPostStatsClick(): void
    {
         // First, get a valid video ID
        $requestRandom = (new ServerRequestFactory())->createServerRequest('GET', '/api/videos/random?limit=1');
        $responseRandom = $this->app->handle($requestRandom);
        $bodyRandom = (string) $responseRandom->getBody();
        $dataRandom = json_decode($bodyRandom, true);

        if (empty($dataRandom) || !isset($dataRandom[0]['id'])) {
            $this->markTestSkipped('No video found to test stats click.');
            return;
        }
        $videoId = $dataRandom[0]['id'];

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/api/stats/click')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody(['id' => $videoId]);
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertJson($body);
        $data = json_decode($body, true);
        $this->assertEquals(['ok' => true], $data);
    }

    public function testGetRandomVideosInvalidLimit(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/api/videos/random?limit=abc');
        $response = $this->app->handle($request);
        // Slim's default behavior for type errors in query params might not be a 400 without specific middleware.
        // Depending on how your app handles this, it might return 200 with default limit or an error.
        // For now, let's assume it defaults or ignores, and check for a valid response structure.
        $this->assertEquals(200, $response->getStatusCode()); // Or 400 if you have validation middleware
        $body = (string) $response->getBody();
        $this->assertJson($body);
    }

    public function testPostStatsViewMissingId(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/api/stats/view')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([]); // Missing 'id'
        $response = $this->app->handle($request);
        $this->assertEquals(400, $response->getStatusCode()); // Assuming your app returns 400 for bad request
        $body = (string) $response->getBody();
        $this->assertJson($body);
        $data = json_decode($body, true);
        $this->assertArrayHasKey('error', $data);
    }
}

