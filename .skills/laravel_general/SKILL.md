# Laravel General Skill

Information and instructions for general Laravel development best practices and project specifics.

## Project Specifics

### Soft Deletes
The project uses a custom soft delete implementation using a `del_flag` column (enum/int) instead of the default `deleted_at` timestamp.
- **Enabled**: All models extending `BaseModel` have this.
- **Values**: `0` for active, `1` for deleted (typical setup, check `BaseModel` for exact values).
- **Scope**: A global scope automatically filters out `del_flag = 1`.

### Base Classes
Always extend the provided base classes to leverage built-in functionality:
- `BaseModel`: Soft delete management.
- `BaseRepository`: Basic CRUD methods (`all`, `find`, `create`, `update`, `delete`).
- `BaseService`: Wrapper for Repository methods.
- `BaseController`: Standardized JSON responses (`successResponse`, `errorResponse`).

### Routing
- API routes reside in `routes/api.php`.
- Use resource controllers for standard CRUD: `Route::apiResource('products', ProductController::class)`.

### Security
- Always use `Request` objects for input.
- Use Mass Assignment protection (`$fillable`).
- Encrypt sensitive data.
- Use Policy/Gate for authorization.

## Workflow
1. Create Migration.
2. Create Model and set `$fillable`.
3. Create Repository extending `BaseRepository`.
4. Create Service extending `BaseService`.
5. Create Controller extending `BaseController`.
6. Define Route.

## Example: Base Usage

```php
// Model
class Product extends BaseModel {}

// Repository
class ProductRepository extends BaseRepository {}

// Service
class ProductService extends BaseService 
{
    public function __construct(ProductRepository $repo)
    {
        $this->repository = $repo;
    }
}
```
