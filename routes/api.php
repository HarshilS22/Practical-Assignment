<?php
    
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\TaskController;
    use Illuminate\Support\Facades\Route;
    
    /*
    |--------------------------------------------------------------------------
    | ADMIN APP API Routes
    |--------------------------------------------------------------------------
    |
    | Here you will define the latest routes of admin app
    |
    */
    
    Route::post('/login', [AuthController::class, 'loginForAPI']);
    
    Route::middleware(['jwt.auth'])->group(function () {
        Route::get('/tasks', [TaskController::class, 'getTaskForApi'])->name('tasks');
        Route::post('/task', [TaskController::class, 'addTaskForApi'])->name('task.add');
        Route::put('/task/{id}', [TaskController::class, 'updateTaskForApi'])->name('task.update');
        Route::delete('/task/{id}', [TaskController::class, 'deleteTaskForApi'])->name('task.delete');
    });
