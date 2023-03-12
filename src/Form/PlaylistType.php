<?php

namespace App\Form;

use App\Entity\EpisodeStrategy;
use App\Entity\Playlist;
use App\Entity\PlaylistConfig;
use App\Form\DataTransformer\PlaylistConfigDataTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Config\Framework\Workflows\WorkflowsConfig\PlaceConfig;

class PlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('playlistConfigs', EntityType::class, [
                'class' => EpisodeStrategy::class,
                'multiple' => true,
                'label' => 'Expression Scripts',
            ])
        ;

        $builder->get('playlistConfigs')->addModelTransformer(new PlaylistConfigDataTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
