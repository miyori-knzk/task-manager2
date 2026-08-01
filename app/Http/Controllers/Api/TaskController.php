<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $task = Task::with(['category', 'user'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            // ->get();
            ->paginate(15);

        return TaskResource::collection($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): TaskResource
    {
        $task->load(['category', 'user']);

        return new TaskResource($task);
    }
}
