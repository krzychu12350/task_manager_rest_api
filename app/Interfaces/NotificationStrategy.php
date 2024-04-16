<?php

namespace App\Interfaces;

use App\Models\Task;

interface NotificationStrategy
{
    public function sendNotification(Task $task): void;
}