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
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use App\Form\DesignationType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use \Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class DocumentType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $builder
            ->add('date', DateType::class, [
                'label' => 'Date de creation',
                'widget' => 'single_text'])

            ->add('tva', null, ['label' => 'Taux de TVA (%)',
            'property_path' => 'tva',
            'data' => 20])

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
                'query_builder' => function (EntityRepository $entityRepository) use ($user) {
                    return $entityRepository->createQueryBuilder('client')
                        ->where('client.User = :User')
                        ->setParameter('User', $user);
                },
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes ',
                'required' => false,
                'attr' => [
                    'rows' => '3', 
                '   cols' => '30',]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
