<?php

namespace App\Form;

use App\Entity\Parametres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ParametresFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modeMicro', CheckboxType::class, ['label' => 'Mode micro-entrepreneur ', 'required' => false,])
            ->add('afficherCalendrier', CheckboxType::class, ['label' => 'Afficher calendrier ', 'required' => false,])
    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parametres::class,
        ]);
    }
}
