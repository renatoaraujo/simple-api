<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Person;
use App\Model\PersonList;
use Ramsey\Uuid\UuidInterface;

final class PersonService implements PersonServiceInterface
{
    public function loadPersons(): PersonList
    {
        return new PersonList();
    }

    public function loadPersonByUuid(UuidInterface $uuid): Person
    {
        return new Person();
    }
}
