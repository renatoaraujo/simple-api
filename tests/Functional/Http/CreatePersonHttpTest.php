<?php
declare(strict_types=1);

namespace App\Tests\Functional\Http;

use App\Tests\Functional\AbstractFunctionalTests;
use Symfony\Component\HttpFoundation\JsonResponse;

final class CreatePersonTest extends AbstractFunctionalTests
{
    public function testCreatePersonWithSuccess()
    {
        $client = $this->getAuthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('POST', '/api/person', [], [], $headers, \json_encode([
            'name' => 'John Doe',
            'email' => 'john.doe@localhost.test',
            'birth_date' => '10/10/1989'
        ]));

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_CREATED);
    }

    /**
     * @dataProvider invalidPersonPayloadProvider
     */
    public function testCreatePersonWithInvalidPayloadFails(array $payload)
    {
        $client = $this->getAuthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('POST', '/api/person', [], [], $headers, \json_encode($payload));

        $this->assertEquals($client->getResponse()->getStatusCode(), JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testCreatePersonWithoutJWTTokenFails()
    {
        $client = $this->getUnauthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('POST', '/api/person', [], [], $headers);

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_UNAUTHORIZED);
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
