<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

final class PersonService implements PersonServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadPersons(): array
    {
        return $this->entityManager->getRepository(Person::class)
            ->findAll();
    }

    public function loadPersonByUuid(UuidInterface $uuid): Person
    {
        return $this->entityManager->getRepository(Person::class)
            ->find($uuid);
    }

    public function createPersonFromPayload(array $payload): Person
    {
        $person = new Person();
        $person->setName($payload['name']);
        $person->setEmail($payload['email']);
        $person->setBirthDate(\DateTime::createFromFormat('d/m/Y', $payload['birth_date']));

        $this->entityManager->beginTransaction();
        $this->entityManager->persist($person);
        $this->entityManager->flush();
        $this->entityManager->commit();

        return $person;
    }

    public function updatePersonFromPayloadWithUuid(array $payload, UuidInterface $uuid): Person
    {
        $person = $this->entityManager->getRepository(Person::class)
            ->find($uuid);
        $person->setName($payload['name']);
        $person->setEmail($payload['email']);
        $person->setBirthDate(\DateTime::createFromFormat('d/m/Y', $payload['birth_date']));

        $this->entityManager->beginTransaction();
        $this->entityManager->persist($person);
        $this->entityManager->flush();
        $this->entityManager->commit();

        return $person;
    }

    public function deletePersonFromUuid(UuidInterface $uuid): void
    {
        $person = $this->entityManager->getRepository(Person::class)
            ->find($uuid);
        $this->entityManager->beginTransaction();
        $this->entityManager->remove($person);
        $this->entityManager->flush();
        $this->entityManager->commit();
    }
}
