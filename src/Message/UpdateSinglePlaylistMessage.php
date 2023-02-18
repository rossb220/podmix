<?php

namespace App\Message;

final class UpdateSinglePlaylistMessage
{
    public function __construct(
        readonly string $playlistId
    ) {
    }

    public function getPlaylistId(): string
    {
        return $this->playlistId;
    }
}
