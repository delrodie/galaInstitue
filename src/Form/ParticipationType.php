<?php

namespace App\Form;

use App\Entity\Participation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('club')
            ->add('nom')
            ->add('prenoms')
            ->add('tel')
            ->add('ville')
            ->add('montant')
            ->add('status')
            ->add('idTransaction')
            ->add('statutsTransaction')
            ->add('reference')
            ->add('createdAt')
            ->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}
