<?php

namespace App\Controller\Api;

use App\Entity\Playlist;
use App\Entity\PlaylistConfig;
use App\Repository\EpisodeStrategyRepository;
use App\StrategyEngine\PlaylistBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ExpressionDebugController extends AbstractController
{
    public function __construct(
        readonly SerializerInterface $serializer,
        readonly PlaylistBuilder $episodeor
    ) {
    }

    #[Route('/api/expression/debug', name: 'app_expression_debug', methods: ['POST'])]
    public function debug(Request $request): Response
    {
        $content = $request->getContent();

        if ($content === null) {
            throw new BadRequestHttpException("body is empty");
        }

        /* @var  Playlist $playlist */
        $config = $this->serializer->deserialize($content, "App\Entity\PlaylistConfig[]", 'json');

        $episodes = $this->episodeor->getEpisodes($config);
        return $this->response($episodes);
    }

    public function response(PlaylistConfig|array $playlist): Response
    {
        $responseBody = $this->serializer->serialize($playlist, 'json', [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            'groups' => ['GET']
        ]);

        return new JsonResponse($responseBody, Response::HTTP_OK, [], true);
    }
}
