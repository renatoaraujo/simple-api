<?php
declare(strict_types=1);

namespace App\Model;

final class PersonList implements \JsonSerializable
{
    /** @var Person[] */
    private $persons = [];

    public function addPerson(Person $person)
    {
        $this->persons[] = $person;
    }

    public static function fromArrayOfPerson(Person ...$person): PersonList
    {
        $instance = new self();
        $instance->persons = $person;
        return $instance;
    }

    public function jsonSerialize()
    {
        return $this->persons;
    }
}
