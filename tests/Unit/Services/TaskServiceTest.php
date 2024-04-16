<?php

namespace tests\Unit\Services;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

/**
 * Class TaskServiceTest
 */
class TaskServiceTest extends TestCase
{
    protected TaskService $taskService;
    protected TaskRepositoryInterface $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskRepository = Mockery::mock(TaskRepositoryInterface::class);

        $this->taskService = new TaskService($this->taskRepository);

        Event::fake();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * Test that it can get all tasks for a user.
     *
     * @return void
     */
    public function test_it_can_get_all_user_tasks()
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create(['user_id' => $user->id]);

        $this->taskRepository->shouldReceive('all')
            ->once()
            ->with($user)
            ->andReturn($tasks);

        $result = $this->taskService->getAllUserTasks($user);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals($tasks->toArray(), $result->toArray());
    }

    /**
     * Test that it can get a task by its ID.
     *
     * @return void
     */
    public function test_it_can_get_task_by_id()
    {
        $task = Task::factory()->create();

        $this->taskRepository->shouldReceive('find')
            ->once()
            ->with($task->id)
            ->andReturn($task);

        $result = $this->taskService->getTaskById($task->id);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($task->toArray(), $result->toArray());
    }

    /**
     * Test that it can create a task.
     *
     * @return void
     */
    public function test_it_can_create_task()
    {
        $user = User::factory()->create();
        $taskData = ['title' => 'Task title', 'description' => 'Task description'];

        $this->taskRepository->shouldReceive('create')
            ->once()
            ->with($user, $taskData)
            ->andReturn(new Task($taskData));

        $result = $this->taskService->createTask($user, $taskData);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($taskData['title'], $result->title);
        $this->assertEquals($taskData['description'], $result->description);
    }

    /**
     * Test that it can update a task.
     *
     * @return void
     */
    public function test_it_can_update_task()
    {
        $task = Task::factory()->create();
        $updatedData = ['title' => 'Updated Title', 'description' => 'Updated Description'];

        $this->taskRepository->shouldReceive('find')
            ->zeroOrMoreTimes()
            ->with($task->id)
            ->andReturn($task);

        $this->taskRepository->shouldReceive('update')
            ->once()
            ->with($task->id, $updatedData)
            ->andReturnUsing(function ($id, $data) use ($task) {
                $task->fill($data)->save();
                return $task;
            });

        $result = $this->taskService->updateTask($task->id, $updatedData);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($updatedData['title'], $result->title);
    }

    /**
     * Test that it can delete a task.
     *
     * @return void
     */
    public function test_it_can_delete_task()
    {
        $task = Task::factory()->create();

        $this->taskRepository->shouldReceive('delete')
            ->once()
            ->with($task->id);

        $this->taskService->deleteTask($task->id);
    }
}
