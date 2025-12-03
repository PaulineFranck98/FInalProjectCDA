<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,  SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Je précise que $profilePictureFile sera une instance de UploadedFile
             /** @var UploadedFile $profilePictureFile */
            // Je récupère les données du champ 'profilePicture' de mon formulaire
            $profilePictureFile = $form->get('profilePicture')->getData();
            // Je vérifie si un fichier a bien été téléchargé
            if($profilePictureFile){
                // J'extrais le nom du fichier original sans l'extension, et je le stocke dans la variable 'originalFilename'
                $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Je rends la chaîne de caractères sûre en enlevant les espaces et caractères spéciaux avec la fonction slug()
                $safeFilename = $slugger->slug($originalFilename);
                // Je génère un nom de fichier unique en ajoutant un identifiant unique
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePictureFile->guessExtension();

                try{
                    // Je déplace le fichier vers le dossier où les images téléchargées sont stockées
                    $profilePictureFile->move(
                        // Je récupère le chemin du dossier de téléchargements
                        $this->getParameter('uploads_directory'),
                        // Nouveau nom sous lequel le fichier sera enregitré
                        $newFilename
                    );
                // J'intercepte et gère l'exception en cas d'erreur lors du téléchargement
                } catch(FileException $e){
                    // J'affiche une message d'erreur et stoppe le script 
                    dd('Impossible de déplacer l\'image téléchargée vers le dossier');
                }
                // J'attribue à la propriété 'profilePicture' de l'utilisateur le nouveau nom de fichier    
                $user->setProfilePicture($newFilename);
            }


            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // ============== UNCOMMENT THIS WHEN TEST IS OVER ===========================
            
            // generate a signed url and email it to the user
            // $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            //     (new TemplatedEmail())
            //         ->from(new Address('mailer@admin.com', 'Admin'))
            //         ->to((string) $user->getEmail())
            //         ->subject('Please Confirm your Email')
            //         ->htmlTemplate('registration/confirmation_email.html.twig')
            // );

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    // #[Route('/verify/email', name: 'app_verify_email')]
    // public function verifyUserEmail(Request $request): Response
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    //     // validate email confirmation link, sets User::isVerified=true and persists
    //     try {
    //         /** @var User $user */
    //         $user = $this->getUser();
    //         $this->emailVerifier->handleEmailConfirmation($request, $user);
    //     } catch (VerifyEmailExceptionInterface $exception) {
    //         $this->addFlash('verify_email_error', $exception->getReason());

    //         return $this->redirectToRoute('app_register');
    //     }

    //     // @TODO Change the redirect on success and handle or remove the flash message in your templates
    //     $this->addFlash('success', 'Your email address has been verified.');

    //     return $this->redirectToRoute('app_register');
    // }
}
