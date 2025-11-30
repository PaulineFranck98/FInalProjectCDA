<?php

namespace App\Schedule;

use App\Message\UserDeletionMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Scheduler\RecurringMessage;

#[AsSchedule('default')]
class UserDeletionScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            // 1 fois par jour
            ->add(
                RecurringMessage::every('1 day', new UserDeletionMessage())
            );
    }
}
