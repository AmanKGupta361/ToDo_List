<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $tasks = Task::where('is_completed', false)->get();
    return view('tasks.index', compact('tasks'));
}

public function store(Request $request)
{
    $request->validate([
        'title' => 'required|unique:tasks',
    ]);

    $task = Task::create(['title' => $request->title]);

    return response()->json(['success' => true, 'id' => $task->id]);
}

public function update($id)
{
    $task = Task::findOrFail($id);
    $task->is_completed = true;
    $task->save();

    return response()->json(['success' => true]);
}

public function destroy($id)
{
    $task = Task::findOrFail($id);
    $task->delete();

    return response()->json(['success' => true]);
}

public function allTasks()
{
    $tasks = Task::all();
    $completedTasks = Task::where('is_completed', true)->get();
    $pendingTasks = Task::where('is_completed', false)->get();

    return view('tasks.all', compact('tasks', 'completedTasks', 'pendingTasks'));
}
}
