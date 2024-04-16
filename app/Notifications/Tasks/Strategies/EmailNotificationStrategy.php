<?php

namespace App\Notifications\Tasks\Strategies;

use App\Interfaces\NotificationStrategy;
use App\Models\Task;
use App\Notifications\Tasks\NewTaskStatusEmailNotification;

/**
 * Notification strategy implementation for sending notifications via email.
 */
class EmailNotificationStrategy implements NotificationStrategy
{
    /**
     * Send a notification for the given task via email.
     *
     * @param Task $task The task to send the notification for.
     */
    public function sendNotification(Task $task): void
    {
        $task->user->notify(new NewTaskStatusEmailNotification($task));
    }
}
