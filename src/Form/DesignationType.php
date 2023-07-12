<?php

namespace App\Form;

use App\Entity\Designation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesignationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('unite')
            ->add('prixUnitaire')
            ->add('quantite')
            ->add('prixHorsTax')
            ->add('prixTotal')
            ->add('tva')
            ->add('documents')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Designation::class,
        ]);
    }
}
