<?php declare(strict_types=1);
namespace NAVIT\Snyk;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\{
    Client as HttpClient,
    Handler\MockHandler,
    HandlerStack,
    Psr7\Response,
    Psr7\Request,
    Middleware,
};
use NAVIT\Snyk\Models\Organization;

/**
 * @coversDefaultClass NAVIT\Snyk\ApiClient
 */
class ApiClientTest extends TestCase {
    /**
     * @param array<int,Response> $responses A list of responses to return
     * @param array<int,array{response:Response,request:Request}> $history
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
        $httpClient = $this->getMockClient([new Response(200, [], '{"id":"abc-123456","name":"name","created":"2020-06-03T13:28:56.784Z"}')], $history);
        $org = (new ApiClient('abc-123', 'token', $httpClient))->createOrganization('name');
        $this->assertSame(['name' => 'name'], json_decode($history[0]['request']->getBody()->getContents(), true));
        $this->assertStringEndsWith('group/abc-123/org', (string) $history[0]['request']->getUri());

        $this->assertSame('abc-123456', $org->getId(), 'Incorrect org ID');
        $this->assertSame('name', $org->getName(), 'Incorrect org name');
        $this->assertSame(1591190936, $org->getCreated()->getTimestamp(), 'Incorrect date');
    }

    /**
     * @return array<int, array{0: string, 1: bool}>
     */
    public function getInvites() : array {
        return [
            ['user1@nav.no', true],
            ['user2@nav.no', false],
        ];
    }

    /**
     * @dataProvider getInvites
     * @covers ::inviteUserToOrganization
     */
    public function testCanInviteUserToOrg(string $email, bool $isAdmin) : void {
        $org = $this->createConfiguredMock(Organization::class, [
            'getId' => 'org-id',
        ]);
        $history = [];
        $httpClient = $this->getMockClient([new Response(200)], $history);
        (new ApiClient('abc-123', 'token', $httpClient))->inviteUserToOrganization($email, $isAdmin, $org);
        $this->assertSame(['email' => $email, 'isAdmin' => $isAdmin], json_decode($history[0]['request']->getBody()->getContents(), true));
        $this->assertStringEndsWith('org/org-id/invite', (string) $history[0]['request']->getUri());
    }
}