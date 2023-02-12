<?php

namespace App\MessageHandler;

use App\Entity\PlaylistEpisode;
use App\Message\UpdateSinglePlaylistMessage;
use App\Repository\PlaylistEpisodeRepository;
use App\Repository\PlaylistRepository;
use App\StrategyEngine\PlaylistBuilder;
use DateTimeImmutable;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateSinglePlaylistMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        readonly PlaylistBuilder $episodeor,
        readonly PlaylistEpisodeRepository $repository,
        readonly PlaylistRepository $playlistRepository
    ) {
    }

    public function __invoke(UpdateSinglePlaylistMessage $message)
    {
        $this->repository->removeAllByPlaylistId($message->getPlaylistId());

        $playlist = $this->playlistRepository->findOneById($message->getPlaylistId());

        $episodes = $this->episodeor->getEpisodes($playlist->getPlaylistConfigs()->toArray());
        $date  = new DateTimeImmutable();

        foreach ($episodes as $episode) {
            $playlistEpisode = (new PlaylistEpisode())
                ->setPlaylist($playlist)
                ->setPublishedAt($date)
                ->setEpisode($episode);

                $this->repository->save($playlistEpisode, true);
        }
    }
}
