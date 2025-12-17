<?php
    
    namespace App\Http\Controllers;
    
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Foundation\Validation\ValidatesRequests;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Routing\Controller as BaseController;
    
    /**
     *
     */
    class Controller extends BaseController
    {
        use AuthorizesRequests, ValidatesRequests;
        
        /**
         * Used to return success response
         *
         * @param $data
         * @param $message
         * @return \Illuminate\Http\JsonResponse
         */
        protected function successResponse($data, $message): JsonResponse
        {
            $data = gettype($data) !== 'object' ? (object) $data : $data;
            
            return response()->json(['success' => true, 'data' => $data, 'message' => $message]);
        }
        
        /**
         * @param  string     $errorMessage
         * @param  int        $statusCode
         * @param  \stdClass  $data
         * @return \Illuminate\Http\JsonResponse
         */
        protected function failedResponse(string $errorMessage = 'Something went wrong. Please try after sometime', int $statusCode = 400, \stdClass $data = new \stdClass()): JsonResponse
        {
            return response()->json(['success' => false, 'data' => $data, 'message' => $errorMessage], $statusCode);
        }
    }
