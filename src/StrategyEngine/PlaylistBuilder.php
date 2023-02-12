<?php

namespace App\StrategyEngine;

use App\Entity\EpisodeStrategy;
use App\Entity\PlaylistConfig;
use App\Entity\Podcast;
use App\Repository\EpisodeRepository;
use App\Repository\PodcastRepository;

class PlaylistBuilder
{
    protected array $expressionHandlers;
    private EpisodeRepository $repository;

    public function __construct(EpisodeRepository $repository, FilterExpressionHandler $filterExpressionHandler, SortExpressionHandler $sortExpressionHandler)
    {
        $this->repository = $repository;
        $this->expressionHandlers = [$filterExpressionHandler, $sortExpressionHandler];
    }

    /**
     * @param Array<PlaylistConfig> $playlistConfig
     */
    public function getEpisodes(array $playlistConfig): array
    {
        $podcastIds = [];
        $finalEpisodes = [];
        $expressionHandlers = [new FilterExpressionHandler(), new SortExpressionHandler()];

        foreach ($playlistConfig as $config) {
            foreach ($config->getEpisodeStrategy()->getPodcasts()->toArray() as $podcast) {
                $podcastIds[] = $podcast->getId();
            }
        }

        if (count($podcastIds) > 0) {
            $episodes = $this->repository->getAllByPodcastIds($podcastIds);
        } else {
            $episodes = $this->repository->getAll();
        }

        foreach ($playlistConfig as $config) {
            $episodeStrategy = $config->getEpisodeStrategy();
            $searchableEpisodes = [];

            foreach ($episodes as $episode) {
                foreach ($episodeStrategy->getPodcasts() as $ep) {
                    if ($episode->getPodcast()->getId() === $ep->getId()) {
                        $searchableEpisodes[] = $episode;
                    }
                }
            }

            if (count($episodeStrategy->getPodcasts()) === 0) {
                $searchableEpisodes = $episodes;
            }

            $expressionableEpisodes = $searchableEpisodes;
            foreach ($episodeStrategy->getExpression() as $expression) {
                foreach ($expressionHandlers as $expressionHandler) {
                    if ($expressionHandler->getType() === $expression["type"]) {
                        $expressionableEpisodes = $expressionHandler->execute($expression, $expressionableEpisodes);
                    }
                }
            }

                $finalEpisodes = array_merge($finalEpisodes, $expressionableEpisodes);
        }

        return $finalEpisodes;
    }
}
