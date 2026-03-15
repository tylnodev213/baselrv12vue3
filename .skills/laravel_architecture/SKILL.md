# Laravel Architecture Skill

Information and instructions for adhering to the project's Controller -> Service -> Repository -> Model architecture.

## Architecture Overview

The project follows a strict layered architecture:

1.  **Controller**: Handles HTTP requests, validates input, calls Services, and returns standardized JSON responses.
2.  **Service**: Contains business logic, manages transactions, and calls Repositories.
3.  **Repository**: Interfaces with the database via Eloquent Models, abstracting data access.
4.  **Model**: Represents database entities, defines relationships, casts, and scopes.

## Implementation Rules

### Controllers
- Must extend `BaseController`.
- Should NOT contain business logic.
- Use constructor injection for Services.
- Return responses using `successResponse()`, `errorResponse()`, or `validationErrorResponse()`.

### Services
- Must extend `BaseService`.
- Use constructor injection for Repositories.
- Handle complex business logic and cross-repository operations.
- Return data or throw exceptions for errors.

### Repositories
- Must extend `BaseRepository`.
- All database queries should reside here.
- Avoid using Eloquent models directly in Services or Controllers.

### Models
- Must extend `BaseModel`.
- Define `del_flag` for soft deletes.
- Use global scopes to exclude deleted records by default.

## Example Flow

```php
// Controller
public function store(Request $request) {
    $data = $request->validate([...]);
    $result = $this->service->create($data);
    return $this->successResponse($result);
}

// Service
public function create(array $data) {
    return $this->repository->create($data);
}

// Repository
public function create(array $data) {
    return $this->model->create($data);
}
```
