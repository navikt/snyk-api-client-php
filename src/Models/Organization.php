<?php declare(strict_types=1);
namespace NAVIT\Snyk\Models;

use Psr\Http\Message\ResponseInterface;
use DateTime;
use InvalidArgumentException;

class Organization {
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var DateTime */
    private $created;

    public function __construct(string $id, string $name, string $created) {
        $this->id = $id;
        $this->name = $name;
        $this->created = new DateTime($created);
    }

    public function getId() : string {
        return $this->id;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getCreated() : DateTime {
        return $this->created;
    }

    /**
     * @param array{id: string, name: string, created: string} $data
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $data) : self {
        foreach (['id', 'name', 'created'] as $required) {
            if (empty($data[$required])) {
                throw new InvalidArgumentException(sprintf('Missing required data element: %s', $required));
            }
        }

        return new self(
            $data['id'],
            $data['name'],
            $data['created']
        );
    }

    public static function fromResponse(ResponseInterface $response) : self {
        /** @var array{id: string, name: string, created: string} */
        $data = json_decode($response->getBody()->getContents(), true);

        return self::fromArray($data);
    }
}