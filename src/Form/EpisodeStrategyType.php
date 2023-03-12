<?php

namespace App\Form;

use App\Entity\EpisodeStrategy;
use App\Entity\Podcast;
use App\Form\DataTransformer\ExpressionDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EpisodeStrategyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('length', IntegerType::class)
            ->add('expression', ExpressionType::class)
            ->add('podcasts', EntityType::class, [
                'class' => Podcast::class,
                'multiple' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EpisodeStrategy::class,
        ]);
    }
}
