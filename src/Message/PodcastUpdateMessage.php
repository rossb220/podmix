<?php

namespace App\Message;

final class PodcastUpdateMessage
{
    private string $podcastId;

    public function __construct(string $podcastId)
    {
        $this->podcastId = $podcastId;
    }

    public function getPodcastId(): string
    {
        return $this->podcastId;
    }
}
