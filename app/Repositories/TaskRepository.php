<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Repository class for handling Task-related database operations.
 */
class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Retrieve all tasks for a given user.
     *
     * @param User $user The user whose tasks to retrieve.
     *
     * @return Collection A collection of tasks.
     */
    public function all(User $user): Collection
    {
        return $user->tasks()->get();
    }

    /**
     * Find a task by its ID.
     *
     * @param int $id The ID of the task to find.
     *
     * @return Task|null The found task, or null if not found.
     */
    public function find(int $id): ?Task
    {
        return Task::findOrFail($id);
    }

    /**
     * Create a new task for a user.
     *
     * @param User $user The user for whom to create the task.
     * @param array $data The data to create the task with.
     *
     * @return Model The created task model.
     */
    public function create(User $user, array $data): Model
    {
        return $user->tasks()->create($data);
    }

    /**
     * Update a task.
     *
     * @param int $id The ID of the task to update.
     * @param array $data The data to update the task with.
     *
     * @return Task The updated task model.
     */
    public function update(int $id, array $data): Task
    {
        $task = Task::findOrFail($id);
        $task->update($data);

        return $task;
    }

    /**
     * Delete a task by its ID.
     *
     * @param int $id The ID of the task to delete.
     */
    public function delete(int $id): void
    {
        Task::findOrFail($id)->delete();
    }
}
