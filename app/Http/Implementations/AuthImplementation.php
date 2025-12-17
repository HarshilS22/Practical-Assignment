<?php
    
    namespace App\Http\Implementations;
    
    use Illuminate\Auth\AuthenticationException;
    use Symfony\Component\Routing\Exception\ResourceNotFoundException;
    use Tymon\JWTAuth\Facades\JWTAuth;
    
    /**
     *
     */
    class AuthImplementation extends BaseImplementation
    {
        /**
         * @throws \Illuminate\Auth\AuthenticationException
         */
        public function loginForAPI($request, $auth): array
        {
            $token = $this->getToken($auth, $request);
            
            return $this->successResponse(
                [
                    'user'          => $this->getUserData($auth, 'No user found'),
                    'authorization' => [
                        'token' => $token,
                        'type'  => 'bearer',
                    ]
                ],
                'Token generated successfully');
        }
        
        /**
         * @throws \Illuminate\Auth\AuthenticationException
         */
        public function getToken($auth, $request): mixed
        {
            $token = JWTAuth::attempt(['email' => $request['username'], 'password' => $request['password']]);
            
            if ( !$token) {
                throw new AuthenticationException('Incorrect credentials provided.');
            }
            
            if ($auth::user()->status === true) {
                return $token;
            }
            
            throw new AuthenticationException('Incorrect credentials provided.');
        }
        
        /**
         * @param $auth
         * @param $exceptionMessage
         * @return object
         */
        public function getUserData($auth, $exceptionMessage): object
        {
            $user = $auth::user();
            if ( !$user) {
                throw new ResourceNotFoundException($exceptionMessage);
            }
            
            return (object) [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role,
                'status' => $user->status
            ];
        }
    }
