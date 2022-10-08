<?php

namespace App\Tests\Controller;

use App\Entity\Partner;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PartnerControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PartnerRepository $repository;
    private string $path = '/partner/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Partner::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Partner index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'partner[name]' => 'Testing',
            'partner[phoneNumber]' => 'Testing',
            'partner[isActive]' => 'Testing',
            'partner[createdAt]' => 'Testing',
            'partner[updatedAt]' => 'Testing',
            'partner[user]' => 'Testing',
        ]);

        self::assertResponseRedirects('/partner/');

        self::assertCount($originalNumObjectsInRepository + 1, $this->repository->findAll());
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Partner();
        $fixture->setName('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setIsActive('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setUser('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Partner');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Partner();
        $fixture->setName('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setIsActive('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setUser('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'partner[name]' => 'Something New',
            'partner[phoneNumber]' => 'Something New',
            'partner[isActive]' => 'Something New',
            'partner[createdAt]' => 'Something New',
            'partner[updatedAt]' => 'Something New',
            'partner[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/partner/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getPhoneNumber());
        self::assertSame('Something New', $fixture[0]->getIsActive());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Partner();
        $fixture->setName('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setIsActive('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setUser('My Title');

        $this->repository->add($fixture, true);

        self::assertCount($originalNumObjectsInRepository + 1, $this->repository->findAll());

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertCount($originalNumObjectsInRepository, $this->repository->findAll());
        self::assertResponseRedirects('/partner/');
    }
}
