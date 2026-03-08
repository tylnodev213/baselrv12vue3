<template>
  <div class="home-page">
    <h1>Chào mừng đến với Laravel + Vue 3</h1>

    <div class="welcome-message">
      <p>Xin chào {{ authStore.user?.name }}!</p>
      <p>Đây là trang chủ của ứng dụng, bạn có thể bắt đầu phát triển từ đây.</p>
    </div>

    <div class="features">
      <div class="feature-card">
        <h3>🔐 JWT Authentication</h3>
        <p>Hệ thống xác thực với JWT Token và Refresh Token đã được setup sẵn.</p>
      </div>

      <div class="feature-card">
        <h3>📦 Architecture</h3>
        <p>Cấu trúc Service -> Repository -> Controller được setup sẵn cho các xử lý CRUD.</p>
      </div>

      <div class="feature-card">
        <h3>🎨 Vue 3 Components</h3>
        <p>Các Base Component đã được tạo: BaseInput, BaseForm, BaseTable, BaseModal.</p>
      </div>

      <div class="feature-card">
        <h3>🧩 Composables</h3>
        <p>Composables được chia nhỏ: useForm, useModal, useLoading, giúp code sạch hơn.</p>
      </div>

      <div class="feature-card">
        <h3>✅ Validation</h3>
        <p>Form validation với error messages hiển thị dưới Input, response backend return status 200.</p>
      </div>

      <div class="feature-card">
        <h3>🗑️ Soft Delete</h3>
        <p>Soft Delete Model với field del_flag (Enum 0: off, 1: on) đã được setup.</p>
      </div>
    </div>

    <div class="next-steps">
      <h2>Bước tiếp theo</h2>
      <ol>
        <li>Tạo Model mới (sử dụng BaseModel)</li>
        <li>Tạo Repository cho Model (sử dụng BaseRepository)</li>
        <li>Tạo Service cho Model (sử dụng BaseService)</li>
        <li>Tạo Controller cho Model (sử dụng BaseController)</li>
        <li>Tạo Component Vue (sử dụng BaseForm, BaseTable, BaseInput)</li>
        <li>Setup Router cho page mới</li>
      </ol>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/useAuthStore';

const authStore = useAuthStore();
</script>

<style scoped>
.home-page {
  max-width: 1200px;
  margin: 0 auto;
}

h1 {
  text-align: center;
  color: #212529;
  margin-bottom: 2rem;
}

.welcome-message {
  background-color: #e7f3ff;
  border-left: 4px solid #007bff;
  padding: 1.5rem;
  border-radius: 0.25rem;
  margin-bottom: 3rem;
}

.welcome-message p {
  margin: 0.5rem 0;
  color: #004085;
}

.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.feature-card {
  background-color: white;
  border: 1px solid #dee2e6;
  border-radius: 0.5rem;
  padding: 1.5rem;
  transition: all 0.3s ease-in-out;
}

.feature-card:hover {
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  transform: translateY(-5px);
}

.feature-card h3 {
  margin-top: 0;
  color: #007bff;
}

.feature-card p {
  color: #6c757d;
  line-height: 1.6;
}

.next-steps {
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 0.5rem;
  padding: 2rem;
}

.next-steps h2 {
  color: #212529;
  margin-top: 0;
}

.next-steps ol {
  color: #495057;
  line-height: 2;
}

.next-steps li {
  margin-bottom: 0.5rem;
}
</style>
