<?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration {
        public function up(): void
        {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                
                // Basic info
                $table->string('name', 100);
                $table->string('email', 150)->unique();
                
                // Auth
                $table->string('password', 255);
                
                // Role & status
                $table->enum('role', ['Admin', 'User'])
                    ->default('User')
                    ->index();
                
                $table->boolean('status')
                    ->default(true)
                    ->index();
                
                $table->timestamps();
            });
        }
        
        public function down(): void
        {
            Schema::dropIfExists('users');
        }
    };
