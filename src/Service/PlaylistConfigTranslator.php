<?php

namespace App\Service;

use App\Entity\Episode;
use App\Entity\Playlist;
use App\Entity\PlaylistConfig;
use App\Entity\Podcast;
use App\Repository\EpisodeRepository;
use App\Repository\EpisodeStrategyRepository;
use App\Repository\PlaylistConfigRepository;
use App\Repository\PodcastRepository;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PlaylistConfigTranslator
{
    public function __construct(
        private readonly EpisodeStrategyRepository $repo,
        private readonly PlaylistConfigRepository $configRepo,
    ) {
    }

    public function new(Playlist $playlist): Playlist
    {
        foreach ($playlist->getPlaylistConfigs() as $key => $config) {
            $episodeStrategy = $this->repo->findOneById($config->getEpisodeStrategy()->getId());

            if ($episodeStrategy === null) {
                throw new BadRequestHttpException(sprintf("episode strategy <%d> not found", $config->getEpisodeStrategy()->getId()));
            }

            $config->setPosition($key);
            $config->setEpisodeStrategy($episodeStrategy);
            $config->setPosition($key);
            $config->setEpisodeStrategy($episodeStrategy);
            $config->setPlaylist($playlist);
            $playlist->addPlaylistConfig($config);
        }

        return $playlist;
    }

    public function update(Playlist $playlist): Playlist
    {
        $this->configRepo->removeAllByPlaylistId($playlist->getId());

        return $this->new($playlist);
    }
}
