<template>
  <div class="layout-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="brand-icon bg-gradient-primary">
          <svg
            width="15"
            height="15"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M10 0C4.47715 0 0 4.47715 0 10C0 15.5228 4.47715 20 10 20C15.5228 20 20 15.5228 20 10C20 4.47715 15.5228 0 10 0ZM10 18C5.58172 18 2 14.4183 2 10C2 5.58172 5.58172 2 10 2C14.4183 2 18 5.58172 18 10C18 14.4183 14.4183 18 10 18ZM10 6C10.5523 6 11 6.44772 11 7V13C11 13.5523 10.5523 14 10 14C9.44772 14 9 13.5523 9 13V7C9 6.44772 9.44772 6 10 6Z"
              fill="white"
            />
          </svg>
        </div>
        <span class="brand-text">Soft UI Dashboard</span>
      </div>

      <div class="sidebar-links">
        <div class="menu-label">PAGES</div>
        <router-link to="/" class="sidebar-link" exact-active-class="active">
          <div class="icon">🏠</div>
          <span>Dashboard</span>
        </router-link>

        <router-link to="/products" class="sidebar-link" exact-active-class="active">
          <div class="icon">📦</div>
          <span>Sản phẩm</span>
        </router-link>

        <router-link to="/my-team" class="sidebar-link" exact-active-class="active">
          <div class="icon">👥</div>
          <span>Team của tôi</span>
        </router-link>

        <router-link to="/profile" class="sidebar-link" exact-active-class="active">
          <div class="icon">👤</div>
          <span>Hồ sơ cá nhân</span>
        </router-link>

        <template v-if="authStore.user?.role === 'admin'">
          <div class="menu-label">ADMIN</div>
          <router-link to="/admin/teams" class="sidebar-link" exact-active-class="active">
            <div class="icon">🏢</div>
            <span>Quản lý Team</span>
          </router-link>
          <router-link to="/admin/users" class="sidebar-link" exact-active-class="active">
            <div class="icon">👔</div>
            <span>Quản lý User</span>
          </router-link>
        </template>
      </div>

      <div class="sidebar-footer">
        <div class="upgrade-card bg-gradient-dark">
          <p>Cần hỗ trợ?</p>
          <small>Hãy liên hệ với Admin.</small>
          <button class="btn-help">LIÊN HỆ</button>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Navbar -->
      <nav class="top-nav">
        <div class="breadcrumb">
          <span class="root">Pages</span> / <span class="current">{{ currentRouteName }}</span>
        </div>
        <div class="nav-actions">
          <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" placeholder="Type here..." />
          </div>
          <div class="user-info">
            <span class="name">{{ authStore.user?.name }}</span>
            <button class="logout-btn" @click="handleLogout">SIGN OUT</button>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <div class="page-body">
        <RouterView />
      </div>
    </main>

    <!-- Global Components -->
    <BaseModal
      :is-open="isOpen"
      :title="title"
      :message="message"
      :type="type"
      :is-loading="isLoading"
      @confirm="confirm"
      @cancel="cancel"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/useAuthStore';
import { useModal } from '@/composables/useModal';
import BaseModal from '@/components/BaseModal.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const { isOpen, title, message, type, isLoading, confirm, cancel, showConfirm } = useModal();

const currentRouteName = computed(() => {
  return route.meta?.title || route.name || 'Dashboard';
});

const handleLogout = () => {
  showConfirm('Bạn có chắc muốn đăng xuất?', async () => {
    await authStore.logout();
    await router.push({ name: 'login' });
  });
};
</script>

<style scoped>
.layout-wrapper {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 260px;
  background-color: var(--bg-color);
  padding: 1.5rem;
  position: fixed;
  left: 0;
  top: 0;
  bottom: 0;
  z-index: 100;
  border-right: 0 solid transparent;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  padding-left: 1rem;
  margin-bottom: 2rem;
}

.brand-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
}

.brand-text {
  font-weight: 600;
  font-size: 0.875rem;
  color: var(--text-main);
  letter-spacing: 0.5px;
}

.menu-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-secondary);
  margin: 1.5rem 0 0.5rem 1rem;
  opacity: 0.8;
}

.sidebar-links {
  display: flex;
  flex-direction: column;
}

.sidebar-link {
  display: flex;
  align-items: center;
  padding: 0.675rem 1rem;
  margin: 0.125rem 0;
  border-radius: 0.5rem;
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 400;
  transition: var(--transition);
}

.sidebar-link .icon {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: 8px;
  margin-right: 0.75rem;
  box-shadow:
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
  font-size: 0.9rem;
}

.sidebar-link.active {
  background-color: #fff;
  color: var(--text-main);
  font-weight: 600;
  box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
}

.sidebar-link.active .icon {
  background-image: var(--primary-gradient);
}

/* Sidebar Footer Upgrade Card */
.sidebar-footer {
  margin-top: auto;
  padding-top: 2rem;
}

.upgrade-card {
  padding: 1.25rem;
  border-radius: 1rem;
  color: #fff;
  position: relative;
  overflow: hidden;
}

.upgrade-card p {
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.upgrade-card small {
  display: block;
  margin-bottom: 1rem;
  opacity: 0.8;
}

.btn-help {
  width: 100%;
  padding: 0.5rem;
  border-radius: 0.5rem;
  border: none;
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
  font-weight: 700;
  font-size: 0.7rem;
  cursor: pointer;
  transition: var(--transition);
}

.btn-help:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

/* Main Content Area */
.main-content {
  flex: 1;
  margin-left: 260px;
  padding: 1rem 1.5rem;
  transition: var(--transition);
}

.top-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  margin-bottom: 1.5rem;
}

.breadcrumb {
  font-size: 0.875rem;
  color: var(--text-main);
}

.breadcrumb .root {
  opacity: 0.5;
}

.breadcrumb .current {
  font-weight: 600;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.search-box {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 10px;
  font-size: 0.8rem;
  color: var(--text-secondary);
}

.search-box input {
  padding: 0.5rem 1rem 0.5rem 2.2rem;
  border-radius: 0.5rem;
  border: 1px solid #d2d6da;
  font-size: 0.75rem;
  width: 200px;
  outline: none;
  transition: var(--transition);
}

.search-box input:focus {
  width: 240px;
  border-color: #cb0c9f;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info .name {
  font-weight: 600;
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.logout-btn {
  background: none;
  border: none;
  font-weight: 700;
  font-size: 0.75rem;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 0;
}

.logout-btn:hover {
  color: var(--text-main);
}

.page-body {
  padding-top: 0.5rem;
}

@media (max-width: 991.98px) {
  .sidebar {
    transform: translateX(-100%);
    transition: var(--transition);
  }
  .main-content {
    margin-left: 0;
  }
}
</style>
