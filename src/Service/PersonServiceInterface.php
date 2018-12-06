<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Person;
use App\Model\PersonList;
use Ramsey\Uuid\UuidInterface;

interface PersonServiceInterface
{
    public function loadPersons(): PersonList;

    public function loadPersonByUuid(UuidInterface $uuid): Person;

    public function createPersonFromPayload(array $payload): Person;

    public function updatePersonFromPayloadWithUuid(array $payload, UuidInterface $uuid): Person;

    public function deletePersonFromUuid(UuidInterface $uuid): void;
}
