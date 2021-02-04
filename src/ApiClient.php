<?php declare(strict_types=1);
namespace NAVIT\Snyk;

use GuzzleHttp\Client as HttpClient;

class ApiClient
{
    private string $groupId;
    private HttpClient $httpClient;

    /**
     * Class constructor
     *
     * @param string $groupId The group ID to use
     * @param string $token The API token
     * @param HttpClient $httpClient Pre-configured HTTP client to use
     */
    public function __construct(string $groupId, string $token, HttpClient $httpClient = null)
    {
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
     * @return array<string,mixed>
     */
    public function createOrganization(string $name): array
    {
        /** @var array<string,mixed> */
        return json_decode($this->httpClient->post(sprintf('group/%s/org', $this->groupId), [
            'json' => [
                'name' => $name,
            ],
        ])->getBody()->getContents(), true);
    }

    /**
     * Invite a user to an organization via email
     *
     * @param string $email
     * @param bool $isAdmin
     * @param string $orgId
     * @return void
     */
    public function inviteUserToOrganization(string $email, bool $isAdmin, string $orgId): void
    {
        $this->httpClient->post(sprintf('org/%s/invite', $orgId), ['json' => [
            'email'   => $email,
            'isAdmin' => $isAdmin,
        ]]);
    }
}
