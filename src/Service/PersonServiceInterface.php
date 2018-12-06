<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\PersonList;

interface PersonServiceInterface
{
    public function loadPersons(): PersonList;
}
