<?php

namespace App\Tests\Controller;

use App\Entity\Association;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AssociationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $associationRepository;
    private string $path = '/association/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->associationRepository = $this->manager->getRepository(Association::class);

        foreach ($this->associationRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Association index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'association[Nom]' => 'Testing',
            'association[Email]' => 'Testing',
            'association[Telephone]' => 'Testing',
            'association[Description]' => 'Testing',
            'association[Adresse]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->associationRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Association();
        $fixture->setNom('My Title');
        $fixture->setEmail('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setDescription('My Title');
        $fixture->setAdresse('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Association');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Association();
        $fixture->setNom('Value');
        $fixture->setEmail('Value');
        $fixture->setTelephone('Value');
        $fixture->setDescription('Value');
        $fixture->setAdresse('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'association[Nom]' => 'Something New',
            'association[Email]' => 'Something New',
            'association[Telephone]' => 'Something New',
            'association[Description]' => 'Something New',
            'association[Adresse]' => 'Something New',
        ]);

        self::assertResponseRedirects('/association/');

        $fixture = $this->associationRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getTelephone());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getAdresse());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Association();
        $fixture->setNom('Value');
        $fixture->setEmail('Value');
        $fixture->setTelephone('Value');
        $fixture->setDescription('Value');
        $fixture->setAdresse('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/association/');
        self::assertSame(0, $this->associationRepository->count([]));
    }
}
