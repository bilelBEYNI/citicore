<?php

namespace App\Tests\Controller;

use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReclamationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $reclamationRepository;
    private string $path = '/reclamation/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->reclamationRepository = $this->manager->getRepository(Reclamation::class);

        foreach ($this->reclamationRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reclamation index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'reclamation[Sujet]' => 'Testing',
            'reclamation[Description]' => 'Testing',
            'reclamation[Date_Creation]' => 'Testing',
            'reclamation[Date_Resolution]' => 'Testing',
            'reclamation[Type_Reclamation]' => 'Testing',
            'reclamation[Cin_Utilisateur]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->reclamationRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reclamation();
        $fixture->setSujet('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDate_Creation('My Title');
        $fixture->setDate_Resolution('My Title');
        $fixture->setType_Reclamation('My Title');
        $fixture->setCin_Utilisateur('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reclamation');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reclamation();
        $fixture->setSujet('Value');
        $fixture->setDescription('Value');
        $fixture->setDate_Creation('Value');
        $fixture->setDate_Resolution('Value');
        $fixture->setType_Reclamation('Value');
        $fixture->setCin_Utilisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'reclamation[Sujet]' => 'Something New',
            'reclamation[Description]' => 'Something New',
            'reclamation[Date_Creation]' => 'Something New',
            'reclamation[Date_Resolution]' => 'Something New',
            'reclamation[Type_Reclamation]' => 'Something New',
            'reclamation[Cin_Utilisateur]' => 'Something New',
        ]);

        self::assertResponseRedirects('/reclamation/');

        $fixture = $this->reclamationRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getSujet());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getDate_Creation());
        self::assertSame('Something New', $fixture[0]->getDate_Resolution());
        self::assertSame('Something New', $fixture[0]->getType_Reclamation());
        self::assertSame('Something New', $fixture[0]->getCin_Utilisateur());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reclamation();
        $fixture->setSujet('Value');
        $fixture->setDescription('Value');
        $fixture->setDate_Creation('Value');
        $fixture->setDate_Resolution('Value');
        $fixture->setType_Reclamation('Value');
        $fixture->setCin_Utilisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/reclamation/');
        self::assertSame(0, $this->reclamationRepository->count([]));
    }
}
