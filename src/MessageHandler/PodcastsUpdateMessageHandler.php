<?php

namespace App\MessageHandler;

use App\Message\PodcastsUpdateMessage;
use App\Repository\EpisodeRepository;
use App\Repository\PodcastRepository;
use App\Service\RssReader;
use App\Service\EpisoderUpdater;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PodcastsUpdateMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        readonly EpisoderUpdater $updater,
        readonly PodcastRepository $repository
    ) {
    }
    public function __invoke(PodcastsUpdateMessage $message)
    {
        $podcasts = $this->repository->getAll();

        foreach ($podcasts as $podcast) {
            $this->updater->updateEpisodes($podcast);
        }
    }
}
