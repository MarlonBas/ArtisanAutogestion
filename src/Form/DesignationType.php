<?php

namespace App\Form;

use App\Entity\Designation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DesignationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description ',
                'attr' => [
                    'rows' => '3', 
                '   cols' => '30',]
            ])
            ->add('unite', null, ['label' => 'Unité ',
            'required' => false,])
            ->add('prixUnitaire', null, ['label' => 'Prix unitaire '])
            ->add('quantite', null, ['label' => 'Quantité '])
            ->add('tva', null, ['label' => 'TVA(%) ',
            'required' => false,
            'data' => 20,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Designation::class,
        ]);
    }
}
