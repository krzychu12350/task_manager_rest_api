<?php

namespace App\Interfaces;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function all(User $user): Collection;

    public function find(int $id): ?Task;

    public function create(User $user, array $data):  Model;

    public function update(int $id, array $data): Task;

    public function delete(int $id): void;
}