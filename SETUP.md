# SETUP & INSTALLATION GUIDE

## 📋 Yêu cầu hệ thống

- **PHP**: 8.2 hoặc cao hơn
- **Node.js**: 16 hoặc cao hơn
- **npm** hoặc **yarn**: Quản lý package cho Node.js
- **Composer**: Quản lý package cho PHP
- **SQLite** hoặc các database khác

## 🔧 Cài đặt & Chạy

### Bước 1: Clone/Download Project
```bash
# Vào folder project
cd laravel12-vue3
```

### Bước 2: Cài đặt Backend (Laravel)

1. **Cài các package PHP:**
```bash
composer install
```

2. **Sao chép file .env:**
```bash
cp .env.example .env
```

3. **Tạo app key:**
```bash
php artisan key:generate
```

4. **Tạo JWT secret:**
```bash
php artisan jwt:secret
```

5. **Tạo database (SQLite):**
```bash
touch database/database.sqlite
```

6. **Chạy migrations:**
```bash
php artisan migrate
```

7. **Khởi chạy Laravel development server:**
```bash
php artisan serve
```

Server sẽ chạy tại: **http://localhost:8000**

### Bước 3: Cài đặt Frontend (Vue 3)

1. **Cài các package Node:**
```bash
npm install
```

hoặc nếu dùng yarn:
```bash
yarn install
```

2. **Khởi chạy Vite development server:**
```bash
npm run dev
```

hoặc:
```bash
yarn dev
```

Frontend sẽ chạy tại: **http://localhost:5173**

## ✅ Kiểm tra cài đặt

### Backend API
```bash
# Test API
curl http://localhost:8000/api/auth/me
# Sẽ trả về lỗi 401 vì chưa authenticate
```

### Frontend
Mở browser tại `http://localhost:5173`
- Bạn sẽ thấy trang Login
- Có thể đăng ký tài khoản mới hoặc dùng tài khoản đã tồn tại

## 📝 Tạo tài khoản test

### Cách 1: Sử dụng API
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Cách 2: Dùng Frontend
- Truy cập http://localhost:5173
- Click vào "Đăng ký ngay"
- Điền thông tin và submit

## 🗄️ Cấu trúc Database

### Users Table
```
id (bigint, primary key)
name (string)
email (string, unique)
password (string)
del_flag (tinyint, default: 0)
created_at (timestamp)
updated_at (timestamp)
```

### Products Table (Example)
```
id (bigint, primary key)
name (string)
description (text)
price (decimal)
del_flag (tinyint, default: 0)
created_at (timestamp)
updated_at (timestamp)
```

## 🔒 JWT Configuration

File `.env`:
```
JWT_SECRET=your_generated_secret
JWT_ALGORITHM=HS256
JWT_TTL=60                    # Access token TTL (minutes)
JWT_REFRESH_TTL=20160        # Refresh token TTL (minutes, 14 days)
```

## 🚀 Build cho Production

### Frontend
```bash
npm run build
```

Output sẽ được tạo tại `dist/` folder

### Backend
```bash
php artisan config:cache
php artisan view:cache
php artisan route:cache
```

## 📚 API Endpoints

### Authentication
```
POST   /api/auth/register          # Đăng ký
POST   /api/auth/login             # Đăng nhập
POST   /api/auth/logout            # Đăng xuất
POST   /api/auth/refresh-token     # Refresh token
GET    /api/auth/me                # Lấy thông tin user (require auth)
```

### Products (Example)
```
GET    /api/products               # Lấy danh sách (require auth)
POST   /api/products               # Tạo mới (require auth)
GET    /api/products/{id}          # Lấy chi tiết (require auth)
PUT    /api/products/{id}          # Cập nhật (require auth)
DELETE /api/products/{id}          # Xóa (soft delete, require auth)
```

## 🐛 Troubleshooting

### Lỗi: "CORS Error"
- Nếu gặp CORS error, kiểm tra `.env` file
- Đảm bảo `APP_URL=http://localhost:8000`
- Kiểm tra middleware CORS trong `app/Http/Middleware/`

### Lỗi: "TokenExpiredException"
- Sử dụng Refresh Token để lấy access token mới
- Axios interceptor sẽ tự động xử lý

### Lỗi: "Database not found"
```bash
# Tạo database SQLite
touch database/database.sqlite

# Chạy migration
php artisan migrate
```

### Frontend không kết nối được Backend
- Kiểm tra Backend server đang chạy: `php artisan serve`
- Kiểm tra `.env` file có `VITE_API_URL=http://localhost:8000/api`
- Kiểm tra port (backend: 8000, frontend: 5173)

## 📖 Tài liệu tham khảo

- [Laravel 12 Docs](https://laravel.com/docs)
- [Vue 3 Docs](https://vuejs.org)
- [Pinia Docs](https://pinia.vuejs.org)
- [Vite Docs](https://vitejs.dev)
- [JWT Auth Package](https://github.com/tymondesigns/jwt-auth)
- [Axios Docs](https://axios-http.com)

## 💡 Tips

1. **Hot Reload**: Frontend (Vite) và Backend (Laravel) đều hỗ trợ hot reload
2. **API Testing**: Dùng Postman hoặc VS Code REST Client extension
3. **Database**: Dùng SQLite Viewer extension để xem database SQLite
4. **DevTools**: Dùng Vue DevTools browser extension để debug Pinia store
5. **Network**: Dùng browser DevTools Network tab để debug API requests

## 🔄 Development Workflow

1. **Terminal 1**: Chạy Laravel
```bash
php artisan serve
```

2. **Terminal 2**: Chạy Vite (Frontend)
```bash
npm run dev
```

3. **IDE/Editor**: Mở project folder trong VS Code hoặc editor của bạn

4. **Browser**: Truy cập http://localhost:5173

5. **Lưu code**: Cả Frontend và Backend đều sẽ auto-reload

## ✨ Một số bước sau

1. Thêm nhiều features hơn (User management, etc.)
2. Tạo thêm pages và components
3. Setup CI/CD pipeline (Github Actions, etc.)
4. Deploy lên hosting (Heroku, DigitalOcean, etc.)
5. Setup email notifications
6. Add testing (Unit tests, E2E tests)
7. Setup logging và monitoring
