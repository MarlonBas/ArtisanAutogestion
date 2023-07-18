<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Client;
use App\Form\DesignationType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'Date de creation',
                'widget' => 'single_text'])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Devis' => 'devisEnCours',
                    'Facture' => 'facturesEnCours',
                ],
                'label' => 'Type de document'
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'label' => 'Client',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
