<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Person;
use Ramsey\Uuid\UuidInterface;

interface PersonServiceInterface
{
    public function loadPersons(): array;

    public function loadPersonByUuid(UuidInterface $uuid): Person;

    public function createPersonFromPayload(array $payload): Person;

    public function updatePersonFromPayloadWithUuid(array $payload, UuidInterface $uuid): Person;

    public function deletePersonFromUuid(UuidInterface $uuid): void;
}
