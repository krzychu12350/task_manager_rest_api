<?php

namespace App\Http\Requests\Tasks;

use App\Enums\StatusEnum;
use App\Rules\EnumRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');

        return Gate::allows('update', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|min:3',
            'description' => 'string|min:12',
            'status' => [
                new EnumRule(StatusEnum::class)
            ]
        ];
    }
}
