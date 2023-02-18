<?php

namespace App\StrategyEngine;

use App\Entity\AbstractExpression;
use App\Entity\Episode;
use App\Entity\Podcast;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class FilterExpressionHandler implements ExpressionHandlerInterface
{
    private ExpressionLanguage $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }
    public function getType(): string
    {
        return "filter";
    }

    public function execute(array $expression, array $input): array
    {
        $result = [];

        if (!isset($expression['script'])) {
            return $input;
        }

        foreach ($input as $episode) {
            $isValid = $this->expressionLanguage->evaluate($expression['script'], [
                'episode' => $episode,
                'podcast' => $episode->getPodcast(),
            ]);

            if ($isValid) {
                $result[] = $episode;
            }
        }


        return $result;
    }
}
