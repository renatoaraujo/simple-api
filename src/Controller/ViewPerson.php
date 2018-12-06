<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\PersonServiceInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

final class ViewPerson
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var PersonServiceInterface */
    private $service;

    public function __construct(SerializerInterface $serializer, PersonServiceInterface $service)
    {
        $this->serializer = $serializer;
        $this->service = $service;
    }

    public function __invoke(Request $request)
    {
        $personId = $request->get('id');
        $uuid = Uuid::fromString($personId);

        $loadedPersons = $this->service->loadPersonByUuid($uuid);
        $data = $this->serializer->serialize($loadedPersons, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, ['Content-Type', 'application/json'], true);
    }
}
