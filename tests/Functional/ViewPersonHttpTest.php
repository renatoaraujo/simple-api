<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ViewPersonHttpTest extends WebTestCase
{
    /** @var Client */
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testViewPersonWithSuccess()
    {
        $randomUuid = Uuid::uuid4();
        $headers = ['content-type' => 'application/json'];

        $this->client->request(
            'GET',
            sprintf('/person/%s', $randomUuid->toString()),
            [],
            [],
            $headers
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_OK);
    }
}
