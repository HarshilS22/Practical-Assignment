<?php
    
    namespace App\Models;
    
    use App\Http\Requests\RegisterRequest;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Tymon\JWTAuth\Contracts\JWTSubject;
    
    use Illuminate\Support\Facades\Hash;
    
    /**
     *
     */
    class User extends Authenticatable implements JWTSubject
    {
        use Notifiable;
        
        // Explicit table name
        /**
         *
         */
        const DEFAULT_USER_ROLE = 'User';
        /**
         *
         */
        const DEFAULT_USER_STATUS = true;
        /**
         * @var string
         */
        protected $table = 'users';
        // Mass assignable fields
        /**
         * @var string[]
         */
        protected $fillable = [
            'name',
            'email',
            'password',
            'role',
            'status',
        ];
        // Hidden fields in arrays / JSON
        /**
         * @var string[]
         */
        protected $hidden = [
            'password',
            'remember_token',
        ];
        // Cast attributes
        /**
         * @var string[]
         */
        protected $casts = [
            'status' => 'boolean',
        ];
        
        public function getJWTIdentifier()
        {
            return $this->getKey();
        }
        
        public function getJWTCustomClaims(): array
        {
            return [];
        }
        
        /**
         * @param $email
         * @return mixed
         */
        public function getUserDataByEmail($email): mixed
        {
            return $this->where('email', $email)->first();
        }
        
        /**
         * @param  \App\Http\Requests\RegisterRequest  $request
         * @return \App\Models\User
         */
        public function createUser(RegisterRequest $request): User
        {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = self::DEFAULT_USER_ROLE; // default role
            $user->status = self::DEFAULT_USER_STATUS; // active status
            $user->save();
            
            return $user;
        }
    }
