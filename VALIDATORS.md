# Laravel Custom Validators

Custom validation rules registered as Laravel rule strings for easy use in any validation context.

## Usage

Custom validation rules are registered as string-based rules and can be used directly in any validation:

```php
// In controller
$validator = $request->validate([
    'email' => 'required|email|custom_unique:users,email',
    'password' => 'required|custom_password',
]);

// In form request class
public function rules()
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|custom_unique:users,email',
        'password' => 'required|custom_password:8',
    ];
}
```

## Available Rules

### 1. custom_exists
Validates that a value exists in a table and is NOT soft deleted (del_flag = 0).

**Usage:**
```php
// Basic: field exists in table
'product_id' => 'custom_exists:products,id'

// With ignore: ignore a specific record (useful for updates)
'category_id' => 'custom_exists:categories,id,id,123'
```

**Parameters:**
- `table` - Table name (required)
- `column` - Column name to check (required)
- `ignore_field` - Field to ignore (optional)
- `ignore_value` - Value to ignore (optional)

**Examples:**

```php
// Create validation
$rules = [
    'category_id' => 'required|custom_exists:categories,id',
    'user_id' => 'required|custom_exists:users,id',
];

// Update validation - ignore current record
$rules = [
    'category_id' => "required|custom_exists:categories,id,id,{$categoryId}",
];
```

**In Request Class:**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|custom_exists:categories,id',
            'user_id' => 'required|custom_exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'category_id.custom_exists' => 'The selected category is invalid or has been deleted.',
        ];
    }
}
```

### 2. custom_unique
Validates that a value is unique in a table and only checks non-deleted records (del_flag = 0).

**Usage:**
```php
// Basic: field must be unique
'email' => 'custom_unique:users,email'

// With ignore: ignore a specific record (useful for updates)
'email' => 'custom_unique:users,email,id,789'
```

**Parameters:**
- `table` - Table name (required)
- `column` - Column to check uniqueness (required)
- `ignore_field` - Field to ignore (optional)
- `ignore_value` - Value to ignore (optional)

**Examples:**

```php
// Registration - email must be unique
$rules = [
    'email' => 'required|email|custom_unique:users,email',
];

// Update profile - email must be unique but ignore own record
$rules = [
    'email' => "required|email|custom_unique:users,email,id," . auth()->id(),
];

// Product slug must be unique
$rules = [
    'slug' => "required|custom_unique:products,slug,id,{$productId}",
];
```

**In Request Class:**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|custom_unique:users,email,id,' . $this->user->id,
        ];
    }

    public function messages()
    {
        return [
            'email.custom_unique' => 'This email is already registered.',
        ];
    }
}
```

### 3. custom_password
Validates password strength with configurable minimum length.

**Default Requirements:**
- Minimum 8 characters
- At least one uppercase letter (A-Z)
- At least one lowercase letter (a-z)
- At least one digit (0-9)
- At least one special character (!@#$%^&*(),.?":{}|<>)

**Usage:**
```php
// Default (8 characters minimum)
'password' => 'required|custom_password'

// Custom minimum length
'password' => 'required|custom_password:12'
```

**Parameters:**
- `length` - Minimum password length (optional, default: 8)

**Examples:**

```php
// Registration - default requirements
$rules = [
    'password' => 'required|custom_password',
    'password_confirmation' => 'required|same:password',
];

// Admin password - stronger requirement
$rules = [
    'password' => 'required|custom_password:12',
    'password_confirmation' => 'required|same:password',
];
```

**In Request Class:**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|custom_unique:users,email',
            'password' => 'required|custom_password',
            'password_confirmation' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'password.custom_password' => 'Password must contain uppercase, lowercase, number, and special character.',
        ];
    }
}
```

## Real-World Examples

### Example 1: User Registration

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|custom_unique:users,email',
            'password' => 'required|custom_password:8',
            'password_confirmation' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email.',
            'email.custom_unique' => 'This email is already registered.',
            'password.custom_password' => 'Password must have uppercase, lowercase, number, and special character.',
        ];
    }
}
```

### Example 2: Product Creation

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|custom_unique:products,slug',
            'category_id' => 'required|custom_exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'category_id.custom_exists' => 'The selected category is invalid or has been deleted.',
            'slug.custom_unique' => 'This product slug already exists.',
        ];
    }
}
```

### Example 3: Product Update

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => "required|string|custom_unique:products,slug,id,{$this->product->id}",
            'category_id' => 'required|custom_exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
}
```

### Example 4: Profile Update with Optional Password

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|custom_unique:users,email,id,' . auth()->id(),
        ];

        // Password is optional, but if provided, must be strong
        if ($this->filled('password')) {
            $rules['password'] = 'required|custom_password';
            $rules['password_confirmation'] = 'required|same:password';
        }

        return $rules;
    }
}
```

### Example 5: Direct Usage in Controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|custom_unique:users,email',
            'password' => 'required|custom_password',
            'password_confirmation' => 'required|same:password',
        ]);

        // Create user...
        return $this->successResponse('User created successfully', $user, 201);
    }

    public function update(Request $request, $userId)
    {
        $validated = $request->validate([
            'email' => "required|email|custom_unique:users,email,id,{$userId}",
            'name' => 'required|string|max:255',
        ]);

        // Update user...
        return $this->successResponse('User updated successfully');
    }
}
```

## Rule Parameters Reference

### custom_exists

| Param | Type | Required | Example |
|-------|------|----------|---------|
| table | string | Yes | `users`, `products` |
| column | string | Yes | `id`, `email` |
| ignore_field | string | No | `id` |
| ignore_value | mixed | No | `123` |

### custom_unique

| Param | Type | Required | Example |
|-------|------|----------|---------|
| table | string | Yes | `users`, `products` |
| column | string | Yes | `email`, `slug` |
| ignore_field | string | No | `id` |
| ignore_value | mixed | No | `123` |

### custom_password

| Param | Type | Required | Default | Example |
|-------|------|----------|---------|---------|
| length | int | No | 8 | `8`, `12`, `16` |

## Key Features

✅ **Soft Delete Aware** - Automatically excludes soft deleted records (del_flag = 1)

✅ **Simple String Syntax** - Use like any Laravel rule: `'field' => 'custom_unique:table,column'`

✅ **Update Friendly** - Support ignore parameters to exclude current record

✅ **Chainable with Other Rules** - Combine with standard Laravel rules: `'email' => 'required|email|custom_unique:users,email'`

✅ **Custom Messages** - Override error messages in request class

✅ **No Dependencies** - Just add to any validation array

## Error Messages

Custom error messages can be added in request classes:

```php
public function messages()
{
    return [
        'email.custom_unique' => 'This email is already in use.',
        'product_id.custom_exists' => 'The selected product does not exist.',
        'password.custom_password' => 'Password must be stronger.',
    ];
}
```

## Auto Registration

These rules are automatically registered in `app/Providers/AppServiceProvider.php` in the `boot()` method through the `registerCustomValidationRules()` function.

No additional configuration needed - just use them directly!

## Implementation Details

All custom rules are registered via `Validator::extend()` in the service provider:

- **custom_exists** - Checks if value exists in table where `del_flag = 0`
- **custom_unique** - Checks if value doesn't exist in table where `del_flag = 0`
- **custom_password** - Validates password strength with regex patterns

Each rule includes a `Validator::replacer()` for custom error messages.
