<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\PersonList;

final class PersonService implements PersonServiceInterface
{
    public function loadPersons(): PersonList
    {
        return new PersonList();
    }
}
