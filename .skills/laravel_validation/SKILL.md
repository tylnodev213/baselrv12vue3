# Laravel Validation Skill

Information and instructions for implementing standardized validation in the project.

## Validation Principles

Validation should be consistent, secure, and provide helpful feedback to the user.

### Where to Validate
- **Primary**: Controllers should handle request validation.
- **Complex**: Complex business logic validation (e.g., state-dependent rules) should be handled in the Service layer.

### Methods
- **Inline Validation**: Use `$request->validate([...])` for simple forms.
- **Form Requests**: Use dedicated FormRequest classes (`php artisan make:request`) for complex or reusable validation rules.

### Standard Response Format
Validation errors must be returned in the project's standard response format:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

## Best Practices
- Use `unique` with ignore rules for updates.
- Sanitize input before validation if necessary.
- Use customs messages for better UX.
- Leverage Laravel's built-in rules (email, numeric, exists, etc.).

## Example: Form Request

```php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $this->user_id,
        'role' => 'required|in:admin,user',
    ];
}
```

## Example: Controller Validation

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|unique:posts|max:255',
        'body' => 'required',
    ]);

    $post = $this->service->create($validated);
    return $this->successResponse($post);
}
```
