<?php

namespace App\Form\DataTransformer;

use App\Entity\EpisodeStrategy;
use App\Entity\PlaylistConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Config\Framework\Workflows\WorkflowsConfig\PlaceConfig;

class PlaylistConfigDataTransformer implements DataTransformerInterface
{
    public function transform(mixed $playlistConfigs)
    {
        $episodeStrategies = [];

        foreach ($playlistConfigs as $playlistConfig) {
            $episodeStrategies[] = $playlistConfig->getEpisodeStrategy();
        }

        return $episodeStrategies;
    }

    public function reverseTransform($expression): ?array
    {
        $playlistConfigs = [];

        foreach ($expression as $index => $strategy) {
            $playlistConfig = (new PlaylistConfig())
                ->setPosition($index)
                ->setEpisodeStrategy($strategy);

            $playlistConfigs[] = $playlistConfig;
        }

        return $playlistConfigs;
    }
}
