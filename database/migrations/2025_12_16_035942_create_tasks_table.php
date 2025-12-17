<?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration {
        public function up(): void
        {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                
                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete()
                    ->index();
                
                $table->string('title');
                $table->text('description');
                $table->date('due_date')->nullable()->index();
                
                $table->enum('status', [
                    'to_do',
                    'in_progress',
                    'done'
                ])->default('to_do')->index();
                
                $table->timestamps();
            });
        }
        
        public function down(): void
        {
            Schema::dropIfExists('tasks');
        }
    };
