<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'attr' => [
                    'placeholder' =>'Nom complet pour la livraison'
                ]
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Numéro et nom de rue'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Code postal'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville de livraison'
                ]
            ])
            ->add('Commander', SubmitType::class, [
                'label' => 'Passer la commande',
                'attr' => [
                    'class' => "btn-outline-secondary"
                ]
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            // data_class nous retourne un objet Purchase par defaut
            'data_class' => Purchase::class
        ]);
    }
}
