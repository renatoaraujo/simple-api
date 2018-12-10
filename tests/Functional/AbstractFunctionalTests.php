<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixture\PersonFixture;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Liip\FunctionalTestBundle\Test\WebTestCase;

abstract class AbstractFunctionalTests extends WebTestCase
{
    /** @var string */
    protected $personId = 'e182b31c-6040-48e5-9958-fe82ebeabc9d';

    protected function getAuthenticatedClient(): Client
    {
        $client = static::createClient();
        $client->request('POST', '/token', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], \json_encode([
            'username' => 'test@localhost.local',
            'password' => 'test',
        ]));

        if ($client->getResponse()->getStatusCode() === JsonResponse::HTTP_UNAUTHORIZED) {
            $this->markTestSkipped('Tests requires valid authentication!');
        }

        $data = \json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    protected function getUnauthenticatedClient(): Client
    {
        return static::createClient();
    }

    protected function setUp()
    {
        $this->loadFixtures([
            PersonFixture::class,
        ]);
    }

    protected function tearDown()
    {
        $kernel = static::bootKernel();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();
    }
}
