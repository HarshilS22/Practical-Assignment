<?php
    
    namespace App\Http\Controllers;
    
    use App\Http\Implementations\TaskImplementation;
    use App\Http\Requests\TaskRequest;
    use App\Models\Task;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    
    /**
     *
     */
    class TaskController extends Controller
    {
        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|object
         */
        public function index()
        {
            return view('task.index');
        }
        
        /**
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function list(Request $request): JsonResponse
        {
            $query = Task::query();
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if (session('user_role') !== 'Admin') {
                $query->where('user_id', session('user_id'));
            }
            
            $tasks = $query
                ->orderBy('due_date', 'asc')
                ->get()
                ->map(function ($task) {
                    return [
                        'user'        => $task->user?->name, // replace with relation later
                        'title'       => $task->title,
                        'description' => $task->description,
                        'due_date'    => $task->due_date?->format('Y-m-d'),
                        'status'      => $task->status,
                        'updated_at'  => $task->updated_at?->format('Y-m-d H:i'),
                        'action'      => '
                        <button class="btn btn-sm btn-gradient-info" onclick="editTask(' . $task->id . ')">Edit</button>
                        <button class="btn btn-sm btn-gradient-danger" onclick="deleteTask(' . $task->id . ')">Delete</button>
                    ',
                    ];
                });
            
            return response()->json([
                'data' => $tasks
            ]);
        }
        
        /**
         * @param  \App\Http\Requests\TaskRequest  $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function store(TaskRequest $request): JsonResponse
        {
            $task = [
                'title'       => $request->title,
                'description' => $request->description,
                'due_date'    => $request->due_date,
                'status'      => $request->status,
            ];
            
            (new Task())->addTask($task, session('user_id'));
            
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'data'    => $task
            ]);
        }
        
        /**
         * @param  \App\Models\Task  $task
         * @return \Illuminate\Http\JsonResponse
         */
        public function show(Task $task): JsonResponse
        {
            if (session('user_role') !== 'Admin' && $task->user_id !== session('user_id')) {
                abort(403);
            }
            
            return response()->json([
                'data' => [
                    'id'          => $task->id,
                    'title'       => $task->title,
                    'description' => $task->description,
                    'due_date'    => $task->due_date?->format('Y-m-d'),
                    'status'      => $task->status,
                ]
            ]);
        }
        
        /**
         * @param  \App\Http\Requests\TaskRequest  $request
         * @param  \App\Models\Task                $task
         * @return \Illuminate\Http\JsonResponse
         */
        public function update(TaskRequest $request, Task $task): JsonResponse
        {
            if (session('user_role') !== 'Admin' && $task->user_id !== session('user_id')) {
                abort(403);
            }
            
            $task->update($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully'
            ]);
        }
        
        /**
         * @param  \App\Models\Task  $task
         * @return \Illuminate\Http\JsonResponse
         */
        public function destroy(Task $task): JsonResponse
        {
            if (session('user_role') !== 'Admin' && $task->user_id !== session('user_id')) {
                abort(403);
            }
            
            $task->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
        }
        
        /**
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function getTaskForApi(Request $request): JsonResponse
        {
            $userId = Auth::user()->id;
            if (Auth::user()->role === 'Admin') {
                $userId = 'all';
            }
            
            $response = (new TaskImplementation())->getTaskForApi($userId);
            
            return $this->successResponse($response['data'], $response['message']);
        }
        
        /**
         * @param  \App\Http\Requests\TaskRequest  $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function addTaskForApi(TaskRequest $request): JsonResponse
        {
            $response = (new TaskImplementation())->addTaskForApi($request->all(), Auth::user()->id);
            
            return $this->successResponse($response['data'], $response['message']);
        }
        
        /**
         * @param  \App\Http\Requests\TaskRequest  $request
         * @param                                  $taskId
         * @return \Illuminate\Http\JsonResponse
         */
        public function updateTaskForApi(TaskRequest $request, $taskId): JsonResponse
        {
            $response = (new TaskImplementation())->updateTaskForApi($request->all(),$taskId, Auth::user()->id);
            
            return $this->successResponse($response['data'], $response['message']);
        }
        
        /**
         * @param $taskId
         * @return \Illuminate\Http\JsonResponse
         */
        public function deleteTaskForApi($taskId): JsonResponse
        {
            
            $response = (new TaskImplementation())->deleteTaskForApi($taskId, Auth::user()->id);
            
            return $this->successResponse($response['data'], $response['message']);
        }
    }
