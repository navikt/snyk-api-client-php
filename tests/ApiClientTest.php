<?php declare(strict_types=1);
namespace NAVIT\Snyk;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Middleware;

/**
 * @coversDefaultClass NAVIT\Snyk\ApiClient
 */
class ApiClientTest extends TestCase {
    /**
     * @param Response[] $responses A list of responses to return
     * @param array<array-key, array{response: Response, request: Request}> $history
     * @return HttpClient
     */
    private function getMockClient(array $responses, array &$history = []) : HttpClient {
        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push(Middleware::history($history));
        return new HttpClient(['handler' => $handler]);
    }

    /**
     * @covers ::createOrganization
     */
    public function testCanCreateOrganization() : void {
        $history = [];
        $httpClient = $this->getMockClient([new Response(200)], $history);
        (new ApiClient('abc-123', 'token', $httpClient))->createOrganization('org');
        $this->assertSame(['name' => 'org'], json_decode($history[0]['request']->getBody()->getContents(), true));
        $this->assertStringEndsWith('group/abc-123/org', (string) $history[0]['request']->getUri());
    }
}