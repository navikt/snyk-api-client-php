<?php declare(strict_types=1);
namespace NAVIT\Snyk\Models;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

/**
 * @coversDefaultClass NAVIT\Snyk\Models\Organization
 */
class OrganizationTest extends TestCase {
    /**
     * @covers ::fromResponse
     * @covers ::fromArray
     * @covers ::__construct
     * @covers ::getId
     * @covers ::getName
     * @covers ::getCreated
     */
    public function testCanCreateFromResponse() : void {
        $org = Organization::fromResponse($this->createConfiguredMock(ResponseInterface::class, [
            'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                'getContents' => '{"id":"abc-123","name":"org-name","created":"2020-06-03T13:28:56.784Z"}',
            ]),
        ]));
        $this->assertSame('abc-123', $org->getId(), 'Incorrect ID');
        $this->assertSame('org-name', $org->getName(), 'Incorrect name');
        $this->assertSame(1591190936, $org->getCreated()->getTimestamp(), 'Incorrect date');
    }

    /**
     * @covers ::fromArray
     * @psalm-suppress InvalidArgument
     */
    public function testThrowsExceptionOnMissingElement() : void {
        $this->expectExceptionObject(new InvalidArgumentException('Missing required data element: created'));
        /** @phpstan-ignore-next-line */
        Organization::fromArray(['id' => 'abc', 'name' => 'name']);
    }
}