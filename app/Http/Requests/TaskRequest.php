<?php
    
    namespace App\Http\Requests;
    
    use Illuminate\Foundation\Http\FormRequest;
    
    class TaskRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return true; // handle auth via controller if needed
        }
        
        public function rules(): array
        {
            return [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'due_date'    => 'required|date',
                'status'      => 'required|in:to_do,in_progress,done',
            ];
        }
        
        public function messages(): array
        {
            return [
                'title.required'    => 'Title is required',
                'due_date.required' => 'Due date is required',
                'status.in'         => 'Invalid status selected',
            ];
        }
    }
