<?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Support\Facades\Auth;
    
    /**
     *
     */
    class Task extends Model
    {
        use HasFactory;
        
        /**
         * Mass assignable fields
         */
        protected $fillable = [
            'user_id',
            'title',
            'description',
            'due_date',
            'status',
        ];
        /**
         * Cast attributes to proper data types
         */
        protected $casts = [
            'due_date' => 'date',
        ];
        /**
         * Default attribute values
         */
        protected $attributes = [
            'status' => 'todo',
        ];
        
        /**
         * Task belongs to a User
         */
        public function user()
        {
            return $this->belongsTo(User::class);
        }
        
        /**
         * @param $userId
         * @return \Illuminate\Database\Eloquent\Collection
         */
        public function getTaskForApi($userId = 'all'): Collection
        {
            if ($userId === 'all') {
                return self::get();
            }
            
            return self::where('user_id', $userId)->get();
        }
        
        /**
         * @param $input
         * @param $userId
         * @return mixed
         */
        public function addTask($input, $userId)
        {
            $dateAndTime = now()->format('Y-m-d H:i:s');
            
            return self::insert([
                'title'       => $input['title'],
                'description' => $input['description'],
                'due_date'    => $input['due_date'],
                'status'      => $input['status'],
                'created_at'  => $dateAndTime,
                'updated_at'  => $dateAndTime,
                'user_id'     => $userId
            ]);
        }
        
        /**
         * @param  array  $input
         * @return \Illuminate\Database\Eloquent\Model
         */
        public function updateTaskForApi(array $input): Model
        {
            $taskQuery = self::query()->where('id', $input['task_id']);
            
            if (Auth::user()->role !== 'Admin') {
                $taskQuery->where('user_id', Auth::id());
            }
            
            $task = $taskQuery->firstOrFail();
            
            $task->update([
                'title'       => $input['title'],
                'description' => $input['description'],
                'due_date'    => $input['due_date'],
                'status'      => $input['status'],
            ]);
            
            return $task;
        }
        
        /**
         * @param $taskId
         * @return mixed
         */
        public function deleteTaskForApi($taskId): mixed
        {
            $query = self::where('id', $taskId);
            
            if (Auth::user()->role !== 'Admin') {
                $query->where('user_id', Auth::id());
            }
            
            return $query->delete();
        }
    }
