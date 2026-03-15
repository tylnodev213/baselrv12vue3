# Laravel Optimization Skill

Information and instructions for writing performance-optimized Laravel code.

## Performance Best Practices

### Database Optimization
- **Eager Loading**: Always use `with()` to prevent N+1 query problems.
- **Select Specific Columns**: Use `select(['id', 'name'])` instead of fetching all columns (`*`).
- **Pagination**: Use `paginate()` or `simplePaginate()` for large datasets.
- **Indexing**: Ensure foreign keys and frequently searched columns are indexed in migrations.
- **Chunking**: Use `chunk()` or `cursor()` when processing large result sets to save memory.

### Code Efficiency
- **Caching**: Use Laravel's Cache facade for expensive operations or frequently accessed static data.
- **Queueing**: Offload heavy tasks (email sending, image processing) to background jobs.
- **Lazy Collections**: Use `LazyCollection` for memory-efficient processing of large arrays.

### Eloquent vs Query Builder
- Use Eloquent for standard CRUD and relationship management.
- Use Query Builder for complex, performance-critical raw queries.

## Example: Eager Loading

```php
// BAD: Causes N+1 queries
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->user->name;
}

// GOOD: Single query with join
$posts = Post::with('user')->get();
foreach ($posts as $post) {
    echo $post->user->name;
}
```

## Example: Specific Select

```php
// GOOD: Reducing memory usage
$users = User::select(['id', 'email'])->paginate(15);
```
