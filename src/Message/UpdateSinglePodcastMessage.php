<?php

namespace App\Message;

final class UpdateSinglePodcastMessage
{
    public function __construct(
        readonly string $podcastId)
    {
    }

    public function getPodcastId(): string
    {
        return $this->podcastId;
    }
}
