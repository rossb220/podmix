<?php

namespace App\Test\Controller;

use App\Entity\EpisodeStrategy;
use App\Repository\EpisodeStrategyRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EpisodeStrategyControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EpisodeStrategyRepository $repository;
    private string $path = '/episode/strategy/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(EpisodeStrategy::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EpisodeStrategy index');

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
            'episode_strategy[title]' => 'Testing',
            'episode_strategy[length]' => 'Testing',
            'episode_strategy[expression]' => 'Testing',
            'episode_strategy[podcasts]' => 'Testing',
        ]);

        self::assertResponseRedirects('/episode/strategy/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new EpisodeStrategy();
        $fixture->setTitle('My Title');
        $fixture->setLength('My Title');
        $fixture->setExpression('My Title');
        $fixture->setPodcasts('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EpisodeStrategy');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new EpisodeStrategy();
        $fixture->setTitle('My Title');
        $fixture->setLength('My Title');
        $fixture->setExpression('My Title');
        $fixture->setPodcasts('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'episode_strategy[title]' => 'Something New',
            'episode_strategy[length]' => 'Something New',
            'episode_strategy[expression]' => 'Something New',
            'episode_strategy[podcasts]' => 'Something New',
        ]);

        self::assertResponseRedirects('/episode/strategy/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getLength());
        self::assertSame('Something New', $fixture[0]->getExpression());
        self::assertSame('Something New', $fixture[0]->getPodcasts());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new EpisodeStrategy();
        $fixture->setTitle('My Title');
        $fixture->setLength('My Title');
        $fixture->setExpression('My Title');
        $fixture->setPodcasts('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/episode/strategy/');
    }
}
