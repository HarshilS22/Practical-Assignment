# Practical Assignment – Task Management System (Laravel 11)

This project is a Task Management System built using Laravel 11 with JWT-based API authentication and role-based authorization. It is developed as part of a technical assignment submission.

## Features
- User authentication using JWT (JSON Web Token)
- Role-based access control (Admin / User)
- Task CRUD operations (Create, Read, Update, Delete)
- Admin can manage all tasks
- Users can manage only their own tasks
- Secure API routes with JWT middleware
- RESTful API design
- Laravel migrations for database schema

## Tech Stack
- Backend: PHP 8.x, Laravel 11
- Database: MySQL
- Authentication: JWT (tymon/jwt-auth)
- API Testing: Postman
- Version Control: Git & GitHub

## Installation & Setup

1. Clone the repository  
git clone https://github.com/HarshilS22/Practical-Assignment.git  
cd Practical-Assignment  

2. Install dependencies  
composer install  

3. Configure environment file  
Copy `.env.example` to `.env` and update database credentials:  
DB_DATABASE=your_database_name  
DB_USERNAME=your_db_username  
DB_PASSWORD=your_db_password  

4. Generate application key  
php artisan key:generate  

5. Configure JWT Authentication  
composer require tymon/jwt-auth  
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"  
php artisan jwt:secret  

6. Run migrations and seeders  
php artisan migrate --seed  

A default Admin user is created automatically.  
Admin Email: admin@example.com  
Admin Password: 123456  

Users can be created using the Register page.

7. Start the application  
php artisan serve  

Application URL:  
http://127.0.0.1:8000  

## API Endpoints

Authentication  
POST /api/login – User login (JWT token generation)

Tasks (Protected Routes)  
GET /api/tasks – List tasks  
POST /api/task – Create task  
PUT /api/task/{id} – Update task  
DELETE /api/task/{id} – Delete task  

All task-related APIs are protected using JWT middleware.  
Send token in headers:  
Authorization: Bearer <jwt_token>  
Accept: application/json  

## Database
Database structure is created using Laravel migrations located in database/migrations.

## Video Demo
The demo video includes login, JWT token generation, task CRUD operations, and role-based access control.  
Video Link: [(Add your video link here)](https://drive.google.com/file/d/1nSA8QNxu6hCgXFC_S6ohvl8eLeIu_JIE/view?usp=sharing)

## Author
Harshil Sohaliya  
PHP / Laravel Developer  

## License
This project is submitted for technical evaluation purposes only.
