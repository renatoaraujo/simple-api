<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\PersonServiceInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

final class UpdatePerson
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
        $payload = \json_decode($request->getContent(), true);
        $personId = $request->get('id');
        $uuid = Uuid::fromString($personId);

        if (!array_key_exists('name', $payload) || empty($payload['name'])) {
            throw new BadRequestHttpException('Missing person name.');
        }

        if (!array_key_exists('email', $payload) || empty($payload['email'])) {
            throw new BadRequestHttpException('Missing person email.');
        }

        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException('Invalid email provided.');
        }

        if (!array_key_exists('birth_date', $payload) || empty($payload['birth_date'])) {
            throw new BadRequestHttpException('Missing person birth date.');
        }

        $person = $this->service->updatePersonFromPayloadWithUuid($payload, $uuid);
        $data = $this->serializer->serialize($person, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
