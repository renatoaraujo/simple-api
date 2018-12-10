<?php
declare(strict_types=1);

namespace App\Tests\Functional\Http;

use App\Tests\Functional\AbstractFunctionalTests;
use Symfony\Component\HttpFoundation\JsonResponse;

final class DeletePersonHttpTest extends AbstractFunctionalTests
{
    public function testDeletePersonWithSuccess()
    {
        $client = $this->getAuthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('DELETE', sprintf('/api/person/%s', $this->personId), [], [], $headers);

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_OK);
    }

    public function testDeletePersonWithoutJWTTokenFails()
    {
        $client = $this->getUnauthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('DELETE', sprintf('/api/person/%s', $this->personId), [], [], $headers);

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_UNAUTHORIZED);
    }
}
