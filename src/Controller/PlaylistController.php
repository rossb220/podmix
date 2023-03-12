<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Message\UpdateSinglePlaylistMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PlaylistRepository;
use App\Service\PlaylistConfigTranslator;
use DateTimeImmutable;
use Symfony\Component\Messenger\MessageBusInterface;

#[Route('/playlist')]
class PlaylistController extends AbstractController
{
    public function __construct(
        private readonly PlaylistConfigTranslator $playlistConfigTranslator,
        private readonly PlaylistRepository $playlistRepository,
        private readonly MessageBusInterface $bus
    ) {
    }

    #[Route('/', name: 'app_playlist_index', methods: ['GET'])]
    public function index(PlaylistRepository $playlistRepository): Response
    {
        return $this->render('playlist/index.html.twig', [
            'playlists' => $playlistRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_playlist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PlaylistRepository $playlistRepository): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlist->setPublishedAt(new DateTimeImmutable());
            $playlist = $this->playlistConfigTranslator->new($playlist);
            $playlistRepository->save($playlist, true);

            $this->bus->dispatch(new UpdateSinglePlaylistMessage($playlist->getId()));

            return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('playlist/new.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_show', methods: ['GET'])]
    public function show(Playlist $playlist): Response
    {
        return $this->render('playlist/show.html.twig', [
            'playlist' => $playlist,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_playlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $id, PlaylistRepository $playlistRepository): Response
    {
        $playlist = $this->playlistRepository->findOneById($id);

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlist = $this->playlistConfigTranslator->update($playlist);

            $playlistRepository->save($playlist, true);

            $this->bus->dispatch(new UpdateSinglePlaylistMessage($playlist->getId()));

            return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('playlist/edit.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, PlaylistRepository $playlistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->request->get('_token'))) {
            $playlistRepository->remove($playlist, true);
        }

        return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
    }
}
