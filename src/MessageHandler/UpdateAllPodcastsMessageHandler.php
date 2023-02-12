<?php

namespace App\MessageHandler;

use App\Message\UpdateAllPodcastsMessage;
use App\Repository\PodcastRepository;
use App\Service\RssReader;
use App\Service\EpisoderUpdater;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateAllPodcastsMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        readonly EpisoderUpdater $updater,
        readonly PodcastRepository $repository
    ) {
    }
    public function __invoke(UpdateAllPodcastsMessage $message)
    {
        $podcasts = $this->repository->getAll();

        foreach ($podcasts as $podcast) {
            $rssReader = new RssReader($podcast->getUrl());
            $this->updater->updateEpisodes($podcast, $rssReader);
            $this->updater->updatePodcast($podcast, $rssReader);
        }
    }
}
