<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Validator;


class TaskController extends Controller
{
    public function index()
    {
    $tasks = auth('sanctum')->user()->tasks()->latest()->paginate(20);

    //return collection of tasks as a resource
    return new TaskResource(true, 'List Data task', $tasks);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'customer'   => 'required',
            'status'   => 'required',
            'urgent'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create task
            try {
                $post = auth('sanctum')->user()->tasks()->create([
                    'name'     => $request->name,
                    'customer' => $request->customer,
                    'status'   => $request->status,
                    'urgent'   => $request->urgent,
                    'user_id'  => auth('sanctum')->user()->id,  // Assuming user_id is a field in the Task model
                ]);
            
                return new TaskResource(true, 'Data Task Berhasil Ditambahkan!', $post);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
            }
            
    }

    public function show($id)
    {
        //find post by ID
        $task = auth('sanctum')->user()->tasks()->find($id);

        //return single Task as a resource
        return new TaskResource(true, 'Detail Data Task!', $task);
    }

    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'customer'   => 'required',
            'status'   => 'required',
            'urgent'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $task = auth('sanctum')->user()->tasks()->find($id);

        $task->update([
            'name'     => $request->name,
            'customer'   => $request->customer,
            'status'   => $request->status,
            'urgent'   => $request->urgent,
        ]);

        //return response
        return new TaskResource(true, 'Data Task Berhasil Diubah!', $task);
    }

    public function destroy($id)
    {

        //find post by ID
        $task = auth('sanctum')->user()->tasks()->find($id);
        

        //delete post
        $task->delete();

        //return response
        return new TaskResource(true, 'Data Task Berhasil Dihapus!', null);
    }


}
