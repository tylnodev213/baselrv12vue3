# ⚡ Quick Start Guide

## Option 1: Chạy với Docker (Recommended) 🐳

### Development Setup (All-in-one)
```bash
# 1 command để chạy cả backend + frontend
docker-compose -f docker-compose-dev.yml up
```

- Tất cả chạy trong 1 container
- Auto-run migrations
- Auto-start Vite dev server
- Hot reload cho cả backend và frontend
- Truy cập: http://localhost:8000

### Production Setup (Separate services)
```bash
# Build images
docker-compose build

# Start services
docker-compose up -d

# Run migrations
docker-compose exec php-fpm php artisan migrate
```

- Backend: http://localhost:8000
- Frontend: http://localhost:5173

**📖 Chi tiết xem [DOCKER.md](./DOCKER.md)**

---

## Option 2: Chạy Local (Without Docker)

### Terminal 1: Backend Setup

```bash
# 1. Cài dependencies
composer install

# 2. Copy .env
cp .env.example .env

# 3. Generate keys
php artisan key:generate
php artisan jwt:secret

# 4. Tạo database
touch database/database.sqlite

# 5. Chạy migrations
php artisan migrate

# 6. Start server
php artisan serve
```

**Server chạy tại:** http://localhost:8000

### Terminal 2: Frontend Setup

```bash
# 1. Cài dependencies
npm install

# 2. Start dev server
npm run dev
```

**Frontend chạy tại:** http://localhost:5173

## ✅ Testing

### Test Login
1. Truy cập http://localhost:5173
2. Click "Đăng ký ngay"
3. Nhập info: Name, Email, Password
4. Click "Đăng ký"
5. Sẽ chuyển hướng sang trang chủ

### Test Products
1. Từ trang chủ, click "Sản phẩm" trên navbar
2. Click "+ Thêm sản phẩm"
3. Nhập: Tên, Giá, Mô tả
4. Click "Tạo mới"
5. Xác nhận modal
6. Sản phẩm được thêm vào danh sách

## 📁 Key Files

### Backend
- `app/Models/User.php` - User model
- `app/Models/Product.php` - Product model (example)
- `app/Services/UserService.php` - User service
- `app/Repositories/UserRepository.php` - User repository
- `app/Http/Controllers/AuthController.php` - Auth API
- `routes/api.php` - API routes
- `.env` - Environment variables

### Frontend
- `resources/js/pages/LoginPage.vue` - Login page
- `resources/js/pages/ProductPage.vue` - Products CRUD (example)
- `resources/js/stores/useAuthStore.js` - Auth store
- `resources/js/router/index.js` - Routes
- `resources/js/services/api.js` - API config
- `resources/js/components/BaseForm.vue` - Base form component

## 🔧 Customization

### Thêm Model mới (e.g., Category)

1. **Create Model:**
```bash
php artisan make:model Category --migration
```

2. **Create Repository:**
```php
// app/Repositories/CategoryRepository.php
class CategoryRepository extends BaseRepository {
    protected function getModel() {
        return new Category();
    }
}
```

3. **Create Service:**
```php
// app/Services/CategoryService.php
class CategoryService extends BaseService {
    // ...
}
```

4. **Create Controller:**
```php
// app/Http/Controllers/CategoryController.php
class CategoryController extends BaseController {
    // ...
}
```

5. **Add Routes:**
```php
// routes/api.php
Route::resource('categories', CategoryController::class)->middleware('auth:api');
```

6. **Create API Service (Vue):**
```js
// resources/js/services/categoryService.js
export const categoryService = {
  getAll: () => api.get('/categories'),
  create: (data) => api.post('/categories', data),
  // ...
}
```

7. **Create Store (Pinia):**
```js
// resources/js/stores/useCategoryStore.js
export const useCategoryStore = defineStore('category', () => {
  // ...
});
```

8. **Create Page:**
```vue
<!-- resources/js/pages/CategoryPage.vue -->
<template>
  <!-- Component here -->
</template>
```

9. **Add Route:**
```js
// resources/js/router/index.js
{
  path: 'categories',
  name: 'categories',
  component: CategoryPage,
}
```

## 🐛 Troubleshooting

### Lỗi: "npm: command not found"
- Cài Node.js từ https://nodejs.org

### Lỗi: "composer: command not found"
- Cài Composer từ https://getcomposer.org

### Lỗi: "CORS Error"
- Đảm bảo Laravel server chạy ở port 8000
- Kiểm tra `.env` file có `APP_URL=http://localhost:8000`

### Lỗi: "Cannot find module '@/...'"
- Check `vite.config.js` alias
- Restart Vite server (`npm run dev`)

### Lỗi: "Unauthorized" khi call API
- Đảm bảo đã login
- Check token lưu trong localStorage
- Check browser console cho errors

## 📚 Next Steps

1. Thêm nhiều models (Users, Categories, Orders, etc.)
2. Thêm validation phức tạp
3. Thêm pagination/filtering
4. Thêm search functionality
5. Setup email notifications
6. Add file upload
7. Setup testing
8. Deploy to production

## 💻 IDE Setup

### VS Code Extensions
- Laravel Extension Pack
- Vue 3 Snippets
- Prettier - Code formatter
- ESLint
- Thunder Client (for API testing)

### Useful Commands

```bash
# Laravel
php artisan tinker              # Interactive console
php artisan make:model Name     # Create model
php artisan migrate:refresh     # Reset database
php artisan db:seed             # Run seeders

# Vue/Node
npm run build                   # Build for production
npm run preview                 # Preview production build
```

## 🚀 Production Deployment

### Backend (Laravel)
```bash
# 1. Build
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache

# 2. Environment
# Set .env to production
APP_ENV=production
APP_DEBUG=false
```

### Frontend (Vue)
```bash
# 1. Build
npm run build

# 2. Deploy dist/ folder to web server
# or configure Laravel to serve the dist folder
```

## 📖 Documentation

- [Full Setup Guide](./SETUP.md)
- [Docker Setup Guide](./DOCKER.md)
- [Architecture Guide](./ARCHITECTURE.md)
- [README with examples](./README.md)

---

**Happy Coding!** 🎉

Nếu có bất kỳ câu hỏi, hãy xem các file documentation
