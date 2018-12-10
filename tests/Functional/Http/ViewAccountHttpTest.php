<?php
declare(strict_types=1);

namespace App\Tests\Functional\Http;

use App\Tests\Functional\AbstractFunctionalTests;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ViewAccountHttpTest extends AbstractFunctionalTests
{
    public function testViewAccountWithSuccess()
    {
        $client = $this->getAuthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('GET', '/api/account', [], [], $headers);

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_OK);
    }

    public function testViewAccountWithoutJWTTokenFails()
    {
        $client = $this->getUnauthenticatedClient();
        $headers = ['content-type' => 'application/json'];
        $client->request('GET', '/api/account', [], [], $headers);

        $response = $client->getResponse();
        $this->assertEquals($response->getStatusCode(), JsonResponse::HTTP_UNAUTHORIZED);
    }
}
