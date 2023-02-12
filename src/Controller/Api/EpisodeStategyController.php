<?php

namespace App\Controller\Api;

use App\Entity\EpisodeStrategy;
use App\Repository\EpisodeStrategyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class EpisodeStategyController extends AbstractController
{
    public function __construct(
        readonly SerializerInterface $serializer,
        readonly EpisodeStrategyRepository $repository,
    ) {
    }
    #[Route('/episodestrategy', name: 'api_episode_strategy_create', methods: ['POST'])]
    public function index(Request $request): Response
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

        $this->repository->save($episodeStrategy, true);

        $savedEpisodeStrategy = $this->repository->findOneBy(['title' => $episodeStrategy->getTitle()]);

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
            'groups' => ['GET']
        ]);

        return new JsonResponse($responseBody, Response::HTTP_OK, [], true);
    }
}
