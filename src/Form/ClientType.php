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
            ->add('email', null, ['label' => 'Email ', 'attr'=>['class'=>'form_display']])
            ->add('nom', null, ['label' => 'Nom ', 'attr'=>['class'=>'form_display']])
            ->add('adresse', null, ['label' => 'Numéro de rue ', 'attr'=>['class'=>'form_display']])
            ->add('codePostal', null, ['label' => 'Code postal ', 'attr'=>['class'=>'form_display']])
            ->add('ville', null, ['label' => 'Ville ', 'attr'=>['class'=>'form_display']])
            ->add('telephone', null, ['label' => 'Téléphone ', 'attr'=>['class'=>'form_display']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
