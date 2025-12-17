<?php
    
    namespace App\Http\Implementations;
    
    
    class BaseImplementation
    {
        public function successResponse($data, $message = ''): array
        {
            return ['success' => true, 'data' => (object) $data, 'message' => $message];
        }
        
        /**
         * @param             $message
         * @param  \stdClass  $data
         * @return array
         */
        public function failureResponse($message, \stdClass $data = new \stdClass()): array
        {
            return ['success' => false, 'data' => $data, 'message' => $message];
        }
    }
