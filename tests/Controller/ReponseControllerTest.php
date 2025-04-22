<?php

namespace App\Tests\Controller;

use App\Entity\Reponse;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReponseControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $reponseRepository;
    private string $path = '/reponse/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->reponseRepository = $this->manager->getRepository(Reponse::class);

        foreach ($this->reponseRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reponse index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'reponse[Contenu]' => 'Testing',
            'reponse[Date_Reponse]' => 'Testing',
            'reponse[Statut]' => 'Testing',
            'reponse[reclamation]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->reponseRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reponse();
        $fixture->setContenu('My Title');
        $fixture->setDate_Reponse('My Title');
        $fixture->setStatut('My Title');
        $fixture->setReclamation('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reponse');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reponse();
        $fixture->setContenu('Value');
        $fixture->setDate_Reponse('Value');
        $fixture->setStatut('Value');
        $fixture->setReclamation('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'reponse[Contenu]' => 'Something New',
            'reponse[Date_Reponse]' => 'Something New',
            'reponse[Statut]' => 'Something New',
            'reponse[reclamation]' => 'Something New',
        ]);

        self::assertResponseRedirects('/reponse/');

        $fixture = $this->reponseRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getContenu());
        self::assertSame('Something New', $fixture[0]->getDate_Reponse());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getReclamation());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reponse();
        $fixture->setContenu('Value');
        $fixture->setDate_Reponse('Value');
        $fixture->setStatut('Value');
        $fixture->setReclamation('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/reponse/');
        self::assertSame(0, $this->reponseRepository->count([]));
    }
}
