<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, ['label' => 'Email '])
            ->add('nom', null, ['label' => 'Nom '])
            ->add('adresse', null, ['label' => 'Numéro et rue '])
            ->add('codePostal', null, ['label' => 'Code postal '])
            ->add('ville', null, ['label' => 'Ville '])
            ->add('telephone', null, ['label' => 'Téléphone '])
            ->add('siret', null, ['label' => 'N° de SIRET ',
            'required' => false,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
