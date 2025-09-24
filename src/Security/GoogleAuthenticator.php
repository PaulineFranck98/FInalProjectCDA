<?php

namespace App\Security;

use App\Entity\User;
use RuntimeException;
use App\Repository\UserRepository;
use App\Security\AbstractOAuthAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GoogleAuthenticator extends AbstractOAuthAuthenticator
{
    protected string $serviceName = 'google';

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $repository): ?User
    {
        if(!($resourceOwner instanceOf GoogleUser))
        {
            throw new RuntimeException("expecting google user");
        }

        if(true !== ($resourceOwner->toArray()['email_verified'] ?? null))
        {
            throw new AuthenticationException("email not verified");
        }

        $user =  $repository->findOneBy([
            'google_id' => $resourceOwner->getId(),
            'email' => $resourceOwner->getEmail()
        ]);

        // update si infos manquantes lors de la connexion
        if($user) {
            $data = $resourceOwner->toArray();

            if ($user->getUsername() === null) {
                $user->setUsername($data['name'] ?? null);
            }
            if ($user->getProfilePicture() === null) {
                $user->setProfilePicture($data['picture'] ?? null);
            }

            $repository->add($user, true);
        }

        return $user;
    }
}