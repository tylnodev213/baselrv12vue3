# Architecture Documentation

## 🏗️ Backend Architecture (Laravel)

### Service → Repository → Controller Pattern

```
┌─────────────────────┐
│    Controller       │
│   (HTTP Request)    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│     Service         │
│ (Business Logic)    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│   Repository        │
│ (Data Access)       │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│      Model          │
│  (Database Entity)  │
└─────────────────────┘
```

### Controller
- Xử lý HTTP requests/responses
- Validate input (tham khảo Service)
- Gọi Service để xử lý logic
- Trả về JSON response

```php
class ProductController extends BaseController {
    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }
    
    public function store(Request $request) {
        // Validate và gọi Service
        $product = $this->productService->create($request->all());
        return $this->successResponse($product);
    }
}
```

### Service
- Chứa business logic
- Gọi Repository để lấy data
- Xử lý các validation phức tạp
- Quản lý các transaction
- Gọi các service khác nếu cần

```php
class ProductService extends BaseService {
    public function __construct(ProductRepository $repo) {
        $this->repository = $repo;
    }
    
    public function create(array $data) {
        // Business logic
        $product = $this->repository->create($data);
        // Có thể gọi service khác, send email, etc.
        return $product;
    }
}
```

### Repository
- Tất cả các database queries
- Trừu tượng hóa data access
- Tái sử dụng qua các model
- Có query builder methods

```php
class ProductRepository extends BaseRepository {
    public function getByPriceRange($min, $max) {
        return Product::whereBetween('price', [$min, $max])->get();
    }
}
```

### Model
- Định nghĩa database table
- Relationships (nếu có)
- Casts, accessors, mutators
- Scopes

### Base Classes

#### BaseModel
```php
class Product extends BaseModel {
    // Tự động có del_flag
    // Tự động filter deleted records
}
```

**Features:**
- Soft delete với `del_flag` enum
- Global scope: loại bỏ deleted records
- Methods: `softDelete()`, `restore()`, `isDeleted()`

#### BaseRepository
**Methods có sẵn:**
- `all()` - Lấy tất cả
- `find($id)` - Lấy theo ID
- `create(array $data)` - Tạo mới
- `update($id, array $data)` - Cập nhật
- `delete($id)` - Xóa mềm
- `restore($id)` - Khôi phục
- `forceDelete($id)` - Xóa vĩnh viễn
- `paginate($perPage)` - Phân trang
- `where(array $conditions)` - Query điều kiện

#### BaseService
- Wrapper quanh Repository
- Các methods giống BaseRepository
- Có thể thêm business logic methods

#### BaseController
**CRUD Methods:**
- `index()` - GET /resource
- `show($id)` - GET /resource/{id}
- `store(Request $request)` - POST /resource
- `update(Request $request, $id)` - PUT /resource/{id}
- `destroy($id)` - DELETE /resource/{id}

**Helper Methods:**
- `successResponse($data)` - Return 200 success
- `errorResponse($message)` - Return 200 error
- `validationErrorResponse($errors)` - Return validation errors

## 🎨 Frontend Architecture (Vue 3)

### Project Structure

```
resources/js/
├── components/          # Reusable UI components
├── pages/              # Full page components
├── layouts/            # Layout wrappers
├── services/           # API services
├── stores/             # Pinia stores
├── router/             # Vue Router config
├── composables/        # Reusable logic hooks
├── App.vue
└── main.js
```

### Layer Architecture

```
┌──────────────────────────┐
│      Pages/Views         │
│   (User Interface)       │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│     Components           │
│   (UI Composition)       │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│    Composables           │
│   (Business Logic)       │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│   Services (API)         │
│  (HTTP Requests)         │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│   Axios Instance         │
│  (Network Layer)         │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│    Backend API           │
└──────────────────────────┘
```

### Components

#### Base Components
Sử dụng lại cho nhiều pages:

**BaseInput**
- Props: `modelValue`, `type`, `label`, `error`, `required`
- Events: `update:modelValue`, `blur`, `focus`

**BaseForm**
- Props: `onSubmit`, `submitText`, `showCancel`
- Features: Loading state, confirmation modal
- Slots: Default slot cho form fields

**BaseTable**
- Props: `columns`, `rows`, `showActions`
- Events: `edit`, `delete`
- Slots: Custom cell rendering

**BaseModal**
- Props: `isOpen`, `title`, `message`, `type`, `isLoading`
- Events: `confirm`, `cancel`
- Features: Loading state, multiple types (info, confirm, success, error)

#### Form Components
- Cấu thành từ BaseComponents
- Bind vào form data từ composable `useForm`
- Display validation errors từ API

### Services

#### api.js
- Axios instance configuration
- Request interceptor: Thêm JWT token
- Response interceptor: Refresh token nếu 401

```js
api.interceptors.request.use(config => {
  // Thêm token vào header
  config.headers.Authorization = `Bearer ${token}`;
  return config;
});

api.interceptors.response.use(null, error => {
  // Nếu 401, refresh token
});
```

#### authService.js
```js
export const authService = {
  register(payload),
  login(payload),
  logout(),
  refreshToken(refreshToken),
  me(),
}
```

#### productService.js (Example)
```js
export const productService = {
  getAll(page, perPage),
  getById(id),
  create(payload),
  update(id, payload),
  delete(id),
}
```

### Pinia Stores

#### useAuthStore
```js
// State
user, accessToken, refreshToken, isAuthenticated, isLoading

// Actions
register(), login(), logout(), fetchUser(), checkAuth()

// Getters
getAccessToken(), getRefreshToken()
```

#### useProductStore
```js
// State
products, currentProduct, isLoading, currentPage, totalPages

// Actions
fetchProducts(), createProduct(), updateProduct(), deleteProduct()

// Getters
totalProducts, isEmpty
```

### Composables

#### useForm
Quản lý form state và validation

```js
const form = useForm();
form.formData      // Reactive form data
form.errors        // Form errors
form.setFieldError()
form.validateForm()
form.resetForm()
```

#### useModal
Quản lý modal state

```js
const modal = useModal();
modal.isOpen
modal.openModal({ title, message, type, onConfirm })
modal.showConfirm(message, callback)
modal.showSuccess(message)
modal.showError(message)
```

#### useLoading
Quản lý global loading state

```js
const { isLoading, startLoading, stopLoading } = useLoading();
```

### Router Configuration

```js
const routes = [
  {
    path: '/',
    component: LayoutDefault,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'home', component: HomePage },
      { path: 'products', name: 'products', component: ProductPage },
    ]
  },
  {
    path: '/auth',
    component: LayoutAuth,
    children: [
      { path: 'login', name: 'login', component: LoginPage },
      { path: 'register', name: 'register', component: RegisterPage },
    ]
  }
];
```

**Navigation Guards:**
- Kiểm tra `requiresAuth` meta
- Kiểm tra `requiresGuest` meta
- Redirect đến login nếu unauthorized
- Load user info khi first load

### Form Flow

```
┌────────────────────┐
│   User Input       │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│  Client Validate   │ (useForm)
│  (Validation)      │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ Show Errors Below  │ (BaseInput)
│ Input Field        │
└─────────┬──────────┘
          │ (If valid)
          ▼
┌────────────────────┐
│  Show Modal        │
│  Confirm Dialog    │ (BaseModal)
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│  Submit API        │ (Show Loading)
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│  Response Status?  │
└─────────┬──────────┘
      ┌───┴────┐
      │         │
     ✓         ✗
    │         │
    ▼         ▼
┌──────┐  ┌───────────┐
│Done  │  │ API Error │
└──────┘  │ Display   │
          └───────────┘
```

## 🔐 Authentication Flow

### Login
```
Frontend                    Backend
   │                           │
   │── POST /auth/login ─────► │
   │   {email, password}       │
   │                           │ Validate
   │◄─ 200 {tokens} ────────── │
   │
   │ Store tokens in localStorage
   │ Setup Authorization header
   │ Redirect to /
   │
   └─ GET /auth/me ────────────►│
                                │ Verify token
                     ◄─ 200 {user data}
```

### Token Refresh
```
When Access Token expires (401):

Frontend                    Backend
   │                           │
   │ Axios: 401 Error          │
   │                           │
   │ POST /auth/refresh-token──►│
   │ {refresh_token}           │
   │                           │ Validate
   │◄─ 200 {new_token} ────────│
   │
   │ Update Authorization header
   │ Retry original request
   │
   └─ Original Request ────────►│
                    ◄─ 200 Success
```

## 📋 Request/Response Format

### API Response Format

**Success:**
```json
{
  "success": true,
  "message": "Success message",
  "data": { /* data */ }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": { /* field errors */ }
}
```

**Validation Error:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": "Email is invalid",
    "password": "Password is too short"
  }
}
```

### JWT Tokens

**Access Token:**
- TTL: 60 minutes
- Used: Authorization header
- Format: Bearer {token}

**Refresh Token:**
- TTL: 14 days
- Used: Refresh token endpoint
- Stored: localStorage

## 🔄 Data Flow Example: Create Product

```
ProductPage Component
        │
        │ Bind form to useForm()
        │ Input: name, price, description
        │
        ▼
BaseForm Component
        │
        │ On submit:
        │ 1. validateForm()
        │ 2. Show modal confirm
        │ 3. If confirmed, call productService.create()
        │
        ▼
productService.js
        │
        │ api.post('/api/products', payload)
        │
        ▼
Axios Interceptor
        │
        │ Add Authorization header
        │ POST http://localhost:8000/api/products
        │
        ▼
ProductController@store
        │
        │ 1. Validate input
        │ 2. Call ProductService->create()
        │
        ▼
ProductService@create
        │
        │ 1. Business logic
        │ 2. Call ProductRepository->create()
        │
        ▼
ProductRepository@create
        │
        │ 1. Product::create($data)
        │
        ▼
Product Model
        │
        │ Save to database
        │
        ▼
Response back to Frontend
        │
        │ Update useProductStore
        │ Close form modal
        │ Show success message
        │
        ▼
ProductPage re-renders
        │
        │ New product appears in table
```

## 🏥 Error Handling

### Frontend Error Handling
1. **Network Errors**: Try-catch blocks
2. **Validation Errors**: Display under each field
3. **API Errors**: Show modal error
4. **Token Errors**: Refresh token or logout
5. **404/500**: Show error page

### Backend Error Handling
1. **Validation**: Return 200 with errors
2. **Authentication**: Return 401
3. **Authorization**: Return 403
4. **Not Found**: Return 200 with success=false
5. **Server Error**: Return 500 with message

## 🎯 Best Practices

### Backend
1. Always use Repository for data access
2. Keep Service layer thin (business logic only)
3. Use validation in Controller
4. Always return same response format
5. Use transactions for multiple updates
6. Handle all errors gracefully

### Frontend
1. Use composables for reusable logic
2. Keep components focused and small
3. Use Pinia stores for global state
4. Always show loading states
5. Validate before sending to backend
6. Handle errors gracefully
7. Don't expose backend URLs in components

### Database
1. Always use migrations
2. Add indexes for frequently queried fields
3. Use soft delete (del_flag) for important data
4. Keep database normalized
5. Add timestamps to tables

## 📈 Performance Optimization

### Frontend
- Code splitting with async components
- Lazy load images
- Cache API responses
- Pagination for large lists
- Debounce search inputs

### Backend
- Add database indexes
- Use pagination
- Cache frequent queries
- Use SELECT specific columns
- Eager load relationships
- Use database transactions

### Network
- Enable gzip compression
- Minify CSS/JS
- CDN for static assets
- Use HTTP/2 or HTTP/3
- Optimize images
