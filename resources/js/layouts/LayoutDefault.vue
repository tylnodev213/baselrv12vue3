<template>
  <div class="layout-default">
    <nav class="navbar">
      <div class="container">
        <div class="navbar-brand">
          <router-link
            to="/"
            class="logo"
          >
            Laravel + Vue 3
          </router-link>
        </div>
        <div class="navbar-menu">
          <router-link
            to="/"
            class="nav-link"
          >
            Trang chủ
          </router-link>
          <router-link
            to="/products"
            class="nav-link"
          >
            Sản phẩm
          </router-link>
          <div class="user-menu">
            <span class="user-name">{{ authStore.user?.name }}</span>
            <button
              class="btn-logout"
              @click="handleLogout"
            >
              Đăng xuất
            </button>
          </div>
        </div>
      </div>
    </nav>

    <main class="container content">
      <RouterView />
    </main>

    <BaseModal
      :is-open="modal.isOpen"
      :title="modal.title"
      :message="modal.message"
      :type="modal.type"
      :is-loading="modal.isLoading"
      @confirm="modal.confirm"
      @cancel="modal.cancel"
    />

    <div
      v-if="isGlobalLoading"
      class="loading-overlay"
    >
      <div class="spinner">
        <div class="spinner-border" />
        <p>{{ loadingMessage }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/useAuthStore';
import { useModal } from '@/composables/useModal';
import BaseModal from '@/components/BaseModal.vue';
import { ref } from 'vue';

const router = useRouter();
const authStore = useAuthStore();
const modal = useModal();
const isGlobalLoading = ref(false);
const loadingMessage = ref('');

const handleLogout = () => {
  modal.showConfirm('Bạn có chắc muốn đăng xuất?', async () => {
    await authStore.logout();
    await router.push({ name: 'login' });
  });
};
</script>

<style scoped>
.layout-default {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.navbar {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  padding: 1rem 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.navbar .container {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.navbar-brand {
  display: flex;
  align-items: center;
}

.logo {
  font-size: 1.5rem;
  font-weight: 600;
  color: #007bff;
  text-decoration: none;
}

.logo:hover {
  color: #0056b3;
}

.navbar-menu {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-link {
  color: #495057;
  text-decoration: none;
  transition: color 0.2s;
}

.nav-link:hover {
  color: #007bff;
}

.nav-link.router-link-active {
  color: #007bff;
  font-weight: 600;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  color: #495057;
  font-weight: 500;
}

.btn-logout {
  padding: 0.5rem 1rem;
  background-color: #dc3545;
  color: white;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background-color 0.2s;
}

.btn-logout:hover {
  background-color: #bb2d2d;
}

.content {
  flex: 1;
  padding: 2rem 0;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.spinner {
  text-align: center;
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}
</style>
