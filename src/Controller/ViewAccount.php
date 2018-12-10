<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

final class ViewAccount
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var Security */
    private $security;

    public function __construct(SerializerInterface $serializer, Security $security)
    {
        $this->serializer = $serializer;
        $this->security = $security;
    }

    public function __invoke()
    {
        $user = $this->security->getUser();

        return new JsonResponse(
            $this->serializer->serialize($user, 'json'),
            JsonResponse::HTTP_OK,
            ['Content-Type' => 'application/json'],
            true
        );
    }
}
