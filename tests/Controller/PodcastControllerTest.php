<?php

namespace App\Test\Controller;

use App\Entity\Podcast;
use App\Repository\PodcastRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PodcastControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PodcastRepository $repository;
    private string $path = '/podcast/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Podcast::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Podcast index');

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
            'podcast[title]' => 'Testing',
            'podcast[url]' => 'Testing',
            'podcast[disabledAt]' => 'Testing',
            'podcast[customTitle]' => 'Testing',
            'podcast[description]' => 'Testing',
            'podcast[pubDate]' => 'Testing',
        ]);

        self::assertResponseRedirects('/podcast/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Podcast();
        $fixture->setTitle('My Title');
        $fixture->setUrl('My Title');
        $fixture->setDisabledAt('My Title');
        $fixture->setCustomTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPubDate('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Podcast');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Podcast();
        $fixture->setTitle('My Title');
        $fixture->setUrl('My Title');
        $fixture->setDisabledAt('My Title');
        $fixture->setCustomTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPubDate('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'podcast[title]' => 'Something New',
            'podcast[url]' => 'Something New',
            'podcast[disabledAt]' => 'Something New',
            'podcast[customTitle]' => 'Something New',
            'podcast[description]' => 'Something New',
            'podcast[pubDate]' => 'Something New',
        ]);

        self::assertResponseRedirects('/podcast/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getUrl());
        self::assertSame('Something New', $fixture[0]->getDisabledAt());
        self::assertSame('Something New', $fixture[0]->getCustomTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getPubDate());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Podcast();
        $fixture->setTitle('My Title');
        $fixture->setUrl('My Title');
        $fixture->setDisabledAt('My Title');
        $fixture->setCustomTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPubDate('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/podcast/');
    }
}
