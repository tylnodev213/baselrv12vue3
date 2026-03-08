# 🐳 Docker Setup Guide

## Yêu cầu

- Docker Desktop (hoặc Docker + Docker Compose)
- 4GB RAM tối thiểu

## 🚀 Chạy với Docker

### Option 1: Production-like Setup (Recommended)

Cấu trúc tách biệt: Backend + Frontend + Nginx

```bash
# 1. Build images
docker-compose build

# 2. Khởi chạy containers
docker-compose up -d

# 3. Run migrations (first time)
docker-compose exec php-fpm php artisan migrate

# 4. Generate JWT secret (first time)
docker-compose exec php-fpm php artisan jwt:secret
```

**Access:**
- Backend API: http://localhost:8000
- Frontend: http://localhost:5173

**Containers:**
- `laravel-app` (PHP FPM)
- `nginx-backend` (Backend Nginx)
- `vue-app` (Frontend Nginx)
- `database-storage` (SQLite volume)

### Option 2: Development Setup (All-in-One)

Một container duy nhất - dễ dàng cho development

```bash
# 1. Build image
docker-compose -f docker-compose-dev.yml build

# 2. Khởi chạy
docker-compose -f docker-compose-dev.yml up
```

**Access:**
- Backend API: http://localhost:8000
- Frontend Dev: http://localhost:5173
- Frontend Build: http://localhost:8000 (served by Apache)

**Features:**
- Apache + PHP + Node in one container
- Auto-run migrations
- Auto-run `npm run dev` (Vite)
- Hot reload cho cả Frontend và Backend

## 📝 Commands

### Build Images
```bash
# Build all images
docker-compose build

# Build specific service
docker-compose build frontend
docker-compose build php-fpm
```

### Start/Stop Containers
```bash
# Start in background
docker-compose up -d

# Start with logs
docker-compose up

# Stop containers
docker-compose down

# Stop and remove volumes
docker-compose down -v
```

### Execute Commands
```bash
# Run Laravel commands
docker-compose exec php-fpm php artisan migrate
docker-compose exec php-fpm php artisan tinker

# Run npm commands
docker-compose exec frontend npm install

# Access container shell
docker-compose exec php-fpm bash
docker-compose exec frontend sh
```

### View Logs
```bash
# All containers
docker-compose logs -f

# Specific container
docker-compose logs -f php-fpm
docker-compose logs -f nginx-backend

# Last 100 lines
docker-compose logs --tail=100 php-fpm
```

### Rebuild after code changes
```bash
# If Dockerfile changes
docker-compose build --no-cache php-fpm

# If package.json changes
docker-compose build frontend

# Apply changes
docker-compose up -d
```

## 🔧 Development Workflow

### Production Setup
```bash
# Terminal
docker-compose up -d

# It will:
# 1. Build PHP + Nginx backend
# 2. Build Node + Nginx frontend
# 3. Create SQLite database
# 4. Run both services
```

### Development Setup
```bash
# Single command - all in one
docker-compose -f docker-compose-dev.yml up

# It will:
# 1. Start Apache + PHP + Node
# 2. Auto-run migrations
# 3. Auto-start npm dev server
# 4. Hot reload on code changes
```

## 📁 File Structure

```
Dockerfile              # Production/All-in-one image
Dockerfile.backend      # Backend PHP-FPM image
Dockerfile.frontend     # Frontend Node + Nginx image
docker-compose.yml      # Production setup
docker-compose-dev.yml  # Development setup
.dockerignore          # Files to ignore in Docker build
docker/
├── nginx.conf         # Frontend Nginx config
├── nginx-backend.conf # Backend Nginx config
└── apache.conf        # Apache config (dev)
```

## 🌍 Environment Variables

### Automatic (set in docker-compose.yml)
```
APP_NAME=Laravel12Vue3
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
JWT_SECRET=...
VITE_API_URL=http://localhost:8000/api
```

### Custom Override
Tạo `.env.docker`:
```bash
# .env.docker
APP_DEBUG=false
VITE_API_URL=http://api.example.com/api
```

Use:
```bash
docker-compose -f docker-compose.yml --env-file .env.docker up -d
```

## 🐛 Troubleshooting

### Port already in use
```bash
# Check what's using port 8000
lsof -i :8000
netstat -tuln | grep 8000

# Change port in docker-compose.yml
# ports:
#   - "8001:80"
```

### Database not persisting
```bash
# Check SQLite file exists
ls -la database/database.sqlite

# Create if missing
touch database/database.sqlite
docker-compose exec php-fpm php artisan migrate
```

### Permission denied errors
```bash
# Fix permissions
docker-compose exec php-fpm chown -R www-data:www-data /app
docker-compose exec php-fpm chmod -R 755 /app/storage
```

### Migrations not running
```bash
# Run manually
docker-compose exec php-fpm php artisan migrate:fresh --seed

# Or check logs
docker-compose logs php-fpm
```

### Frontend not rebuilding
```bash
# Clear node modules
docker-compose down -v
docker-compose build --no-cache frontend
docker-compose up -d frontend
```

### API CORS errors
```bash
# Check APP_URL in docker-compose.yml
# Should be: APP_URL=http://localhost:8000

# Check VITE_API_URL
# Should be: VITE_API_URL=http://localhost:8000/api
```

## 📊 Production Deployment

### Build for Production
```bash
# 1. Update docker-compose.yml environment
APP_ENV=production
APP_DEBUG=false

# 2. Build
docker-compose build

# 3. Push to registry (if using)
docker tag laravel-app registry.example.com/laravel-app:1.0
docker push registry.example.com/laravel-app:1.0

# 4. Deploy
docker-compose -f docker-compose.yml up -d
```

### Using Docker Swarm
```bash
# Initialize swarm
docker swarm init

# Deploy stack
docker stack deploy -c docker-compose.yml app
```

### Using Kubernetes
```bash
# Convert docker-compose to K8s
kompose convert -f docker-compose.yml -o k8s/

# Deploy
kubectl apply -f k8s/
```

## 📈 Performance Tips

1. **Use .dockerignore** - Reduce image size
2. **Multi-stage builds** - Smaller final images
3. **Layer caching** - Order Dockerfile for efficiency
4. **Limit container resources**:
   ```yaml
   services:
     php-fpm:
       deploy:
         resources:
           limits:
             memory: 512M
   ```

5. **Use named volumes** - Better performance than bind mounts
6. **Implement health checks**:
   ```yaml
   healthcheck:
     test: ["CMD", "curl", "-f", "http://localhost/health"]
     interval: 30s
     timeout: 10s
     retries: 3
   ```

## 📚 Useful Docker Commands

```bash
# List running containers
docker ps

# List all containers
docker ps -a

# List images
docker images

# Remove dangling images
docker image prune

# Remove unused volumes
docker volume prune

# View container resource usage
docker stats

# Copy file from container
docker cp container_name:/path/to/file ./local/path

# Copy file to container
docker cp ./local/file container_name:/path/to/
```

## 🔒 Security Tips

1. Don't run as root
2. Use environment variables for secrets
3. Limit container privileges
4. Use read-only filesystems where possible
5. Keep images updated
6. Use private registries for production

## 📖 References

- [Docker Official Docs](https://docs.docker.com/)
- [Docker Compose Docs](https://docs.docker.com/compose/)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [PHP Official Docker Images](https://hub.docker.com/_/php)
- [Nginx Official Docker Images](https://hub.docker.com/_/nginx)
- [Node Official Docker Images](https://hub.docker.com/_/node)

## 💡 Quick Reference

```bash
# Development (All-in-one)
docker-compose -f docker-compose-dev.yml up

# Production (Separate services)
docker-compose up -d

# Run migrations
docker-compose exec php-fpm php artisan migrate

# Check logs
docker-compose logs -f

# Stop all
docker-compose down

# Rebuild
docker-compose build --no-cache
```

---

**Happy Dockering!** 🚀
