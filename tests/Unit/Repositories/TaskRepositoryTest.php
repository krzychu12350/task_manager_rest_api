<?php

namespace tests\Unit\Repositories;

use App\Enums\StatusEnum;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Tests\TestCase;

/**
 * Class TaskRepositoryTest
 */
class TaskRepositoryTest extends TestCase
{
    protected TaskRepositoryInterface $taskRepository;
    protected User|Collection|Model $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->taskRepository = Mockery::mock(TaskRepositoryInterface::class);
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
    public function test_it_can_get_all_tasks_for_user()
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $this->taskRepository->shouldReceive('all')
            ->once()
            ->with($this->user)
            ->andReturn(Task::where('user_id', $this->user->id)->get());

        $tasks = $this->taskRepository->all($this->user);

        $this->assertInstanceOf(Collection::class, $tasks);

        $this->assertCount(3, $tasks);
    }

    /**
     * Test that it can find a task by ID.
     *
     * @return void
     */
    public function test_it_can_find_task_by_id()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $this->taskRepository->shouldReceive('find')
            ->once()
            ->with($task->id)
            ->andReturn($task);

        $foundTask = $this->taskRepository->find($task->id);

        $this->assertEquals($task->id, $foundTask->id);
    }

    /**
     * Test that it can create a task for a user.
     *
     * @return void
     */
    public function test_it_can_create_task_for_user()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => StatusEnum::OPEN,
        ];

        $this->taskRepository->shouldReceive('create')
            ->once()
            ->with($this->user, $taskData)
            ->andReturn(Task::factory()->create($taskData));

        $task = $this->taskRepository->create($this->user, $taskData);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    /**
     * Test that it can update a task.
     *
     * @return void
     */
    public function test_it_can_update_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updatedData = [
            'title' => 'Updated Title',
        ];

        $this->taskRepository->shouldReceive('update')
            ->once()
            ->with($task->id, $updatedData)
            ->andReturnUsing(function ($id, $data) use ($task) {
                $task->fill($data)->save();
                return $task;
            });

        $updatedTask = $this->taskRepository->update($task->id, $updatedData);

        $this->assertEquals($updatedData['title'], $updatedTask->title);
    }
}
