<?php
declare(strict_types=1);

namespace App\Tests\Functional\Http;

use App\Tests\Functional\AbstractFunctionalTests;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ViewPersonHttpTest extends AbstractFunctionalTests
{
    public function testViewPersonWithSuccess()
    {
        $client = $this->getAuthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('GET', sprintf('/api/person/%s', $this->personId), [], [], $headers);

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_OK);
    }

    public function testViewPersonWithoutJWTTokenFails()
    {
        $client = $this->getUnauthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('GET', '/api/person', [], [], $headers);

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_UNAUTHORIZED);
    }
}
