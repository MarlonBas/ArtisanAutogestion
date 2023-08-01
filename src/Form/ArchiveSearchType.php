<?php

namespace App\Form;

use App\Entity\ArchiveSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArchiveSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchString', null, [
                'label' => 'Recherche'])
            ->add('date', null, [
                'label' => 'Date de creation'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArchiveSearch::class,
        ]);
    }
}
