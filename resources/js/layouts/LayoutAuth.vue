<template>
  <div class="layout-auth">
    <div class="auth-container">
      <div class="auth-box">
        <h1 class="auth-title">Laravel + Vue 3</h1>
        <RouterView />
      </div>
    </div>

    <BaseModal
      :is-open="modal.isOpen"
      :title="modal.title"
      :message="modal.message"
      :type="modal.type"
      :is-loading="modal.isLoading"
      @confirm="modal.confirm"
      @cancel="modal.cancel"
    />
  </div>
</template>

<script setup>
import { useModal } from '@/composables/useModal';
import BaseModal from '@/components/BaseModal.vue';

const modal = useModal();
</script>

<style scoped>
.layout-auth {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-container {
  width: 100%;
  max-width: 500px;
  padding: 1rem;
}

.auth-box {
  background: white;
  border-radius: 0.5rem;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.auth-title {
  text-align: center;
  margin-bottom: 2rem;
  color: #667eea;
  font-size: 2rem;
}
</style>
