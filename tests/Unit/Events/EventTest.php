<?php

namespace tests\Unit\Events;

use App\Events\TaskStatusChangedEvent;
use App\Models\Task;
use App\Models\User;
use App\Notifications\Tasks\NewTaskStatusEmailNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Class EventTest
 */
class EventTest extends TestCase
{
    /**
     * Test that the event is dispatched when the task status is changed.
     *
     * @return void
     */
    public function test_event_was_dispatched_when_task_status_was_changed(): void
    {
        Event::fake();

        $task = Task::factory()->create();

        Event::dispatch(new TaskStatusChangedEvent($task));

        Event::assertDispatched(TaskStatusChangedEvent::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });
    }

    /**
     * Test that the event listener properly handles the triggered event.
     *
     * @return void
     */
    public function test_event_listener_properly_handle_triggered_event(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        Event::dispatch(new TaskStatusChangedEvent($task));

        Notification::assertSentTo($user, NewTaskStatusEmailNotification::class);
    }
}
