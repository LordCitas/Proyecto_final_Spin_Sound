<?php

namespace App\Form;

use App\Entity\Artista;
use App\Entity\Genero;
use App\Entity\Vinilo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ViniloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('fecha_lanzamiento')
            ->add('precio')
            ->add('stock')
            ->add('artistas', EntityType::class, [
                'class' => Artista::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('generos', EntityType::class, [
                'class' => Genero::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vinilo::class,
        ]);
    }
}
