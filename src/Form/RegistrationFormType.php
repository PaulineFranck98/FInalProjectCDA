<?php

namespace App\Form;

use App\Entity\User;
use App\Form\FormExtension\HoneyPotType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends HoneyPotType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options); // appelle builder de HoneyPotType
        
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Renseignez votre adresse mail'
            ])
           
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'options' => [
                    'attr' => ['class' => 'password-field'],
                    'row_attr' => ['class' => 'formRow'],
                ],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmez votre mot de passe'],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),

                    // new Regex([
                    //     'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$/',

                    //     'message' => 'Votre mot de passe doit contenir aux moins une minuscule, une majuscule, un chiffre et un caractère spécial',
                    // ])
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'ai lu et j\'accepte les <a href="#">conditions</a>',
                'label_html' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions pour continuer',
                    ]),
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
