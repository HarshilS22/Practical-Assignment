<?php
    
    use App\Http\Controllers\TaskController;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\DashboardController;
    
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerUser'])->name('register.post');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware(['session.auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
        Route::get('/tasks/list', [TaskController::class, 'list'])->name('tasks.list');
        
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::get('/tasks/{task}', [TaskController::class, 'show']);
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    });
