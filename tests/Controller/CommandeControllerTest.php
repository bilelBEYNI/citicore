<?php

namespace App\Tests\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CommandeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $commandeRepository;
    private string $path = '/commande/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->commandeRepository = $this->manager->getRepository(Commande::class);

        foreach ($this->commandeRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Commande index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'commande[date_commande]' => 'Testing',
            'commande[status]' => 'Testing',
            'commande[produits]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->commandeRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commande();
        $fixture->setDate_commande('My Title');
        $fixture->setStatus('My Title');
        $fixture->setProduits('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Commande');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commande();
        $fixture->setDate_commande('Value');
        $fixture->setStatus('Value');
        $fixture->setProduits('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'commande[date_commande]' => 'Something New',
            'commande[status]' => 'Something New',
            'commande[produits]' => 'Something New',
        ]);

        self::assertResponseRedirects('/commande/');

        $fixture = $this->commandeRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDate_commande());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getProduits());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commande();
        $fixture->setDate_commande('Value');
        $fixture->setStatus('Value');
        $fixture->setProduits('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/commande/');
        self::assertSame(0, $this->commandeRepository->count([]));
    }
}
