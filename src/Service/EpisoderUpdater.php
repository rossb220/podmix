<?php

namespace App\Service;

use App\Entity\Episode;
use App\Entity\Podcast;
use App\Repository\EpisodeRepository;
use App\Repository\PodcastRepository;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;

class EpisoderUpdater
{
    public function __construct(
        readonly PodcastRepository $podcastRepository,
        readonly EpisodeRepository $episodeRepository,
        readonly LoggerInterface $logger
    ) {
    }

    public function updateEpisodes(Podcast $podcast, RssReader $rssReader)
    {
        $dbEpisodeMap = $this->getEpisodesMap($podcast->getEpisodes());

        $rssEpisodes = $rssReader->getEpisodes();

        foreach ($rssEpisodes as $rssEpisode) {
            $episode = $dbEpisodeMap[$rssEpisode->getGuid()] ?? $rssEpisode;
            $episode->setPodcast($podcast);
            $this->episodeRepository->save($episode, true);
        }
    }


    /**
     * @param $episodes Collection<int, Episode>
     *
     * @return array
     */
    private function getEpisodesMap(Collection $episodes): array
    {
        $map = [];

        foreach ($episodes as $episode) {
            $map[$episode->getGuid()] = $episode;
        }

        return $map;
    }

    public function updatePodcast(Podcast $podcast, RssReader $rssReader)
    {
        $rssPodcast = $rssReader->getPodcast();

        $podcast->setPubDate($rssPodcast->getPubDate())
            ->setDescription($rssPodcast->getDescription())
            ->setTitle($rssPodcast->getTitle());

        $this->podcastRepository->save($podcast, true);
    }
}
