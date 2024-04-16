<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Http\Requests\Tasks\DeleteTaskRequest;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Http\Resources\Tasks\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller handling tasks CRUD operations.
 */
class TaskController extends Controller
{
    /**
     * Task service instance.
     *
     * @var TaskService
     */
    public function __construct(private readonly TaskService $taskService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse The JSON response.
     */
    public function index(): JsonResponse
    {
        $tasks = $this->taskService
            ->getAllUserTasks(AuthHelper::getCurrentUser());

        return $this->success(
            TaskResource::collection($tasks)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTaskRequest $request The store task request.
     *
     * @return JsonResponse The JSON response.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask(
            AuthHelper::getCurrentUser(),
            $request->validated()
        );

        return $this->success(
            new TaskResource($task),
            'Task created successfully',
            201
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTaskRequest $request The update task request.
     * @param Task $task The task to update.
     *
     * @return JsonResponse The JSON response.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $updatedTask = $this->taskService->updateTask($task->id, $request->validated());

        return $this->success(
            new TaskResource($updatedTask),
            'Task updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteTaskRequest $request The request instance.
     * @param Task $task The task to delete.
     *
     * @return JsonResponse The JSON response.
     */
    public function destroy(DeleteTaskRequest $request,Task $task): JsonResponse
    {
        $this->taskService->deleteTask($task->id);

        return $this->success(
            [],
            '',
            Response::HTTP_NO_CONTENT
        );
    }
}
