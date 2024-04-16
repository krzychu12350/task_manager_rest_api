<?php

namespace App\Notifications\Tasks;

use App\Interfaces\NotificationStrategy;
use App\Models\Task;

/**
 * Service responsible for sending notifications related to tasks.
 */
class NotificationService
{
    /**
     * The notification strategy instance.
     *
     * @var NotificationStrategy
     */
    private NotificationStrategy $notificationStrategy;

    /**
     * Set the notification strategy.
     *
     * @param NotificationStrategy $strategy The notification strategy.
     */
    public function setNotificationStrategy(NotificationStrategy $strategy): void
    {
        $this->notificationStrategy = $strategy;
    }

    /**
     * Send a notification for the given task using the set strategy.
     *
     * @param Task $task The task to send the notification for.
     */
    public function sendNotification(Task $task): void
    {
        $this->notificationStrategy->sendNotification($task);
    }
}
