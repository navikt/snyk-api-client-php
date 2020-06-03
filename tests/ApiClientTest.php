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
     * @psalm-suppress ReferenceConstraintViolation
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
        $httpClient = $this->getMockClient([new Response(200, [], '{"id":"abc-123456","name":"name","created":"2020-06-03T13:28:56.784Z"}')], $history);
        $org = (new ApiClient('abc-123', 'token', $httpClient))->createOrganization('name');
        $this->assertSame(['name' => 'name'], json_decode($history[0]['request']->getBody()->getContents(), true));
        $this->assertStringEndsWith('group/abc-123/org', (string) $history[0]['request']->getUri());

        $this->assertSame('abc-123456', $org->getId(), 'Incorrect org ID');
        $this->assertSame('name', $org->getName(), 'Incorrect org name');
        $this->assertSame(1591190936, $org->getCreated()->getTimestamp(), 'Incorrect date');
    }
}