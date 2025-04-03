<?php

namespace App\Tests\Controller;

use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FeedbackControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $feedbackRepository;
    private string $path = '/feedback/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->feedbackRepository = $this->manager->getRepository(Feedback::class);

        foreach ($this->feedbackRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Feedback index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'feedback[contenu]' => 'Testing',
            'feedback[date_creation]' => 'Testing',
            'feedback[participant]' => 'Testing',
            'feedback[organisateur]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->feedbackRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Feedback();
        $fixture->setContenu('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setParticipant('My Title');
        $fixture->setOrganisateur('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Feedback');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Feedback();
        $fixture->setContenu('Value');
        $fixture->setDate_creation('Value');
        $fixture->setParticipant('Value');
        $fixture->setOrganisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'feedback[contenu]' => 'Something New',
            'feedback[date_creation]' => 'Something New',
            'feedback[participant]' => 'Something New',
            'feedback[organisateur]' => 'Something New',
        ]);

        self::assertResponseRedirects('/feedback/');

        $fixture = $this->feedbackRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getContenu());
        self::assertSame('Something New', $fixture[0]->getDate_creation());
        self::assertSame('Something New', $fixture[0]->getParticipant());
        self::assertSame('Something New', $fixture[0]->getOrganisateur());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Feedback();
        $fixture->setContenu('Value');
        $fixture->setDate_creation('Value');
        $fixture->setParticipant('Value');
        $fixture->setOrganisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/feedback/');
        self::assertSame(0, $this->feedbackRepository->count([]));
    }
}
