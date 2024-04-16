<?php

namespace App\Services;

use App\Events\TaskStatusChangedEvent;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Service class for handling task-related operations.
 */
class TaskService extends BaseService
{
    /**
     * Task repository instance.
     *
     * @var TaskRepositoryInterface
     */
    public function __construct(private readonly TaskRepositoryInterface $taskRepository)
    {
    }

    /**
     * Get all tasks for a given user.
     *
     * @param User $user The user whose tasks to retrieve.
     *
     * @return Collection A collection of tasks.
     */
    public function getAllUserTasks(User $user): Collection
    {
        return $this->taskRepository->all($user);
    }

    /**
     * Get a task by its ID.
     *
     * @param int $id The ID of the task to retrieve.
     *
     * @return Task|null The found task, or null if not found.
     */
    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->find($id);
    }

    /**
     * Create a new task for a user.
     *
     * @param User $user The user for whom to create the task.
     * @param array $data The data to create the task with.
     *
     * @return Model The created task model.
     */
    public function createTask(User $user, array $data): Model
    {
        return $this->taskRepository->create($user, $data);
    }

    /**
     * Update a task.
     *
     * @param int $id The ID of the task to update.
     * @param array $data The data to update the task with.
     *
     * @return Task The updated task model.
     */
    public function updateTask(int $id, array $data): Task
    {
        $task = $this->getTaskById($id);

        $this->taskRepository->update($id, $data);

        if (array_key_exists('status', $data) && $data['status'] !== $task->status->value) {
            event(new TaskStatusChangedEvent($task));
        }

        return $this->getTaskById($id);
    }

    /**
     * Delete a task by its ID.
     *
     * @param int $id The ID of the task to delete.
     *
     * @return void
     */
    public function deleteTask(int $id): void
    {
        $this->taskRepository->delete($id);
    }
}
