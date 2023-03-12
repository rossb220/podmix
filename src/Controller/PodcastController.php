<?php

namespace App\Controller;

use App\Entity\Podcast;
use App\Form\NewPodcastType;
use App\Form\PodcastType;
use App\Message\UpdateSinglePodcastMessage;
use App\Repository\PodcastRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/podcast')]
class PodcastController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }
    #[Route('/', name: 'app_podcast_index', methods: ['GET'])]
    public function index(PodcastRepository $podcastRepository): Response
    {
        return $this->render('podcast/index.html.twig', [
            'podcasts' => $podcastRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_podcast_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PodcastRepository $podcastRepository): Response
    {
        $podcast = new Podcast();
        $form = $this->createForm(NewPodcastType::class, $podcast);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $podcastRepository->save($podcast, true);

            $this->bus->dispatch(new UpdateSinglePodcastMessage($podcast->getId()));

            return $this->redirectToRoute('app_podcast_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('podcast/new.html.twig', [
            'podcast' => $podcast,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_podcast_show', methods: ['GET'])]
    public function show(Podcast $podcast): Response
    {
        return $this->render('podcast/show.html.twig', [
            'podcast' => $podcast,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_podcast_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Podcast $podcast, PodcastRepository $podcastRepository): Response
    {
        $form = $this->createForm(PodcastType::class, $podcast);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $podcastRepository->save($podcast, true);

            $this->bus->dispatch(new UpdateSinglePodcastMessage($podcast->getId()));

            return $this->redirectToRoute('app_podcast_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('podcast/edit.html.twig', [
            'podcast' => $podcast,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_podcast_delete', methods: ['POST'])]
    public function delete(Request $request, Podcast $podcast, PodcastRepository $podcastRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $podcast->getId(), $request->request->get('_token'))) {
            $podcastRepository->remove($podcast, true);
        }

        return $this->redirectToRoute('app_podcast_index', [], Response::HTTP_SEE_OTHER);
    }
}
