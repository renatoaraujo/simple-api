<?php
declare(strict_types=1);

namespace App\Tests\Functional\Http;

use App\Tests\Functional\AbstractFunctionalTests;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UpdatePersonHttpTest extends AbstractFunctionalTests
{
    public function testUpdatePersonWithSuccess()
    {
        $client = $this->getAuthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('PUT', sprintf('/api/person/%s', $this->personId), [], [], $headers, \json_encode([
            'name' => 'John Doe',
            'email' => 'john.doe@localhost.test',
            'birth_date' => '10/10/1989',
        ]));

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_OK);
    }

    public function testUpdatePersonWithoutJWTTokenFails()
    {
        $client = $this->getUnauthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('PUT', sprintf('/api/person/%s', $this->personId), [], [], $headers);

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @dataProvider invalidPersonPayloadProvider
     */
    public function testUpdatePersonWithInvalidPayloadFail(array $payload)
    {
        $client = $this->getAuthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('PUT', sprintf('/api/person/%s', $this->personId), [], [], $headers, \json_encode($payload));

        $this->assertEquals($client->getResponse()->getStatusCode(), JsonResponse::HTTP_BAD_REQUEST);
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
