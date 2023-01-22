<?php

namespace App\Message;

final class PodcastUpdateMessage
{
    private int $podcastId;

    public function __construct(int $podcastId)
    {
        $this->podcastId = $podcastId;
    }

    public function getPodcastId(): int
    {
        return $this->podcastId;
    }
}
