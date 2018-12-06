<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class CreatePersonHttpTest extends WebTestCase
{
    /** @var Client */
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testCreatePersonWithSuccess()
    {
        $headers = ['content-type' => 'application/json'];
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@localhost.test',
            'birth_date' => '10/10/1989'
        ];
        $this->client->request('POST', '/person', [], [], $headers, \json_encode($payload));

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_CREATED);
    }

    /**
     * @dataProvider invalidPersonPayloadProvider
     */
    public function testCreatePersonWithInvalidPayloadFail(array $payload)
    {
        $headers = ['content-type' => 'application/json'];
        $this->client->request('POST', '/person', [], [], $headers, \json_encode($payload));

        $this->assertEquals($this->client->getResponse()->getStatusCode(), JsonResponse::HTTP_BAD_REQUEST);
    }

    public function invalidPersonPayloadProvider(): array
    {
        return [
            [['name' => '', 'email' => 'email@email.com', 'birth_date' => '10/10/1989']],
            [['name' => 'Joanna', 'email' => '', 'birth_date' => '10/10/1989']],
            [['name' => 'Someone', 'email' => 'someone@mail.com', 'birth_date' => '']],
            [['name' => 'Someone', 'email' => 'invalid_email', 'birth_date' => '10/10/1989']],
            [['name' => 'Someone', 'email' => 'someone@mail.com']],
            [['name' => 'Someone', 'birth_date' => '']],
            [['email' => 'someone@mail.com', 'birth_date' => '']],
        ];
    }
}
