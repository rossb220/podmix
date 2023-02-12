<?php

namespace App\StrategyEngine;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class SortExpressionHandler implements ExpressionHandlerInterface
{
    private ExpressionLanguage $expressionLanguage;

    public function getType(): string
    {
        return "sort";
    }

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function execute(array $expression, array $input): array
    {
        if (!isset($expression['script'])) {
            return $input;
        }

        usort($input, function ($a, $b) use ($expression) {
            $result = $this->expressionLanguage->evaluate($expression['script'], [
                'episodeA' => $a,
                'podcastA' => $a->getPodcast(),
                'episodeB' => $b,
                'podcastB' => $b->getPodcast(),
            ]);

            return $result;
        });

        return $input;
    }
}
