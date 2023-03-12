<?php

namespace App\Controller;

use App\Entity\EpisodeStrategy;
use App\Form\EpisodeStrategyType;
use App\Repository\EpisodeStrategyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/episode/strategy')]
class EpisodeStrategyController extends AbstractController
{
    #[Route('/', name: 'app_episode_strategy_index', methods: ['GET'])]
    public function index(EpisodeStrategyRepository $episodeStrategyRepository): Response
    {
        return $this->render('episode_strategy/index.html.twig', [
            'episode_strategies' => $episodeStrategyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_episode_strategy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EpisodeStrategyRepository $episodeStrategyRepository): Response
    {
        $episodeStrategy = new EpisodeStrategy();
        $form = $this->createForm(EpisodeStrategyType::class, $episodeStrategy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodeStrategyRepository->save($episodeStrategy, true);

            return $this->redirectToRoute('app_episode_strategy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode_strategy/new.html.twig', [
            'episode_strategy' => $episodeStrategy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_episode_strategy_show', methods: ['GET'])]
    public function show(EpisodeStrategy $episodeStrategy): Response
    {
        return $this->render('episode_strategy/show.html.twig', [
            'episode_strategy' => $episodeStrategy,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_episode_strategy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EpisodeStrategy $episodeStrategy, EpisodeStrategyRepository $episodeStrategyRepository): Response
    {
        $form = $this->createForm(EpisodeStrategyType::class, $episodeStrategy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodeStrategyRepository->save($episodeStrategy, true);

            return $this->redirectToRoute('app_episode_strategy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode_strategy/edit.html.twig', [
            'episode_strategy' => $episodeStrategy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_episode_strategy_delete', methods: ['POST'])]
    public function delete(Request $request, EpisodeStrategy $episodeStrategy, EpisodeStrategyRepository $episodeStrategyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episodeStrategy->getId(), $request->request->get('_token'))) {
            $episodeStrategyRepository->remove($episodeStrategy, true);
        }

        return $this->redirectToRoute('app_episode_strategy_index', [], Response::HTTP_SEE_OTHER);
    }
}
