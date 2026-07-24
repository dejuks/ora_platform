<?php

namespace App\Http\Requests\Admin\Module;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     *
     * The 'super_admin' route middleware already guarantees only a
     * Super Admin reaches this request, so this is just a sanity check.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    /**
     * Prepare the data for validation.
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
        return [

            'name' => [
                'required',
                'string',
                'max:100',
                'unique:modules,name',
            ],

            'code' => [
                'required',
                'string',
                'max:30',
                'alpha_dash',
                'unique:modules,code',
            ],

            'slug' => [
                'required',
                'string',
                'max:100',
                'unique:modules,slug',
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
     * Custom validation messages.
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
     * Friendly attribute names.
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
