<?php

namespace App\Controller\Api;

use App\Entity\Playlist;
use App\Message\PlaylistUpdateMessage;
use App\Message\UpdateSinglePodcastMessage;
use App\Message\UpdateSinglePlaylistMessage;
use App\MessageHandler\UpdateSinglePlaylistMessageHandler;
use App\Repository\EpisodeStrategyRepository;
use App\Repository\PlaylistConfigRepository;
use App\Repository\PlaylistRepository;
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
class PlaylistController extends AbstractController
{
    public function __construct(
        readonly SerializerInterface $serializer,
        readonly PlaylistRepository $repository,
        readonly PlaylistConfigRepository $playlistConfigRepository,
        readonly EpisodeStrategyRepository $episodeStrategyRepository,
        readonly MessageBusInterface $bus,
        readonly UpdateSinglePlaylistMessageHandler $handler
    ) {
    }
    #[Route('/playlist', name: 'api_playlist_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $requestBody = $request->getContent();

        if ($requestBody === '') {
            throw new BadRequestException('no body');
        }

        /* @var  Playlist $playlist */
        $playlist = $this->serializer->deserialize($requestBody, Playlist::class, 'json');

        $duplicateplaylist = $this->repository->findOneBy(['title' => $playlist->getTitle()]);
        if ($duplicateplaylist !== null) {
            throw new BadRequestHttpException();
        }

        $playlist->setPublishedAt(new DateTimeImmutable());

        foreach ($playlist->getPlaylistConfigs() as $key => $config) {
            $episodeStrategy = $this->episodeStrategyRepository->findOneById($config->getEpisodeStrategy()->getId());

            if ($episodeStrategy === null) {
                throw new BadRequestHttpException(sprintf("episode strategy <%d> not found", $config->getEpisodeStrategy()->getId()));
            }

            $config->setPosition($key);
            $config->setEpisodeStrategy($episodeStrategy);
            $config->setPosition($key);
            $config->setEpisodeStrategy($episodeStrategy);
            $playlist->addPlaylistConfig($config);
        }
        $this->repository->save($playlist, true);

        $savedPlaylist = $this->repository->findOneBy(['title' => $playlist->getTitle()]);

        $this->bus->dispatch(new UpdateSinglePlaylistMessage($savedPlaylist->getId()));

        return $this->response($savedPlaylist);
    }

    #[Route('/playlist/{id}/refresh', name: 'api_playlist_refresh', methods: ["POST"])]
    public function refresh(string $id): Response
    {
        $playlist = $this->repository->findOneById($id);

        $handler = $this->handler;

        $handler(new UpdateSinglePlaylistMessage($playlist->getId()));

//        $this->bus->dispatch(new UpdatePlaylistMessage($playlist->getId()));

        return $this->response($playlist);
    }

    #[Route('/playlist/{id}', name: 'api_playlist_update', methods: ['PUT'])]
    public function update(Request $request, string $id): Response
    {
        $requestBody = $request->getContent();

        if ($requestBody === '') {
            throw new BadRequestException('no body');
        }

        /* @var  Playlist $playlist */
        $playlist = $this->serializer->deserialize($requestBody, Playlist::class, 'json');

        /* @var  Playlist $dbPlaylist */
        $dbPlaylist = $this->repository->findOneBy(['id' => $id]);
        if ($dbPlaylist === null) {
            throw new NotFoundHttpException();
        }

        foreach ($dbPlaylist->getPlaylistConfigs() as $config) {
            $this->playlistConfigRepository->remove($config, true);
        }

        foreach ($playlist->getPlaylistConfigs() as $key => $config) {
            $episodeStrategy = $this->episodeStrategyRepository->findOneById($config->getEpisodeStrategy()->getId());

            if ($episodeStrategy === null) {
                throw new BadRequestHttpException(sprintf("episode strategy <%d> not found", $config->getEpisodeStrategy()->getId()));
            }

            $config->setPosition($key);
            $config->setEpisodeStrategy($episodeStrategy);
            $dbPlaylist->addPlaylistConfig($config);
        }
        $this->repository->save($dbPlaylist, true);

        $savedPlaylist = $this->repository->findOneById($dbPlaylist->getId());

        return $this->response($savedPlaylist);
    }

    #[Route('/playlist/{id}', name: 'api_playlist_get', methods: ["GET"])]
    public function get(string $id): Response
    {
        /** @var Playlist $playlist */
        $playlist = $this->repository->findOneBy(['id' => $id]);

        return $this->response($playlist);
    }

    #[Route('/playlists', name: 'api_get_playlists', methods: ["GET"])]
    public function getAll(): Response
    {
        $playlists = $this->repository->findAll();

        return $this->response($playlists);
    }

    #[Route('/playlist/{id}', name: 'api_playlist_delete', methods: ["DELETE"])]
    public function delete(string $id): Response
    {
        /* @var  Playlist $playlist */
        $playlist = $this->repository->findOneById($id);

        if ($playlist === null) {
            throw new NotFoundHttpException('playlist not found');
        }

        $playlist->setDisabledAt(new DateTimeImmutable());
        $this->repository->save($playlist, true);

        return $this->response($playlist);
    }

    public function response(Playlist|array $playlist): Response
    {
        $responseBody = $this->serializer->serialize($playlist, 'json', [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            'groups' => ['GET']
        ]);

        return new JsonResponse($responseBody, Response::HTTP_OK, [], true);
    }
}
