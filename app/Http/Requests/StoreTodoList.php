<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest as BaseRequest;
use Illuminate\Validation\Rule;

class StoreTodoList extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'assignee' => 'nullable|string',
            'due_date' => 'required|date|after:yesterday',
            'time_tracked' => 'nullable|numeric|gte:0',
            'status' => 'nullable|in:pending,open,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
        ];
    }

    public function messages()
{
    return [
        'due_date.after' => 'Due date must end after created or at least today.',
    ];
}
}
