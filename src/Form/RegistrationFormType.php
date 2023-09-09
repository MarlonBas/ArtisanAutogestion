<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifiantEntreprise', TextType::class, [
            'required' => false,
            ])
            ->add('nom', TextType::class, [
                'label' => 'Prénom et nom ',])
            ->add('titre', TextType::class, [
                'label' => 'Profession ',])
            ->add('adresse', TextType::class, [
                'label' => 'Rue et n° ',])
            ->add('codePostal', TextType::class, [
                'label' => 'Code Postal ',])
            ->add('ville', TextType::class,[
                'label' => 'Ville ',])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone ',])
            ->add('email', TextType::class, [
                'label' => 'Email ',])
            ->add('detailsPayment', TextareaType::class, [
                'label' => 'Coordonnées bancaires ',
                'attr' => [
                    'rows' => '3', 
                '   cols' => '50',]
                ])
            
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe ',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/',"Le mot de passe doit contenir au moins
                    une majuscule, une minuscule, un chiffre, et un caractère spécial")
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
