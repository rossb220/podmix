<?php

namespace App\Controller\Api;

use App\Entity\EpisodeStrategy;
use App\Entity\Podcast;
use App\Repository\EpisodeStrategyRepository;
use App\Repository\PodcastRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class EpisodeStategyController extends AbstractController
{
    public function __construct(
        readonly SerializerInterface $serializer,
        readonly EpisodeStrategyRepository $repository,
        readonly PodcastRepository $podcastRepository,
    ) {
    }
    #[Route('/episodestrategy', name: 'api_episode_strategy_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $requestBody = $request->getContent();

        if ($requestBody === '') {
            throw new BadRequestException('no body');
        }

        /* @var  EpisodeStrategy */
        $episodeStrategy = $this->serializer->deserialize($requestBody, EpisodeStrategy::class, 'json');

        $duplicateEpisodeStrategy = $this->repository->findOneBy(['title' => $episodeStrategy->getTitle()]);
        if ($duplicateEpisodeStrategy !== null) {
            throw new BadRequestHttpException();
        }

        $podcastIds = array_map(function (Podcast $podcast) {
            return $podcast->getId();
        }, $episodeStrategy->getPodcasts()->toArray());

        $podcasts = $this->podcastRepository->getByIds($podcastIds);

        if (count($podcasts) !== count($podcastIds)) {
            throw new BadRequestHttpException("podcast not founds");
        }

        $episodeStrategy->setPodcasts(new ArrayCollection($podcasts));

        $this->repository->save($episodeStrategy, true);

        $savedEpisodeStrategy = $this->repository->findOneBy(['title' => $episodeStrategy->getTitle()]);

        return $this->response($savedEpisodeStrategy);
    }

    #[Route('/episodestrategy/{id}', name: 'api_episode_strategy_update', methods: ['PUT'])]
    public function update(Request $request): Response
    {
        $requestBody = $request->getContent();

        if ($requestBody === '') {
            throw new BadRequestException('no body');
        }

        /* @var  EpisodeStrategy */
        $episodeStrategy = $this->serializer->deserialize($requestBody, EpisodeStrategy::class, 'json');

        $dbEpisodeStrategy = $this->repository->findOneBy(['id' => $episodeStrategy->getId()]);
        if ($dbEpisodeStrategy === null) {
            throw new NotFoundHttpException();
        }

        $podcastIds = array_map(function (Podcast $podcast) {
            return $podcast->getId();
        }, $episodeStrategy->getPodcasts()->toArray());

        $podcasts = $this->podcastRepository->getByIds($podcastIds);

        if (count($podcasts) !== count($podcastIds)) {
            throw new BadRequestHttpException("podcast not founds");
        }
        $dbEpisodeStrategy->setPodcasts(new ArrayCollection($podcasts));
        $dbEpisodeStrategy->setTitle($episodeStrategy->getTitle());
        $dbEpisodeStrategy->setExpression($episodeStrategy->getExpression());

        $this->repository->save($dbEpisodeStrategy, true);

        $savedEpisodeStrategy = $this->repository->findOneBy(['id' => $episodeStrategy->getId()]);

        return $this->response($savedEpisodeStrategy);
    }

    #[Route('/episodestrategy/{id}', name: 'api_episode_get_strategy', methods: ["GET"])]
    public function get(string $id): Response
    {
        $episodeStrategy = $this->repository->findOneById($id);

        return $this->response($episodeStrategy);
    }

    #[Route('/episodestrategies', name: 'api_get_episode_strategies', methods: ["GET"])]
    public function getAll(): Response
    {
        $episodeStrategies = $this->repository->findAll();

        return $this->response($episodeStrategies);
    }

    public function response(EpisodeStrategy|array $episodeStrategy): Response
    {
        $responseBody = $this->serializer->serialize($episodeStrategy, 'json', [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            'groups' => ['EpisodeStrategy']
        ]);

        return new JsonResponse($responseBody, Response::HTTP_OK, [], true);
    }
}
