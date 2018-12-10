<?php
declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Entity\Person;
use App\Service\PersonService;
use App\Service\PersonServiceInterface;
use App\Tests\Functional\AbstractFunctionalTests;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class PersonServiceTest extends AbstractFunctionalTests
{
    /** @var string */
    protected $personId = 'e182b31c-6040-48e5-9958-fe82ebeabc9d';

    /** @var PersonServiceInterface */
    private $service;

    protected function setUp()
    {
        parent::setUp();

        $kernel = static::bootKernel();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $logger = $this->createMock(LoggerInterface::class);

        $this->service = new PersonService($entityManager, $logger);
    }

    /** @dataProvider validPersonPayloadProvider */
    public function testCreatePersonFromPayloadWithSuccess(array $payload)
    {
        $person = $this->service->createPersonFromPayload($payload);
        $this->assertInstanceOf(Person::class, $person);
        $this->assertInstanceOf(UuidInterface::class, $person->getId());
        $this->assertSame($payload['name'], $person->getName());
        $this->assertSame($payload['email'], $person->getEmail());
        $this->assertSame($payload['birth_date'], $person->getBirthDate()->format('d/m/Y'));
    }

    /**
     * @expectedException \Exception
     * @dataProvider validPersonPayloadProvider
     */
    public function testCreatePersonFromPayloadWithDuplicatedMailFails(array $payload)
    {
        $this->service->createPersonFromPayload($payload);
        $this->service->createPersonFromPayload($payload);
    }

    /** @dataProvider validPersonPayloadProvider */
    public function testUpdatePersonFromPayloadWithUuid(array $payload)
    {
        $person = $this->service->updatePersonFromPayloadWithUuid($payload, Uuid::fromString($this->personId));
        $this->assertInstanceOf(Person::class, $person);
        $this->assertSame($this->personId, $person->getId()->toString());
        $this->assertSame($payload['name'], $person->getName());
        $this->assertSame($payload['email'], $person->getEmail());
        $this->assertSame($payload['birth_date'], $person->getBirthDate()->format('d/m/Y'));
    }

    /** @expectedException \Exception */
    public function testUpdatePersonFromPayloadDuplicatedMailFails()
    {
        $this->service->createPersonFromPayload([
            'name' => 'John Doe',
            'email' => 'john.doe@localhost.test',
            'birth_date' => '10/10/1999',
        ]);

        $this->service->updatePersonFromPayloadWithUuid([
            'name' => 'John Doe',
            'email' => 'john.doe@localhost.test',
            'birth_date' => (new \DateTime())->format('d/m/Y')
        ], Uuid::fromString($this->personId));
    }

    /**
     * @expectedException \Exception
     */
    public function testUpdatePersonFromPayloadWithUnregisteredUuidFails()
    {
        $this->service->updatePersonFromPayloadWithUuid([
            'name' => 'John Doe',
            'email' => 'john.doe@localhost.test',
            'birth_date' => '10/10/1999',
        ], Uuid::fromString('e182b31c-6040-48e5-9958-fe82ebeabc9f'));
    }

    /** @dataProvider validPersonPayloadProvider */
    public function testLoadPersonListWithSuccess(array $payload)
    {
        $personList = $this->service->loadPersonList();
        $this->assertInstanceOf(Person::class, $personList[0]);
        $this->assertCount(1, $personList);

        $this->service->createPersonFromPayload($payload);
        $personListAfterInsert = $this->service->loadPersonList();
        $this->assertCount(2, $personListAfterInsert);
    }

    public function testLoadPersonByUuidWithSuccess()
    {
        $person = $this->service->loadPersonByUuid(Uuid::fromString($this->personId));
        $this->assertInstanceOf(Person::class, $person);
        $this->assertSame($this->personId, $person->getId()->toString());
    }

    /** @expectedException \Exception */
    public function testLoadPersonByUuidWithUnregisteredUuidFails()
    {
        $this->service->loadPersonByUuid(Uuid::fromString('e182b31c-6040-48e5-9958-fe82ebeabc9e'));
    }

    /** @expectedException \Exception */
    public function testDeletePersonByUuidWithUnregisteredUuidFails()
    {
        $this->service->deletePersonFromUuid(Uuid::fromString('e182b31c-6040-48e5-9958-fe82ebeabc9g'));
    }

    /**
     * @dataProvider validPersonPayloadProvider
     * @expectedException \Exception
     */
    public function testUpdatePersonFromPayloadWithDuplicatedEmailFails(array $payload)
    {
        $this->service->createPersonFromPayload($payload);
        $this->service->updatePersonFromPayloadWithUuid($payload, Uuid::fromString($this->personId));
    }

    public function validPersonPayloadProvider(): array
    {
        return [
            [
                [
                    'name' => 'John Doe',
                    'email' => 'john.doe@localhost.test',
                    'birth_date' => '10/10/1999',
                ],
            ],[
                [
                    'name' => 'John Doe',
                    'email' => 'john.doe@localhost.test',
                    'birth_date' => '10/10/1978',
                ],
            ],[
                [
                    'name' => 'John Doe',
                    'email' => 'john.doe@localhost.test',
                    'birth_date' => '10/10/1990',
                ],
            ],
        ];
    }
}
