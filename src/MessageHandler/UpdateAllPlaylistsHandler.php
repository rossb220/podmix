<?php

namespace App\MessageHandler;

use App\Entity\Playlist;
use App\Message\UpdateAllPlaylistsMessage;
use App\Message\UpdateSinglePlaylistMessage;
use App\Repository\PlaylistRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateAllPlaylistsHandler implements MessageHandlerInterface
{
    public function __construct(
        readonly UpdateSinglePlaylistMessageHandler $handler,
        readonly PlaylistRepository $playlistRepository,
    ) {
    }

    public function __invoke(UpdateAllPlaylistsMessage $message)
    {
        $playlists = $this->playlistRepository->findAll();

        foreach ($playlists as $playlist) {
            $hander = $this->handler;
            $hander(new UpdateSinglePlaylistMessage($playlist->getId()));
        }
    }
}
