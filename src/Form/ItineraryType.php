<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Itinerary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ItineraryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('itineraryName', TextType::class, [
                'label' => "Nom de l'itinéraire",
                'attr' => ['placeholder' => 'Ex : Road trip en Bretagne'],
                'label_attr' => ['class' => 'mb-2']
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée estimée (en jours)',
                'attr' => ['min' => 1, 'placeholder' => 'Ex : 3'],
                'label_attr' => ['class' => 'mb-2']
            ])
            ->add('departureDate', DateType::class, [
                'label' => 'Date de départ prévue',
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                ],
                'label_attr' => ['class' => 'mb-2']
            ])
            ->add('isPublic', ChoiceType::class, [
                'label' => "Visibilité de l'itinéraire",
                'choices' => [
                    'Privé' => false,
                    'Public' => true,
                ],
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Choisissez la visibilité',
            ])
            ->add('save', SubmitType::class, [
                'label' => "Créer l'itinéraire",
                'attr' => [ 'class' => 'btn-violet']
            ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Itinerary::class,
        ]);
    }
}
