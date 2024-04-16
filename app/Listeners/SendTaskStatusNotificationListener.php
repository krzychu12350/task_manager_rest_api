<?php

namespace App\Listeners;

use App\Events\TaskStatusChangedEvent;

use App\Notifications\Tasks\NotificationService;
use App\Notifications\Tasks\Strategies\EmailNotificationStrategy;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskStatusNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(TaskStatusChangedEvent $event): void
    {
        $task = $event->task;
        $this->notificationService->setNotificationStrategy(new EmailNotificationStrategy());
        $this->notificationService->sendNotification($task);
    }
}
