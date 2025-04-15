<?php

namespace App\Tests\Controller;

use App\Entity\ProjetDon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProjetDonControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $projetDonRepository;
    private string $path = '/projet/don/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->projetDonRepository = $this->manager->getRepository(ProjetDon::class);

        foreach ($this->projetDonRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ProjetDon index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'projet_don[nom]' => 'Testing',
            'projet_don[montantRecu]' => 'Testing',
            'projet_don[objectif]' => 'Testing',
            'projet_don[date_debut]' => 'Testing',
            'projet_don[date_fin]' => 'Testing',
            'projet_don[id_association]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->projetDonRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new ProjetDon();
        $fixture->setNom('My Title');
        $fixture->setMontantRecu('My Title');
        $fixture->setObjectif('My Title');
        $fixture->setDate_debut('My Title');
        $fixture->setDate_fin('My Title');
        $fixture->setId_association('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ProjetDon');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new ProjetDon();
        $fixture->setNom('Value');
        $fixture->setMontantRecu('Value');
        $fixture->setObjectif('Value');
        $fixture->setDate_debut('Value');
        $fixture->setDate_fin('Value');
        $fixture->setId_association('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'projet_don[nom]' => 'Something New',
            'projet_don[montantRecu]' => 'Something New',
            'projet_don[objectif]' => 'Something New',
            'projet_don[date_debut]' => 'Something New',
            'projet_don[date_fin]' => 'Something New',
            'projet_don[id_association]' => 'Something New',
        ]);

        self::assertResponseRedirects('/projet/don/');

        $fixture = $this->projetDonRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getMontantRecu());
        self::assertSame('Something New', $fixture[0]->getObjectif());
        self::assertSame('Something New', $fixture[0]->getDate_debut());
        self::assertSame('Something New', $fixture[0]->getDate_fin());
        self::assertSame('Something New', $fixture[0]->getId_association());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new ProjetDon();
        $fixture->setNom('Value');
        $fixture->setMontantRecu('Value');
        $fixture->setObjectif('Value');
        $fixture->setDate_debut('Value');
        $fixture->setDate_fin('Value');
        $fixture->setId_association('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/projet/don/');
        self::assertSame(0, $this->projetDonRepository->count([]));
    }
}
