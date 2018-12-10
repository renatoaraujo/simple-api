<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Person;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class PersonService implements PersonServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function loadPersonList(): array
    {
        return $this->entityManager->getRepository(Person::class)->findAll();
    }

    public function loadPersonByUuid(UuidInterface $uuid): Person
    {
        if (null === $person = $this->entityManager->getRepository(Person::class)->find($uuid)) {
            throw new \Exception(
                sprintf('Person not found with id "%s"', $uuid->toString())
            );
        }

        return $person;
    }

    public function createPersonFromPayload(array $payload): Person
    {
        $person = new Person(Uuid::uuid4());
        $person->setName($payload['name']);
        $person->setEmail($payload['email']);
        $person->setBirthDate(\DateTime::createFromFormat('d/m/Y', $payload['birth_date']));

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($person);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (UniqueConstraintViolationException $exception) {
            $this->entityManager->rollback();
            $this->generateErrorLog($exception);
            throw new \Exception('Email is already registered.');
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            $this->generateErrorLog($exception);
            throw new \Exception('Failed to register person.');
        }

        return $person;
    }

    private function generateErrorLog(\Throwable $throwable)
    {
        $this->logger->error('Execution failed', [
            'code' => $throwable->getCode(),
            'message' => $throwable->getMessage(),
            'trace' => $throwable->getTraceAsString(),
        ]);
    }

    public function updatePersonFromPayloadWithUuid(array $payload, UuidInterface $uuid): Person
    {
        $person = $this->entityManager->getRepository(Person::class)->find($uuid);

        if (is_null($person)) {
            throw new \Exception(sprintf('Person not found with id "%s"', $uuid->toString()));
        }

        $person->setName($payload['name']);
        $person->setEmail($payload['email']);
        $person->setBirthDate(\DateTime::createFromFormat('d/m/Y', $payload['birth_date']));

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($person);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (UniqueConstraintViolationException $exception) {
            $this->entityManager->rollback();
            $this->generateErrorLog($exception);
            throw new \Exception('Email is already registered.');
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            $this->generateErrorLog($exception);
            throw new \Exception('Failed to update person.');
        }

        return $person;
    }

    public function deletePersonFromUuid(UuidInterface $uuid): void
    {
        $person = $this->entityManager->getRepository(Person::class)->find($uuid);

        if (is_null($person)) {
            throw new \Exception(sprintf('Person not found with id "%s"', $uuid->toString()));
        }

        $this->entityManager->beginTransaction();
        $this->entityManager->remove($person);
        $this->entityManager->flush();
        $this->entityManager->commit();
    }
}
