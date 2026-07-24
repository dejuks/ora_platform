<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        // Later replace with permission checking
        // return auth()->user()->can('user.update');

        return auth()->check();
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => strtolower(trim((string) $this->username)),
            'email'    => strtolower(trim((string) $this->email)),
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        // Route Model Binding
        $user = $this->route('user');

        $userId = is_object($user) ? $user->id : $user;

        return [

            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */

            'employee_no' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'employee_no')->ignore($userId),
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Login Information
            |--------------------------------------------------------------------------
            */

            'username' => [
                'required',
                'string',
                'min:4',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($userId),
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'phone')->ignore($userId),
            ],

            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

            'gender' => [
                'nullable',
                Rule::in(['Male', 'Female']),
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],

            /*
            |--------------------------------------------------------------------------
            | Profile Photo
            |--------------------------------------------------------------------------
            */

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            /*
            |--------------------------------------------------------------------------
            | Account Status
            |--------------------------------------------------------------------------
            */

            'status' => [
                'required',
                Rule::in([
                    'Active',
                    'Inactive',
                    'Suspended',
                    'Locked',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Password (Optional)
            |--------------------------------------------------------------------------
            */

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Role Assignment
            |--------------------------------------------------------------------------
            */

            'roles' => [
                'nullable',
                'array',
            ],

            'roles.*' => [
                'integer',
                'exists:roles,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Super Admin
            |--------------------------------------------------------------------------
            */

            'is_super_admin' => [
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

            'employee_no.unique' => 'Employee number already exists.',

            'username.unique' => 'Username already exists.',

            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores.',

            'email.unique' => 'Email address already exists.',

            'phone.unique' => 'Phone number already exists.',

            'password.confirmed' => 'Password confirmation does not match.',

            'profile_photo.image' => 'Profile photo must be an image.',

            'profile_photo.max' => 'Profile photo may not exceed 2 MB.',

            'roles.*.exists' => 'One or more selected roles are invalid.',
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'employee_no'    => 'Employee Number',
            'first_name'     => 'First Name',
            'middle_name'    => 'Middle Name',
            'last_name'      => 'Last Name',
            'username'       => 'Username',
            'email'          => 'Email Address',
            'phone'          => 'Phone Number',
            'gender'         => 'Gender',
            'date_of_birth'  => 'Date of Birth',
            'profile_photo'  => 'Profile Photo',
            'status'         => 'Account Status',
            'password'       => 'Password',
            'roles'          => 'Assigned Roles',

        ];
    }
}