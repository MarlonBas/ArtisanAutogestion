<?php

namespace App\Form;

use App\Entity\ArchiveSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ArchiveSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $defaultStartDate = new \DateTime('-1 year');
        $defaultEndDate = new \DateTime();

        $builder
            ->add('searchString', null, [
                'label' => 'Recherche',
                'required' => false,])
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Entre la date',
                'required' => false,
                'data' => $defaultStartDate])
            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text',
                'label' => 'et la date',
                'required' => false,
                'data' => $defaultEndDate])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArchiveSearch::class,
        ]);
    }
}
