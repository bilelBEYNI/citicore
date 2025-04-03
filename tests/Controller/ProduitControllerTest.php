<?php

namespace App\Tests\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProduitControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $produitRepository;
    private string $path = '/produit/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->produitRepository = $this->manager->getRepository(Produit::class);

        foreach ($this->produitRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Produit index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'produit[nom]' => 'Testing',
            'produit[description]' => 'Testing',
            'produit[prix]' => 'Testing',
            'produit[vendeur_id]' => 'Testing',
            'produit[date_ajout]' => 'Testing',
            'produit[photo]' => 'Testing',
            'produit[commandes]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->produitRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Produit();
        $fixture->setNom('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPrix('My Title');
        $fixture->setVendeur_id('My Title');
        $fixture->setDate_ajout('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setCommandes('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Produit');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Produit();
        $fixture->setNom('Value');
        $fixture->setDescription('Value');
        $fixture->setPrix('Value');
        $fixture->setVendeur_id('Value');
        $fixture->setDate_ajout('Value');
        $fixture->setPhoto('Value');
        $fixture->setCommandes('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'produit[nom]' => 'Something New',
            'produit[description]' => 'Something New',
            'produit[prix]' => 'Something New',
            'produit[vendeur_id]' => 'Something New',
            'produit[date_ajout]' => 'Something New',
            'produit[photo]' => 'Something New',
            'produit[commandes]' => 'Something New',
        ]);

        self::assertResponseRedirects('/produit/');

        $fixture = $this->produitRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getPrix());
        self::assertSame('Something New', $fixture[0]->getVendeur_id());
        self::assertSame('Something New', $fixture[0]->getDate_ajout());
        self::assertSame('Something New', $fixture[0]->getPhoto());
        self::assertSame('Something New', $fixture[0]->getCommandes());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Produit();
        $fixture->setNom('Value');
        $fixture->setDescription('Value');
        $fixture->setPrix('Value');
        $fixture->setVendeur_id('Value');
        $fixture->setDate_ajout('Value');
        $fixture->setPhoto('Value');
        $fixture->setCommandes('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/produit/');
        self::assertSame(0, $this->produitRepository->count([]));
    }
}
