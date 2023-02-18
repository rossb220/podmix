<?php

namespace App\Controller\Api;

use App\Entity\Podcast;
use App\Message\UpdateAllPodcastsMessage;
use App\Message\UpdateSinglePodcastMessage;
use App\Repository\PodcastRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class PodcastController extends AbstractController
{
    public function __construct(
        private readonly PodcastRepository $repository,
        private readonly SerializerInterface $serializer,
        private readonly MessageBusInterface $bus
    ) {
    }
    #[Route('/podcast', name: 'api_podcast_create', methods: ["POST"])]
    public function create(Request $request): Response
    {
        $requestBody = $request->getContent();

        if ($requestBody === '') {
            throw new BadRequestException('no body');
        }

        /* @var Podcast $podcast */
        $podcast = $this->serializer->deserialize($requestBody, Podcast::class, 'json');

        $duplicatePodcast = $this->repository->findOneBy(['url' => $podcast->getUrl()]);
        if ($duplicatePodcast !== null) {
            throw new BadRequestHttpException();
        }

        $this->repository->save($podcast, true);

        $this->bus->dispatch(new UpdateSinglePodcastMessage($podcast->getId()));

        $savedPodcast = $this->repository->findOneBy(['url' => $podcast->getUrl()]);

        return $this->response($savedPodcast);
    }

    #[Route('/podcast/{id}', name: 'api_podcast_get', methods: ["GET"])]
    public function get(string $id): Response
    {
        $podcast = $this->repository->getOne($id);

        return $this->response($podcast);
    }

    #[Route('/podcasts', name: 'api_podcast_get_all', methods: ["GET"])]
    public function getAll(): Response
    {
        $podcasts = $this->repository->getAll();

        return $this->response($podcasts);
    }
    #[Route('/podcast/{id}/refresh', name: 'api_podcast_refresh', methods: ["POST"])]
    public function refresh(string $id): Response
    {
        $podcast = $this->repository->getOne($id);

        $this->bus->dispatch(new UpdateSinglePodcastMessage($podcast->getId()));

        return $this->response($podcast);
    }

    #[Route('/podcasts/refresh', name: 'api_podcast_refresh_all', methods: ['POST'])]
    public function refreshAll(): Response
    {
        $this->bus->dispatch(new UpdateAllPodcastsMessage());

        return new JsonResponse(['message' => 'acknowledged']);
    }
    #[Route('/podcast/{id}', name: 'api_podcast_delete', methods: ["DELETE"])]
    public function delete(string $id): Response
    {
        $podcast = $this->repository->findOneBy(['id' => $id]);

        if ($podcast === null) {
            throw new NotFoundHttpException('podcast not found');
        }

        $podcast->setDisabledAt(new DateTimeImmutable());

        $this->repository->save($podcast, true);

        return $this->response($podcast);
    }

    public function response(Podcast|array $podcast): Response
    {
        $responseBody = $this->serializer->serialize($podcast, 'json', [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            'groups' => ['GET']
        ]);

        return new JsonResponse($responseBody, Response::HTTP_OK, [], true);
    }
}
