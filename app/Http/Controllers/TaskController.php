<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Category;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [];

        $tasks = auth()->user()->tasks()->with('category')->orderBy('priority', 'desc')->orderBy('created_at', 'desc')->get();

        $data['tasks'] = $tasks;

        return view('tasks.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [];

        $categories = Category::orderBy('name')->get();

        $data['categories'] = $categories;

        return view('tasks.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'タスクを作成しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $data = [];

        $this->authorize('view', $task);
        // $this->authorize('nomalPolicy', $task);

        $task->load('category');

        $data['task'] = $task;

        return view('tasks.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        // $this->authorize('nomalPolicy', $task);

        $data = [];

        $categories = Category::orderBy('name')->get();

        $data['task'] = $task;
        $data['categories'] = $categories;

        return view('tasks.edit', $data);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        // $this->authorize('nomalPolicy', $task);

        $task->update($request->validated());

        return redirect()->route('tasks.index')->with('success', 'タスクを更新しました。');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        // $this->authorize('nomalPolicy', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'タスクを削除しました。');
    }
}
