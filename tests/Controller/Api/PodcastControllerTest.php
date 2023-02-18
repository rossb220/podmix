<?php

namespace App\Tests\Controller\Api;

use App\Entity\Podcast;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PodcastControllerTest extends WebTestCase
{

    public function testCreatePodcast(): Podcast
    {
        $client = static::createClient();
        $originalPodcast = new Podcast();
        $faker = Factory::create();
        $originalPodcast->setCustomTitle($faker->name());
        $originalPodcast->setUrl($faker->url());

        /** @var SerializerInterface $serializer */
        $serializer = $client->getContainer()->get(SerializerInterface::class);
        $json = $serializer->serialize($originalPodcast, 'json', [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        $client->request(
            Request::METHOD_POST,
            '/api/podcast',
            [],
            [],
            [
                'content-type' => 'application/json'
            ],
            $json,
        );

        $this->assertResponseIsSuccessful();

        $responseBody = $client->getResponse()->getContent();

        /** @var Podcast $podcast */
        $podcast = $serializer->deserialize($responseBody, Podcast::class, 'json');

        $this->assertEquals($originalPodcast->getUrl(), $podcast->getUrl());
        $this->assertNotNull($podcast->getId());

        return $podcast;
    }

    /**
     * @depends testCreatePodcast
     * @return void
     */
    public function testGetPodcast(Podcast $podcast): Podcast
    {
        $client = static::createClient();
        /** @var SerializerInterface $serializer */
        $serializer = $client->getContainer()->get(SerializerInterface::class);

        $podcastId = $podcast->getId();

        $client->request(
            Request::METHOD_GET,
            "/api/podcast/{$podcastId}"
        );

        $this->assertResponseIsSuccessful();

        $responseBody = $client->getResponse()->getContent();
        /** @var Podcast $podcast */
        $responsePodcast = $serializer->deserialize($responseBody, Podcast::class, 'json');

        $this->assertEquals($podcast->getId(), $responsePodcast->getId());
        $this->assertEquals($podcast->getUrl(), $responsePodcast->getUrl());

        return $responsePodcast;
    }

    /**
     * @depends testCreatePodcast
     * @return void
     */
    public function testCannotCreatePodcastsWithDuplicateUrls(Podcast $podcast): void
    {
        $client = static::createClient();

        $duplicatedPodcast = new Podcast();
        $duplicatedPodcast->setUrl($podcast->getUrl());

        /** @var SerializerInterface $serializer */
        $serializer = $client->getContainer()->get(SerializerInterface::class);
        $json = $serializer->serialize(
            $duplicatedPodcast,
            'json',
            [
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true
            ]
        );

        $client->request(
            Request::METHOD_POST,
            '/api/podcast',
            [],
            [],
            [
                'content-type' => 'application/json'
            ],
            $json,
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }


    /**
     * @depends testGetPodcast
     *
     * @return void
     */
    public function testDeletePodcast(Podcast $podcast)
    {
        $client = static::createClient();

        $client->request(Request::METHOD_DELETE, sprintf("/api/podcast/%s", $podcast->getId()));

        $this->assertResponseIsSuccessful();

        /** @var SerializerInterface $serializer */
        $serializer = $client->getContainer()->get(SerializerInterface::class);

        $responseBody = $client->getResponse()->getContent();

        /** @var Podcast $podcast */
        $responsePodcast = $serializer->deserialize($responseBody, Podcast::class, 'json');

        $this->assertNotNull($responsePodcast->getDisabledAt());
    }
}
