<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all()->where('user_id', auth()->user()->id);

        if ($tasks->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No hay tareas creadas todavia'
            ], 200);
        }

        return response()->json([
            'status' => true,
            'tasks' => $tasks
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $task = new Task();

        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->due_date = $request->due_date;

        $task->user_id = auth()->user()->id;

        $task->save();

        return response()->json([
            'status' => true,
            'message' => 'tarea creada con exito',
            'task' => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::find($id);

        if ($task == null) {
            return response()->json([
                "status" => false,
                "message" => "La tarea no existe"
            ], 404);
        }

        return response()->json([
            "status" => true,
            "task" => $task
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->due_date = $request->due_date;

        $task->user_id = auth()->user()->id;

        $task->save();

        return response()->json([
            'status' => true,
            'message' => 'tarea actualizada con exito',
            'task' => $task
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);

        $task->delete();

        return response()->json([
            'status' => true,
            'message' => 'tarea eliminada con exito',
        ], 200);
    }

    public function filter(Request $request){
        $tasks = Task::where('due_date', $request->query('date'))
            ->where('status', $request->query('state'))
            ->get();
        
        return response()->json([
            "status" => true,
            "tasks" => $tasks
        ]);
        

    }
}
