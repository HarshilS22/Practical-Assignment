<?php
    
    namespace Database\Seeders;
    
    use App\Models\User;
    use Illuminate\Database\Seeder;
    
    class AdminUserSeeder extends Seeder
    {
        public function run(): void
        {
            User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name'      => 'Admin',
                    'password'  => '$2y$10$L26qQykDaYIkBw4n7/j/NOog8Xe3Fn37Oa2MACPaOSUxvrVgrgxXK',
                    'role'      => 'Admin',
                    'status' => true,
                ]
            );
        }
    }
