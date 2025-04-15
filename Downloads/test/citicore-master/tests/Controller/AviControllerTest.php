<?php

namespace App\Tests\Controller;

use App\Entity\Avi;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AviControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $aviRepository;
    private string $path = '/avi/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->aviRepository = $this->manager->getRepository(Avi::class);

        foreach ($this->aviRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Avi index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'avi[Utilisateur_id]' => 'Testing',
            'avi[commentaire]' => 'Testing',
            'avi[date_avis]' => 'Testing',
            'avi[Demande_id]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->aviRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Avi();
        $fixture->setUtilisateur_id('My Title');
        $fixture->setCommentaire('My Title');
        $fixture->setDate_avis('My Title');
        $fixture->setDemande_id('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Avi');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Avi();
        $fixture->setUtilisateur_id('Value');
        $fixture->setCommentaire('Value');
        $fixture->setDate_avis('Value');
        $fixture->setDemande_id('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'avi[Utilisateur_id]' => 'Something New',
            'avi[commentaire]' => 'Something New',
            'avi[date_avis]' => 'Something New',
            'avi[Demande_id]' => 'Something New',
        ]);

        self::assertResponseRedirects('/avi/');

        $fixture = $this->aviRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getUtilisateur_id());
        self::assertSame('Something New', $fixture[0]->getCommentaire());
        self::assertSame('Something New', $fixture[0]->getDate_avis());
        self::assertSame('Something New', $fixture[0]->getDemande_id());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Avi();
        $fixture->setUtilisateur_id('Value');
        $fixture->setCommentaire('Value');
        $fixture->setDate_avis('Value');
        $fixture->setDemande_id('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/avi/');
        self::assertSame(0, $this->aviRepository->count([]));
    }
}
