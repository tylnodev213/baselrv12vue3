<template>
  <teleport to="body">
    <transition name="modal-fade">
      <div
        v-if="isOpen"
        class="modal-overlay"
        @click.self="handleCancel"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">
              {{ title }}
            </h2>
            <button
              type="button"
              class="btn-close"
              :disabled="isLoading"
              @click="handleCancel"
            >
              ✕
            </button>
          </div>

          <div class="modal-body">
            <p>{{ message }}</p>
            <slot />
          </div>

          <div class="modal-footer">
            <button
              v-if="type === 'confirm' || type === 'error' || type === 'warning'"
              type="button"
              class="btn btn-secondary"
              :disabled="isLoading"
              @click="handleCancel"
            >
              {{ cancelButtonText }}
            </button>
            <button
              v-if="type === 'confirm' || type === 'success'"
              type="button"
              class="btn"
              :class="confirmButtonClass"
              :disabled="isLoading"
              @click="handleConfirm"
            >
              <span v-if="!isLoading">{{ confirmButtonText }}</span>
              <span v-else>
                <span class="spinner-border spinner-border-sm me-2" />
                {{ loadingText }}
              </span>
            </button>
            <button
              v-if="type === 'info' || type === 'success' || type === 'error'"
              type="button"
              class="btn btn-primary"
              :disabled="isLoading"
              @click="handleCancel"
            >
              {{ closeButtonText }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Modal',
  },
  message: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    enum: ['info', 'confirm', 'success', 'error', 'warning'],
    default: 'info',
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
  confirmButtonText: {
    type: String,
    default: 'Xác nhận',
  },
  cancelButtonText: {
    type: String,
    default: 'Hủy',
  },
  closeButtonText: {
    type: String,
    default: 'Đóng',
  },
  loadingText: {
    type: String,
    default: 'Đang xử lý...',
  },
});

const emit = defineEmits(['confirm', 'cancel', 'close']);

const confirmButtonClass = computed(() => {
  switch (props.type) {
    case 'confirm':
      return 'btn-primary';
    case 'success':
      return 'btn-success';
    case 'error':
      return 'btn-danger';
    case 'warning':
      return 'btn-warning';
    default:
      return 'btn-primary';
  }
});

const handleConfirm = () => {
  emit('confirm');
};

const handleCancel = () => {
  emit('cancel');
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #dee2e6;
}

.modal-title {
  margin: 0;
  font-size: 1.5rem;
  color: #212529;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6c757d;
  cursor: pointer;
  padding: 0;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}

.btn-close:hover:not(:disabled) {
  color: #212529;
}

.btn-close:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-body {
  padding: 1.5rem;
}

.modal-body p {
  margin: 0 0 1rem 0;
  color: #495057;
}

.modal-footer {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
  padding: 1.5rem;
  border-top: 1px solid #dee2e6;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #0056b3;
}

.btn-success {
  background-color: #28a745;
  color: white;
}

.btn-success:hover:not(:disabled) {
  background-color: #1e7e34;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background-color: #bb2d2d;
}

.btn-warning {
  background-color: #ffc107;
  color: #212529;
}

.btn-warning:hover:not(:disabled) {
  background-color: #e0a800;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background-color: #545b62;
}

.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.spinner-border {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  vertical-align: -0.125em;
  border: 0.25em solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spinner-border 0.75s linear infinite;
}

.spinner-border-sm {
  width: 0.875rem;
  height: 0.875rem;
  border-width: 0.2em;
}

@keyframes spinner-border {
  to {
    transform: rotate(360deg);
  }
}

.me-2 {
  margin-right: 0.5rem;
}

/* Modal fade animation */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease-in-out;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-active .modal-content {
  animation: modal-slide-in 0.3s ease-in-out;
}

.modal-fade-leave-active .modal-content {
  animation: modal-slide-out 0.3s ease-in-out;
}

@keyframes modal-slide-in {
  from {
    transform: scale(0.95);
  }
  to {
    transform: scale(1);
  }
}

@keyframes modal-slide-out {
  from {
    transform: scale(1);
  }
  to {
    transform: scale(0.95);
  }
}
</style>
