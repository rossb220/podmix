<?php

namespace App\Form\DataTransformer;

use App\Entity\EpisodeStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpressionDataTransformer implements DataTransformerInterface
{
    public function transform(mixed $expression): string
    {
        return json_encode($expression);
    }

    public function reverseTransform($expressionScript): ?array
    {
        if (!$expressionScript) {
            return null;
        }

        return json_decode($expressionScript, true);
    }
}
