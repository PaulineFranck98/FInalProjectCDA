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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class RegistrationFormType extends HoneyPotType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options); // appelle builder de HoneyPotType
        
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Renseignez votre adresse mail',
                'attr' => [
                    'class' => 'input-base'
                ],
            ])
           
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'options' => [
                    'attr' => ['class' => 'input-base'],
                    'toggle' => true,
                ],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'row_attr' => ['class' => 'flex flex-col w-full  mb-6'],
                    'label_attr' => ['class' => 'mb-2']
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'row_attr' => ['class' => 'flex flex-col w-full  mb-6'],
                    'label_attr' => ['class' => 'mb-2']
                ],

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

             
            // J'ajoute un champ de type 'file' 
            ->add('profilePicture', FileType::class, [
                // Je définis son label
                'label'=> 'Sélectionnez une photo de profil',
                // J'indique que ce champ n'est associé à aucune propriété de l'entité
                'mapped' => false,
                // Je rends ce champ facultatif pour ne pas avoir à re-télécharger l'image à chaque modification des infos utilisateur
                'required' => false,
                // J'utilise le validateur qui est conçu pour valider des objets par rapport à des contraintes
                'attr' => [
                    'class' => 'input-base file:mr-4 file:py-2  file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100'
                ],
                'label_attr' => ['class' => 'mb-2 block'],
                'row_attr' => ['class' => 'mb-12'],
                'constraints' => [
                    // J'utilise la contrainte 'File' pour spécifier les contraintes
                    new File([
                        // Je définis la taille max du fichier à 1024 kilo octets, soit 1MB
                        'maxSize' => '1024k', 
                        // Je définis les type MIME acceptés 
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png', 
                            'image/webp',
                        ],
                        // Je définis le message d'erreur affiché si le type MIME n'est pas valide
                        'mimeTypesMessage' => 'Format d\'image invalide. Le format de l\'image doit être de type jpeg, png ou webp)',
                    ])
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'ai lu et j\'accepte les <a href="#">conditions</a>',
                'attr' => ['class' => 'accent-violet-700'],
                'row_attr' => ['class' => 'flex justify-between w-full'],
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
