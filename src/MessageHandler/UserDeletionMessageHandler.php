<?php

namespace App\MessageHandler;

use App\Message\UserDeletionMessage;
use App\Repository\UserRepository;
use App\Service\UserDeletionService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserDeletionMessageHandler
{
    public function __construct(private UserRepository $userRepository, private UserDeletionService $deletionService) 
    {
    }

    public function __invoke(UserDeletionMessage $message)
    {
        $users = $this->userRepository->findUsersReadyForDeletion();

        foreach ($users as $user) {
            $this->deletionService->anonymizePermanently($user);
        }
    }
}
