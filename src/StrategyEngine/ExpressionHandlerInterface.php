<?php

namespace App\StrategyEngine;

use App\Entity\AbstractExpression;
use App\Entity\Episode;
use App\Entity\Podcast;

interface ExpressionHandlerInterface
{
    public function getType(): string;

    /**
     * @return Array<Episode>
     */
    public function execute(array $expression, array $input): array;
}
