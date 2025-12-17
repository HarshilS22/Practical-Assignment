<?php
    
    namespace App\Http\Controllers;
    
    use App\Http\Implementations\AuthImplementation;
    use App\Http\Requests\ApiLoginRequest;
    use App\Http\Requests\LoginRequest;
    use App\Http\Requests\RegisterRequest;
    use App\Models\User;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Session;
    
    /**
     *
     */
    class AuthController extends Controller
    {
        /**
         *
         */
        public function __construct()
        {
            if (Session::has('user_id')) {
                redirect()->route('dashboard')->send();
            }
        }
        
        /**
         * @return object
         */
        public function index(): object
        {
            return view('layouts.login');
        }
        
        /**
         * @param  \App\Http\Requests\LoginRequest  $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function login(LoginRequest $request): RedirectResponse
        {
            $user = (new User())->getUserDataByEmail($request->email);
            
            if ($user && Hash::check($request->password, $user->password)) {
                Session::put('user_id', $user->id);
                Session::put('user_name', $user->name);
                Session::put('user_role', $user->role);
                
                return redirect()->route('dashboard')
                    ->with('success', 'Login successful');
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password');
        }
        
        /**
         * @return object
         */
        public function register(): object
        {
            return view('layouts.register');
        }
        
        /**
         * @param  \App\Http\Requests\RegisterRequest  $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function registerUser(RegisterRequest $request): RedirectResponse
        {
            // Check if user with the same email already exists
            $existingUser = (new User())->getUserDataByEmail($request->email);
            if ($existingUser) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Email already registered');
            }
            
            // Create new user
            $user = (new User())->createUser($request);
            
            Session::put('user_id', $user->id);
            Session::put('user_name', $user->name);
            Session::put('user_role', $user->role);
            
            return redirect()->route('dashboard')
                ->with('success', 'Registration successful');
        }
        
        /**
         * @return \Illuminate\Http\RedirectResponse
         */
        public function logout(): RedirectResponse
        {
            Session::flush();
            
            return redirect()->route('login')
                ->with('success', 'Logged out successfully');
        }
        
        /**
         * @throws \Illuminate\Auth\AuthenticationException
         */
        public function loginForAPI(ApiLoginRequest $request): JsonResponse
        {
            $response = (new AuthImplementation())->loginForAPI($request, Auth::class);
            
            return $this->successResponse($response['data'], $response['message']);
        }
    }
