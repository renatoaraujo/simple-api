<?php
declare(strict_types=1);

namespace App\DataFixture;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

final class PersonFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $uuid = Uuid::fromString('e182b31c-6040-48e5-9958-fe82ebeabc9d');
        $person = new Person($uuid);
        $person->setName('John Doe');
        $person->setEmail('john.doe@localhost.local');
        $person->setBirthDate(\DateTimeImmutable::createFromFormat('d/m/Y', '20/01/1992'));

        $manager->persist($person);
        $manager->flush();
    }
}
