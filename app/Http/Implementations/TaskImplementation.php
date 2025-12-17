<?php
    
    namespace App\Http\Implementations;
    
    use App\Models\Task;
    use http\Exception\RuntimeException;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    
    /**
     *
     */
    class TaskImplementation extends BaseImplementation
    {
        /**
         * @param $userId
         * @return array
         */
        public function getTaskForApi($userId): array
        {
            $tasks = (new Task())->getTaskForApi($userId);
            
            if ($tasks->count() === 0) {
                throw new NotFoundHttpException('No task found.');
            }
            
            return $this->successResponse(
                $tasks,
                'Tasks fetched successfully',
            );
        }
        
        /**
         * @param $input
         * @param $userId
         * @return array
         */
        public function addTaskForApi($input, $userId): array
        {
            $task = (new Task())->addTask($input, $userId);
            
            if ( !$task) {
                throw new RuntimeException('Task could not be added.');
            }
            
            return $this->successResponse(
                [],
                'Tasks added successfully',
            );
        }
        
        /**
         * @param $input
         * @param $taskId
         * @param $userId
         * @return array
         */
        public function updateTaskForApi($input, $taskId, $userId): array
        {
            $input['user_id'] = $userId;
            $input['task_id'] = $taskId;
            
            $task = (new Task())->updateTaskForApi($input);
            
            if ( !$task) {
                return $this->failureResponse('Task could not be updated');
            }
            
            return $this->successResponse(
                [],
                'Tasks updated successfully',
            );
        }
        
        /**
         * @param $taskId
         * @param $userId
         * @return array
         */
        public function deleteTaskForApi($taskId, $userId): array
        {
            $task = (new Task())->deleteTaskForApi($taskId, $userId);
            
            if ( !$task) {
                return $this->failureResponse('Task could not be deleted');
            }
            
            return $this->successResponse(
                [],
                'Tasks deleted successfully',
            );
        }
    }
