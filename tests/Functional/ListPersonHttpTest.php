<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ListPersonHttpTest extends WebTestCase
{
    /** @var Client */
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testListPersonWithSuccess()
    {
        $headers = ['content-type' => 'application/json'];
        $payload = [];
        $this->client->request('GET', '/person', [], [], $headers, \json_encode($payload));

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_OK);
    }
}
