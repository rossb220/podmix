<?php

namespace App\MessageHandler;

use App\Message\UpdateSinglePodcastMessage;
use App\Repository\PodcastRepository;
use App\Service\EpisoderUpdater;
use App\Service\RssReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Exception;

final class UpdateSinglePodcastMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        readonly PodcastRepository $podcastRepository,
        readonly EpisoderUpdater $updater,
        readonly LoggerInterface $logger
    ) {
    }
    public function __invoke(UpdateSinglePodcastMessage $message): void
    {
        $podcastId = $message->getPodcastId();

        try {
            $podcast = $this->podcastRepository->getOneWithEpisodes($podcastId);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error(sprintf("podcast with id <%d> not found", $podcastId));

            return;
        }

        $rssReader = new RssReader($podcast->getUrl());
        $this->updater->updateEpisodes($podcast, $rssReader);
        $this->updater->updatePodcast($podcast, $rssReader);
    }
}
