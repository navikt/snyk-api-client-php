<?php declare(strict_types=1);
namespace NAVIT\Snyk;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;
use InvalidArgumentException;
use RuntimeException;

class ApiClient {
    /**
     * @var string
     */
    private $groupId;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Class constructor
     *
     * @param string $groupId The group ID to use
     * @param string $token The API token
     * @param HttpClient $httpClient Pre-configured HTTP client to use
     */
    public function __construct(string $groupId, string $token, HttpClient $httpClient = null) {
        $this->groupId    = $groupId;
        $this->httpClient = $httpClient ?: new HttpClient([
            'base_uri' => 'https://snyk.io/api/v1/',
            'headers' => [
                'Authorization' => sprintf('token %s', $token),
            ],
        ]);
    }

    /**
     * Create an organization
     *
     * @param string $name Name of the organization
     * @return void
     */
    public function createOrganization(string $name) : void {
        $this->httpClient->post(sprintf('group/%s/org', $this->groupId), [
            'json' => [
                'name' => $name,
            ],
        ]);
    }
}