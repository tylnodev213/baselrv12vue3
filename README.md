# Laravel 12 + Vue 3 Project

Một dự án hoàn chỉnh kết hợp Laravel 12 (Backend) và Vue 3 (Frontend) với các tính năng chuyên nghiệp.

## 🎯 Tính năng chính

### Backend (Laravel 12)
- ✅ **JWT Authentication** - Đăng nhập/Đăng ký với JWT Token + Refresh Token
- ✅ **Soft Delete Model** - Base Model với soft delete sử dụng field `del_flag` (Enum 0: off, 1: on)
- ✅ **Architecture** - Cấu trúc Service → Repository → Controller để tách biệt logic
- ✅ **Base Classes** - BaseModel, BaseRepository, BaseService, BaseController để tái sử dụng
- ✅ **API Response** - Response format thống nhất, validation return status 200 (không 422)

### Frontend (Vue 3)
- ✅ **JWT with Refresh Token** - Xác thực tự động với token refresh
- ✅ **Folder Structure** - Cấu trúc thư mục rõ ràng: components, services, stores, router, composables
- ✅ **Base Components** - BaseInput, BaseForm, BaseTable, BaseModal
- ✅ **Form Handling** - Form → Validation → Modal Confirm → Submit với Loading
- ✅ **Composables** - useForm, useModal, useLoading để tách logic
- ✅ **Pinia Store** - State management cho authentication
- ✅ **Axios Interceptor** - Tự động refresh token khi hết hạn

## 📁 Cấu trúc Dự án

```
laravel12-vue3/
├── app/
│   ├── Models/
│   │   ├── BaseModel.php
│   │   └── User.php
│   ├── Services/
│   │   ├── BaseService.php
│   │   └── UserService.php
│   ├── Repositories/
│   │   ├── BaseRepository.php
│   │   └── UserRepository.php
│   ├── Http/Controllers/
│   │   ├── BaseController.php
│   │   └── AuthController.php
│   └── Enums/
│       └── DeleteFlag.php
├── resources/
│   └── js/
│       ├── components/        # Vue components
│       │   ├── BaseInput.vue
│       │   ├── BaseForm.vue
│       │   ├── BaseTable.vue
│       │   └── BaseModal.vue
│       ├── services/          # API services
│       │   ├── api.js        # Axios config + interceptors
│       │   └── authService.js
│       ├── stores/            # Pinia stores
│       │   └── useAuthStore.js
│       ├── router/            # Vue Router
│       │   └── index.js
│       ├── composables/       # Reusable logic
│       │   ├── useForm.js
│       │   ├── useModal.js
│       │   └── useLoading.js
│       ├── layouts/           # Page layouts
│       │   ├── LayoutDefault.vue
│       │   └── LayoutAuth.vue
│       ├── pages/             # Page components
│       │   ├── LoginPage.vue
│       │   ├── RegisterPage.vue
│       │   └── HomePage.vue
│       ├── App.vue
│       └── main.js
├── config/
│   └── auth.php              # Auth config cho JWT
├── routes/
│   └── api.php               # API routes
├── .env                       # Environment variables
├── .env.example              # Example env
├── vite.config.js            # Vite config
├── package.json              # Frontend dependencies
├── composer.json             # Backend dependencies
└── index.html                # HTML entry point
```

## 🚀 Installation & Setup

### Yêu cầu
- PHP 8.2+
- Node.js 16+ & npm/yarn
- Composer

### Backend Setup

1. **Install PHP dependencies:**
```bash
composer install
```

2. **Tạo migration cho users table:**
```bash
php artisan migrate
```

3. **Publish JWT config (nếu cần):**
```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

4. **Generate JWT secret:**
```bash
php artisan jwt:secret
```

5. **Khởi chạy Laravel server:**
```bash
php artisan serve
```

Server sẽ chạy tại `http://localhost:8000`

### Frontend Setup

1. **Install Node dependencies:**
```bash
npm install
```

hoặc nếu dùng yarn:
```bash
yarn install
```

2. **Khởi chạy Vite dev server:**
```bash
npm run dev
```

Frontend sẽ chạy tại `http://localhost:5173`

3. **Build cho production:**
```bash
npm run build
```

## 🔐 Authentication Flow

### Login/Register
1. User nhập thông tin vào Form (BaseInput → BaseForm)
2. Frontend validate client-side
3. Submit API request → Backend validate
4. Backend return status 200 với error messages hoặc tokens
5. Frontend display validation errors dưới Input
6. Nếu thành công: modal confirm → lưu tokens + redirect

### Token Refresh
- Access Token có TTL 60 phút (configurable trong `.env`)
- Refresh Token có TTL 7 ngày
- Axios interceptor tự động refresh token khi 401
- Nếu refresh thất bại → logout + redirect login

## 📝 Sử dụng cấu trúc Service → Repository → Controller

### Ví dụ: Tạo Product CRUD

#### 1. Tạo Model (app/Models/Product.php):
```php
<?php
namespace App\Models;

class Product extends BaseModel
{
    protected $fillable = ['name', 'description', 'price', 'del_flag'];
}
```

#### 2. Tạo Repository (app/Repositories/ProductRepository.php):
```php
<?php
namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new Product();
    }
}
```

#### 3. Tạo Service (app/Services/ProductService.php):
```php
<?php
namespace App\Services;

use App\Repositories\BaseRepository;
use App\Repositories\ProductRepository;

class ProductService extends BaseService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->repository = $productRepository;
    }

    protected function getRepository(): BaseRepository
    {
        return $this->productRepository;
    }
}
```

#### 4. Tạo Controller (app/Http/Controllers/ProductController.php):
```php
<?php
namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\BaseService;

class ProductController extends BaseController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->service = $productService;
    }

    protected function getService(): BaseService
    {
        return $this->productService;
    }
}
```

#### 5. Register routes (routes/api.php):
```php
Route::resource('products', ProductController::class)->middleware('auth:api');
```

## 🎨 Sử dụng Base Components

### BaseInput
```vue
<BaseInput
  v-model="form.name"
  type="text"
  label="Tên sản phẩm"
  placeholder="Nhập tên sản phẩm"
  :error="form.errors.name"
  required
  @blur="handleFieldTouched('name')"
/>
```

### BaseForm
```vue
<BaseForm
  :submit-text="'Lưu'"
  :on-submit="handleSubmit"
  :requires-confirmation="true"
  :confirm-message="'Bạn có chắc muốn lưu?'"
  @submit="handleFormSubmit"
>
  <!-- Các input fields -->
</BaseForm>
```

### BaseTable
```vue
<BaseTable
  :columns="[
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Tên' },
    { key: 'price', label: 'Giá' }
  ]"
  :rows="products"
  @edit="handleEdit"
  @delete="handleDelete"
>
  <template #cell-price="{ row }">
    {{ formatPrice(row.price) }}
  </template>
</BaseTable>
```

### BaseModal
```vue
<BaseModal
  :is-open="modal.isOpen"
  :title="modal.title"
  :message="modal.message"
  :type="modal.type"
  :is-loading="modal.isLoading"
  @confirm="modal.confirm"
  @cancel="modal.cancel"
/>
```

## 🧩 Sử dụng Composables

### useForm
```js
import { useForm } from '@/composables/useForm';

const form = useForm();

// Set form data
form.setFormData({ name: '', email: '' });

// Validate
const isValid = form.validateForm({
  name: [(value) => !value ? 'Tên không được để trống' : null],
  email: [(value) => !value ? 'Email không được để trống' : null],
});

// Get/Set field errors
form.setFieldError('email', 'Email không hợp lệ');
const error = form.getFieldError('email');

// Reset
form.resetForm();
```

### useModal
```js
import { useModal } from '@/composables/useModal';

const modal = useModal();

// Show confirm
modal.showConfirm('Bạn có chắc?', async () => {
  // Do something
});

// Show success
modal.showSuccess('Thành công!');

// Show error
modal.showError('Có lỗi xảy ra!');
```

### useLoading
```js
import { useLoading } from '@/composables/useLoading';

const { isLoading, startLoading, stopLoading } = useLoading();

startLoading('Đang tải...');
// Do something
stopLoading();
```

## 📤 API Response Format

Tất cả API responses đều trả về format sau:

### Success Response (Status 200)
```json
{
  "success": true,
  "message": "Records retrieved successfully",
  "data": {...}
}
```

### Error Response (Status 200)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": "Email not found",
    "password": "Password is incorrect"
  }
}
```

## 🔧 Cấu hình Environment

### .env file
```
APP_NAME=Laravel12Vue3
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

JWT_SECRET=your_secret_key
JWT_TTL=60
JWT_REFRESH_TTL=20160

VITE_API_URL=http://localhost:8000/api
```

## 📚 Thêm tài liệu

- [Laravel Documentation](https://laravel.com/docs)
- [Vue 3 Documentation](https://vuejs.org)
- [Pinia Documentation](https://pinia.vuejs.org)
- [JWT Auth Package](https://github.com/tymondesigns/jwt-auth)
- [Vite Documentation](https://vitejs.dev)

## 📄 License

This project is open source and available under the MIT license.
