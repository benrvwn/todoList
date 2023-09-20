<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Attachement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TaskController extends Controller
{
    protected $task;
    protected $attachement;
    public function __construct(){
        $this->task = new Task;
        $this->attachement = new Attachement;
    }
    public function index()
    {
        $list = $this->task->orderBy('status', 'desc')->get();
        return view('todolist', compact('list'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'desc' => 'required'
        ]);

        $uploadedFiles = $request->file('files');
        $data = [
            'task_name' => $request->input('name'),
            'desc' => $request->input('desc'),
            'status' => 'Ongoing'
        ];
        $newTask = $this->task->insert($data);
        $taskId = $newTask->id;
        if ($uploadedFiles && count($uploadedFiles) > 0) {
            foreach ($uploadedFiles as $file) {
                $file->move(public_path('uploads/' . $request->input('name')), $file->getClientOriginalName());
                $data = [
                    'task_id' => $taskId,
                    'file_name' => $file->getClientOriginalName()
                ];
                $this->attachement->attach($data);
            }
        }

        $fetch = $this->task->orderBy('status', 'desc')->get();
        return response()->json(['data' => $fetch]);
    }

    public function remove($id)
    {
        $data = $this->task->find($id);
        $fileToDelete = public_path('uploads/'. $data->task_name);
        $this->task->remove($id);
        if (File::exists($fileToDelete)) {
            File::deleteDirectory($fileToDelete);
        }
        $this->attachement->removeAllAttach($id);
        $fetch = $this->task->orderBy('status', 'desc')->get();
        return response()->json(['data' => $fetch]);
    }

    public function update(Request $request, $id)
    {
        $data = $this->task->find($id);
        $data->task_name = $request->input('name');
        $data->desc = $request->input('desc');
        $data->save();
        $uploadedFiles = $request->file('files');
        if ($uploadedFiles && count($uploadedFiles) > 0) {
            foreach ($uploadedFiles as $file) {
                $file->move(public_path('uploads/' . $request->input('name')), $file->getClientOriginalName());
                $data = [
                    'task_id' => $id,
                    'file_name' => $file->getClientOriginalName()
                ];
                $this->attachement->attach($data);
            }
        }
        $fetch = $this->task->orderBy('status', 'desc')->get();
        return response()->json(['data' => $fetch]);
    }

    public function viewAttach($id)
    {
        $fetch = $this->attachement->where('task_id', $id)->get()->all();
        return response()->json(['data' => $fetch]);
    }

  
    public function removeAttach($name, $id)
    {
        $data = $this->attachement->find($id);
        $fileToDelete = public_path('uploads/'. $name . '/' . $data->file_name);

        if (File::exists($fileToDelete)) {
            File::delete($fileToDelete);
        }
        $this->attachement->removeAttach($id);
        $fetch = $this->attachement->where('task_id', $data->task_id)->get()->all();
        return response()->json(['data' => $fetch]);
    }

    public function completed($id){
        $data = $this->task->find($id);
        $data->status = 'Completed';
        $data->save();
        $fetch = $this->task->orderBy('status', 'desc')->get();
        return response()->json(['data' => $fetch]);
    }
}
