<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        // Later replace with permission check
        // return auth()->user()->hasPermission('user.create');

        return auth()->check();
    }

    /**
     * Prepare the data for validation.
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
                'unique:users,employee_no'
            ],

            'first_name' => [
                'required',
                'string',
                'max:100'
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100'
            ],

            'last_name' => [
                'required',
                'string',
                'max:100'
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
                'unique:users,username'
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:150',
                'unique:users,email'
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
                'unique:users,phone'
            ],

            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

            'gender' => [
                'nullable',
                'in:Male,Female'
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today'
            ],

            /*
            |--------------------------------------------------------------------------
            | Profile
            |--------------------------------------------------------------------------
            */

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],

            /*
            |--------------------------------------------------------------------------
            | Account
            |--------------------------------------------------------------------------
            */

            'status' => [
                'required',
                'in:Active,Inactive,Suspended,Locked'
            ],

            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],

            /*
            |--------------------------------------------------------------------------
            | Role Assignment
            |--------------------------------------------------------------------------
            |
            | Each role already knows which module it belongs to, so a
            | single flat list covers access across every module —
            | including holding more than one role in the same module.
            |
            */

            'roles' => [
                'nullable',
                'array'
            ],

            'roles.*' => [
                'exists:roles,id'
            ],

            /*
            |--------------------------------------------------------------------------
            | Super Admin
            |--------------------------------------------------------------------------
            */

            'is_super_admin' => [
                'sometimes',
                'boolean'
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

            'password.min' => 'Password must contain at least 8 characters.',

            'roles.*.exists' => 'Selected role does not exist.',

            'profile_photo.image' => 'Profile photo must be an image.',

            'profile_photo.max' => 'Profile photo must not exceed 2 MB.',

            'date_of_birth.before' => 'Date of birth must be before today.'
        ];
    }

    /**
     * Friendly attribute names.
     */
    public function attributes(): array
    {
        return [

            'employee_no' => 'Employee Number',

            'first_name' => 'First Name',

            'middle_name' => 'Middle Name',

            'last_name' => 'Last Name',

            'username' => 'Username',

            'email' => 'Email Address',

            'phone' => 'Phone Number',

            'gender' => 'Gender',

            'date_of_birth' => 'Date of Birth',

            'profile_photo' => 'Profile Photo',

            'status' => 'Account Status',

            'password' => 'Password',

            'roles' => 'Assigned Roles',
        ];
    }
}