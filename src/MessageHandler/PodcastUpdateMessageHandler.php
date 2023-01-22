<?php

namespace App\MessageHandler;

use App\Message\PodcastUpdateMessage;
use App\Repository\PodcastRepository;
use App\Service\EpisoderUpdater;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Exception;

final class PodcastUpdateMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        readonly PodcastRepository $podcastRepository,
        readonly EpisoderUpdater $updater,
        readonly LoggerInterface $logger
    ) {
    }
    public function __invoke(PodcastUpdateMessage $message): void
    {
        $podcastId = $message->getPodcastId();

        try {
            $podcast = $this->podcastRepository->getOneWithEpisodes($podcastId);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error(sprintf("podcast with id <%d> not found", $podcastId));

            return;
        }

        $this->updater->updateEpisodes($podcast);
    }
}
