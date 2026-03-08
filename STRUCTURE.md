# Project Structure

## Directory Overview

```
laravel12-vue3/
├── app/                          # Application code
│   ├── Console/
│   │   └── Commands/             # Custom artisan commands
│   ├── Exceptions/
│   │   └── Handler.php           # Exception handler
│   ├── Http/
│   │   ├── Controllers/          # API controllers
│   │   │   ├── BaseController.php
│   │   │   ├── AuthController.php
│   │   │   └── ProductController.php
│   │   ├── Middleware/           # HTTP middleware
│   │   ├── Requests/             # Form request classes
│   ├── Jobs/                     # Queueable jobs
│   ├── Mail/                     # Mailable classes
│   ├── Models/                   # Eloquent models
│   │   ├── BaseModel.php
│   │   ├── User.php
│   │   └── Product.php
│   ├── Enums/                    # PHP enums
│   │   └── DeleteFlag.php
│   ├── Repositories/             # Data repositories
│   │   ├── BaseRepository.php
│   │   ├── UserRepository.php
│   │   └── ProductRepository.php
│   ├── Services/                 # Business logic
│   │   ├── BaseService.php
│   │   ├── UserService.php
│   │   └── ProductService.php
│   └── Providers/                # Service providers
│       └── AppServiceProvider.php
│
├── bootstrap/                     # Application bootstrap
│   ├── app.php                   # Bootstrap the application
│   └── cache/                    # Bootstrap cache files
│
├── config/                       # Configuration files
│   ├── app.php                   # Application configuration
│   ├── auth.php                  # Authentication configuration
│   ├── cache.php                 # Cache configuration
│   ├── database.php              # Database configuration
│   └── logging.php               # Logging configuration
│
├── database/                     # Database files
│   ├── migrations/               # Schema migrations
│   │   ├── 2024_01_01_000000_create_users_table.php
│   │   └── 2024_01_02_000000_create_products_table_example.php
│   ├── seeders/                  # Seeders
│   ├── factories/                # Factories
│   └── database.sqlite           # SQLite database
│
├── public/                       # Web accessible files
│   ├── .htaccess                # URL rewriting rules
│   ├── index.php                # Application entry point
│   └── favicon.ico              # Favicon
│
├── resources/                    # Frontend resources
│   └── js/                       # Vue 3 frontend
│       ├── components/           # Reusable Vue components
│       │   ├── BaseInput.vue
│       │   ├── BaseForm.vue
│       │   ├── BaseTable.vue
│       │   └── BaseModal.vue
│       ├── pages/                # Page components
│       │   ├── LoginPage.vue
│       │   ├── RegisterPage.vue
│       │   ├── HomePage.vue
│       │   └── ProductPage.vue
│       ├── layouts/              # Layout components
│       │   ├── LayoutDefault.vue
│       │   └── LayoutAuth.vue
│       ├── stores/               # Pinia stores
│       │   ├── useAuthStore.js
│       │   └── useProductStore.js
│       ├── services/             # API services
│       │   ├── api.js            # Axios configuration
│       │   ├── authService.js
│       │   └── productService.js
│       ├── router/               # Vue Router
│       │   └── index.js
│       ├── composables/          # Reusable composables
│       │   ├── useForm.js
│       │   ├── useModal.js
│       │   └── useLoading.js
│       ├── App.vue               # Root component
│       └── main.js               # Application entry
│
├── routes/                       # Route definitions
│   ├── api.php                   # API routes
│   └── console.php               # Console routes
│
├── storage/                      # Application storage
│   ├── app/                      # Application storage
│   ├── framework/                # Framework storage
│   │   ├── cache/                # Cache files
│   │   ├── sessions/             # Session files
│   │   └── views/                # Compiled views
│   └── logs/                     # Application logs
│
├── tests/                        # Test files
│   ├── Unit/                     # Unit tests
│   └── Feature/                  # Feature tests
│
├── docker/                       # Docker files
│   ├── nginx.conf               # Frontend Nginx config
│   ├── nginx-backend.conf       # Backend Nginx config
│   └── apache.conf              # Apache config (dev)
│
├── artisan                       # Laravel CLI
├── composer.json                 # PHP dependencies
├── composer.lock                 # Locked PHP dependencies
├── package.json                  # Node dependencies
├── package-lock.json             # Locked Node dependencies
├── vite.config.js                # Vite configuration
├── postcss.config.js             # PostCSS configuration
├── phpunit.xml                   # PHPUnit configuration
├── .env                          # Environment variables
├── .env.example                  # Example environment variables
├── .env.testing                  # Test environment variables
├── .dockerignore                 # Docker ignore file
├── .gitignore                    # Git ignore file
│
├── Dockerfile                    # All-in-one dev container
├── Dockerfile.backend            # Production backend container
├── Dockerfile.frontend           # Production frontend container
├── docker-compose.yml            # Production compose file
├── docker-compose-dev.yml        # Development compose file
│
├── index.html                    # Vite HTML entry
├── QUICKSTART.md                 # Quick start guide
├── SETUP.md                      # Setup guide
├── DOCKER.md                     # Docker guide
├── ARCHITECTURE.md               # Architecture documentation
└── README.md                     # Project README
```

## Key Directories Explained

### `/app`
Contains all application code:
- **Models**: Eloquent ORM models
- **Controllers**: API controllers
- **Services**: Business logic layer
- **Repositories**: Data access layer
- **Middleware**: HTTP middleware
- **Enums**: Enum classes

### `/bootstrap`
Bootstrap files to initialize the application:
- `app.php` - Creates and returns the application instance

### `/config`
Configuration files for various services:
- `app.php` - General application config
- `database.php` - Database connection config
- `auth.php` - Authentication config
- `cache.php` - Caching config

### `/database`
Database-related files:
- **migrations/** - Database schema migrations
- **seeders/** - Database seeders for fake data
- **factories/** - Model factories for testing

### `/public`
Web-accessible directory:
- `index.php` - Single entry point for all requests
- `.htaccess` - URL rewriting for Apache

### `/resources/js`
Vue 3 frontend application:
- **components/** - Reusable Vue components
- **pages/** - Full page components
- **stores/** - Pinia state management
- **services/** - API communication
- **router/** - Vue Router configuration
- **composables/** - Reusable composition functions

### `/routes`
Route definitions:
- `api.php` - API routes (REST endpoints)
- `console.php` - Console/Artisan commands

### `/storage`
Application-generated files:
- **logs/** - Application log files
- **cache/** - Cached data
- **sessions/** - Session data
- **app/** - Uploaded files

### `/tests`
Test files:
- **Unit/** - Unit tests for individual components
- **Feature/** - Integration tests

## Architecture Patterns

### Layers
```
Controllers (HTTP Layer)
    ↓
Services (Business Logic)
    ↓
Repositories (Data Access)
    ↓
Models (Database)
```

### Models
- Extend `BaseModel` for automatic soft delete functionality
- Define fillable properties and relationships

### Repositories
- Extend `BaseRepository` for automatic CRUD methods
- Add custom query methods as needed

### Services
- Extend `BaseService` for business logic
- Call repositories for data access
- Implement application-specific logic

### Controllers
- Extend `BaseController` for automatic response formatting
- Use services for business logic
- Handle HTTP request/response

## Vue 3 Structure

### Components
Reusable UI components:
- Base components for forms, inputs, tables, modals
- Feature components for specific functionality

### Stores (Pinia)
State management:
- `useAuthStore` - Authentication state
- `useProductStore` - Product data state

### Services
API communication:
- `api.js` - Configured Axios instance with interceptors
- `authService.js` - Authentication API calls
- `productService.js` - Product API calls

### Composables
Reusable logic:
- `useForm()` - Form state management
- `useModal()` - Modal dialogs
- `useLoading()` - Loading states

### Router
Route definitions and guards for page navigation

## Environment Variables

See `.env.example` for available configuration options.

Key variables:
- `APP_ENV` - Environment (local, production, testing)
- `DB_CONNECTION` - Database type (sqlite, mysql, pgsql)
- `JWT_SECRET` - JWT token secret
- `VITE_API_URL` - Frontend API URL

## Development Workflow

1. Create a new Model in `/app/Models`
2. Create a migration in `/database/migrations`
3. Create a Repository in `/app/Repositories`
4. Create a Service in `/app/Services`
5. Create a Controller in `/app/Http/Controllers`
6. Add routes in `/routes/api.php`
7. Create API Service in `/resources/js/services`
8. Create Store in `/resources/js/stores`
9. Create Vue Component in `/resources/js/pages`

## File Naming Conventions

- **Models**: Singular, PascalCase (e.g., `Product.php`)
- **Controllers**: Controller suffix, PascalCase (e.g., `ProductController.php`)
- **Services**: Service suffix, PascalCase (e.g., `ProductService.php`)
- **Repositories**: Repository suffix, PascalCase (e.g., `ProductRepository.php`)
- **Vue Components**: PascalCase (e.g., `ProductPage.vue`)
- **Stores**: `use` prefix + Store name (e.g., `useProductStore.js`)
- **Services (JS)**: Lowercase with service suffix (e.g., `productService.js`)

## Running Commands

```bash
# Laravel Artisan commands
php artisan migrate              # Run migrations
php artisan tinker              # Interactive shell
php artisan make:model Name     # Create a model
php artisan make:migration Name # Create a migration

# NPM commands
npm run dev                      # Start dev server
npm run build                    # Build for production
npm install                      # Install dependencies

# Docker commands
docker-compose up               # Start containers
docker-compose down             # Stop containers
docker-compose logs -f          # View logs
```

---

For more information, see:
- [Setup Guide](./SETUP.md)
- [Docker Guide](./DOCKER.md)
- [Architecture Guide](./ARCHITECTURE.md)
- [Quick Start](./QUICKSTART.md)
