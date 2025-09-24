<?php

namespace App\Form;

use App\Entity\Rating;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // rating is a number between 1 and 5 inclusive
            ->add('rating', IntegerType::class, [
                'attr' => [
                    'data-toggle' => 'rating',
                    'data-min' => 1,
                    'data-max' => 5,
                    'data-step' => 1,
                    'data-size' => 'sm',
                ],
                'label' => false,
            ])
            //the rating is mandatory, but the associated comment is optional
            ->add('comment', TextareaType::class, [
                // I set 'required' to 'false' to make it optional
                'required' => false,
                'label' => false
            ])
    
            ->add('valider', SubmitType::class)
         
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
