<?php

namespace Tests\Feature;

use App\Enums\StatusEnum;
use App\Models\Task;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TaskTest extends TestCase
{
    protected array $newTaskData;

    public function setUp(): void
    {
        parent::setUp();

        $this->newTaskData = [
            'title' => 'Updated Task Title',
        ];
    }

    /**
     * Test that the API returns the list of tasks.
     *
     * @return void
     */
    public function test_api_returns_tasks_list(): void
    {
        $this->actAsAuthenticatedUser();

        $this->getJson('api/tasks')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'status',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'message'
                ]
            );
    }

    /**
     * Test that the API can successfully store a new task.
     *
     * @return void
     */
    public function test_api_successful_store_new_task(): void
    {
        $this->actAsAuthenticatedUser();

        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => StatusEnum::random(),
        ];

        $this->postJson('/api/tasks', $taskData)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    /**
     * Test that the API successfully updates an existing task.
     *
     * @return void
     */
    public function test_api_successful_update_existing_task(): void
    {
        $authenticatedUser = $this->actAsAuthenticatedUser();

        $task = Task::factory()->create();
        $authenticatedUser->tasks()->save($task);

        $newData = [
            'title' => 'Updated Task Title',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $newData)
            ->assertStatus(Response::HTTP_OK);

        $updatedTask = Task::find($task->id);
        $this->assertEquals($newData['title'], $updatedTask->title);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'title',
                'description',
                'status',
                'created_at',
                'updated_at',
            ],
            'message'
        ]);

    }

    /**
     * Test that the API successfully deletes an existing task.
     *
     * @return void
     */
    public function test_api_successful_delete_existing_task(): void
    {
        $authenticatedUser = $this->actAsAuthenticatedUser();

        $task = Task::factory()->create();
        $authenticatedUser->tasks()->save($task);

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * Test that the API returns a 401 status code for unauthenticated delete request.
     *
     * @return void
     */
    public function test_api_unauthenticated_destroy_task(): void
    {
        $task = Task::factory()->create();

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /**
     * Test that the API returns a 404 status code for an invalid task ID for delete request.
     *
     * @return void
     */
    public function test_api_invalid_task_id_for_destroy(): void
    {
        $this->actAsAuthenticatedUser();

        $notExistingTaskId = $this->getNotExistingTaskId();

        $this->deleteJson("/api/tasks/{$notExistingTaskId}")
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Test that the API returns validation errors for the store endpoint.
     *
     * @return void
     */
    public function test_api_returns_validation_errors_for_store_endpoint(): void
    {
        $this->actAsAuthenticatedUser();

        $this->postJson('/api/tasks', [
            'title' => '',
            'description' => '',
            'status' => ''
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['title', 'description', 'status']);
    }

    /**
     * Test that the API allows a user to update a task if authorized.
     *
     * @return void
     */
    public function test_api_allows_user_to_update_task_if_authorized()
    {
        $authenticatedUser = $this->actAsAuthenticatedUser();
        $task = Task::factory()->create();
        $authenticatedUser->tasks()->save($task);

        $newData = [
            'title' => 'Updated Task Title',
        ];

        $this->putJson("/api/tasks/{$task->id}", $newData)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'message'
            ]);
    }

    /**
     * Test that the API does not allow a user to update a task if not authorized.
     *
     * @return void
     */
    public function test_api_does_not_allow_user_to_update_task_if_not_authorized()
    {
        $this->actAsAuthenticatedUser();

        $task = Task::factory()->create();

        $this->putJson("/api/tasks/{$task->id}", $this->newTaskData)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => 'Unauthorized action'
                ],
            ]);
    }

    /**
     * Test that the API allows a user to delete a task if authorized.
     *
     * @return void
     */
    public function test_api_allows_user_to_delete_task_if_authorized()
    {
        $authenticatedUser = $this->actAsAuthenticatedUser();

        $task = Task::factory()->create();
        $authenticatedUser->tasks()->save($task);

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * Test that the API does not allow a user to delete a task if not authorized.
     *
     * @return void
     */
    public function test_api_does_not_allow_user_to_delete_task_if_not_authorized()
    {
        $this->actAsAuthenticatedUser();

        $task = Task::factory()->create();

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => 'Unauthorized action'
                ],
            ]);

    }

    private function getNotExistingTaskId(): int
    {
        $maxTaskId = Task::max('id');

        return ++$maxTaskId;
    }
}
