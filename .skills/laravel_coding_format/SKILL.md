# Laravel Coding Format Skill

Information and instructions for maintaining consistent code style across the Laravel project.

## Coding Standards

The project follows **PSR-12** standards with some specific local conventions.

### Naming Conventions
- **Classes**: PascalCase (e.g., `UserController`, `PaymentService`).
- **Methods**: camelCase (e.g., `storeData()`, `getUserById()`).
- **Variables**: camelCase (e.g., `userData`, `isProcessed`).
- **Constants**: UPPER_SNAKE_CASE (e.g., `STATUS_ACTIVE`).
- **Files**: PascalCase matching the class name.
- **Routes**: kebab-case (e.g., `/user-profile`).
- **Database Tables**: snake_case (e.g., `user_profiles`).

### Formatting Rules
- Use 4 spaces for indentation.
- Opening braces for classes and methods must be on the next line.
- Opening braces for control structures must be on the same line.
- Always specify visibility (public, protected, private) for properties and methods.
- Use type hints for method arguments and return types where possible.

### PHP Specifics
- Use short array syntax `[]`.
- Prefer strict comparisons `===` and `!==`.
- Organize imports alphabetically and remove unused ones.

## Example

```php
namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getUser(int $id): JsonResponse
    {
        $user = $this->userService->find($id);
        return $this->successResponse($user);
    }
}
```
