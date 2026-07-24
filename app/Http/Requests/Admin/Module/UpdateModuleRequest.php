<?php

namespace App\Http\Requests\Admin\Module;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtolower(trim((string) $this->code)),
            'slug' => Str::slug((string) $this->name),
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $module = $this->route('module');

        $moduleId = is_object($module) ? $module->id : $module;

        return [

            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('modules', 'name')->ignore($moduleId),
            ],

            'code' => [
                'required',
                'string',
                'max:30',
                'alpha_dash',
                Rule::unique('modules', 'code')->ignore($moduleId),
            ],

            'slug' => [
                'required',
                'string',
                'max:100',
                Rule::unique('modules', 'slug')->ignore($moduleId),
            ],

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'route' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Module name is required.',
            'name.unique' => 'Module name already exists.',

            'code.required' => 'Module code is required.',
            'code.unique' => 'Module code already exists.',
            'code.alpha_dash' => 'Module code may only contain letters, numbers, dashes and underscores.',

            'slug.unique' => 'Module slug already exists.',
        ];
    }

    /**
     * Friendly names.
     */
    public function attributes(): array
    {
        return [

            'name' => 'Module Name',
            'code' => 'Module Code',
            'slug' => 'Module Slug',
            'icon' => 'Icon',
            'route' => 'Route',
            'description' => 'Description',
            'is_active' => 'Status',
        ];
    }
}
