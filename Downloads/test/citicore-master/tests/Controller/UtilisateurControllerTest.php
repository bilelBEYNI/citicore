<?php

namespace App\Tests\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UtilisateurControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $utilisateurRepository;
    private string $path = '/utilisateur/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->utilisateurRepository = $this->manager->getRepository(Utilisateur::class);

        foreach ($this->utilisateurRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Utilisateur index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'utilisateur[Nom]' => 'Testing',
            'utilisateur[Prenom]' => 'Testing',
            'utilisateur[Num_Tel]' => 'Testing',
            'utilisateur[Email]' => 'Testing',
            'utilisateur[Genre]' => 'Testing',
            'utilisateur[Photo_Utilisateur]' => 'Testing',
            'utilisateur[Mot_De_Passe]' => 'Testing',
            'utilisateur[Role]' => 'Testing',
            'utilisateur[Token]' => 'Testing',
            'utilisateur[failed_attempts]' => 'Testing',
            'utilisateur[ban_time]' => 'Testing',
            'utilisateur[login_failures]' => 'Testing',
            'utilisateur[is_banned]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->utilisateurRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Utilisateur();
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setNum_Tel('My Title');
        $fixture->setEmail('My Title');
        $fixture->setGenre('My Title');
        $fixture->setPhoto_Utilisateur('My Title');
        $fixture->setMot_De_Passe('My Title');
        $fixture->setRole('My Title');
        $fixture->setToken('My Title');
        $fixture->setFailed_attempts('My Title');
        $fixture->setBan_time('My Title');
        $fixture->setLogin_failures('My Title');
        $fixture->setIs_banned('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Utilisateur');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Utilisateur();
        $fixture->setNom('Value');
        $fixture->setPrenom('Value');
        $fixture->setNum_Tel('Value');
        $fixture->setEmail('Value');
        $fixture->setGenre('Value');
        $fixture->setPhoto_Utilisateur('Value');
        $fixture->setMot_De_Passe('Value');
        $fixture->setRole('Value');
        $fixture->setToken('Value');
        $fixture->setFailed_attempts('Value');
        $fixture->setBan_time('Value');
        $fixture->setLogin_failures('Value');
        $fixture->setIs_banned('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'utilisateur[Nom]' => 'Something New',
            'utilisateur[Prenom]' => 'Something New',
            'utilisateur[Num_Tel]' => 'Something New',
            'utilisateur[Email]' => 'Something New',
            'utilisateur[Genre]' => 'Something New',
            'utilisateur[Photo_Utilisateur]' => 'Something New',
            'utilisateur[Mot_De_Passe]' => 'Something New',
            'utilisateur[Role]' => 'Something New',
            'utilisateur[Token]' => 'Something New',
            'utilisateur[failed_attempts]' => 'Something New',
            'utilisateur[ban_time]' => 'Something New',
            'utilisateur[login_failures]' => 'Something New',
            'utilisateur[is_banned]' => 'Something New',
        ]);

        self::assertResponseRedirects('/utilisateur/');

        $fixture = $this->utilisateurRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getPrenom());
        self::assertSame('Something New', $fixture[0]->getNum_Tel());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getGenre());
        self::assertSame('Something New', $fixture[0]->getPhoto_Utilisateur());
        self::assertSame('Something New', $fixture[0]->getMot_De_Passe());
        self::assertSame('Something New', $fixture[0]->getRole());
        self::assertSame('Something New', $fixture[0]->getToken());
        self::assertSame('Something New', $fixture[0]->getFailed_attempts());
        self::assertSame('Something New', $fixture[0]->getBan_time());
        self::assertSame('Something New', $fixture[0]->getLogin_failures());
        self::assertSame('Something New', $fixture[0]->getIs_banned());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Utilisateur();
        $fixture->setNom('Value');
        $fixture->setPrenom('Value');
        $fixture->setNum_Tel('Value');
        $fixture->setEmail('Value');
        $fixture->setGenre('Value');
        $fixture->setPhoto_Utilisateur('Value');
        $fixture->setMot_De_Passe('Value');
        $fixture->setRole('Value');
        $fixture->setToken('Value');
        $fixture->setFailed_attempts('Value');
        $fixture->setBan_time('Value');
        $fixture->setLogin_failures('Value');
        $fixture->setIs_banned('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/utilisateur/');
        self::assertSame(0, $this->utilisateurRepository->count([]));
    }
}
