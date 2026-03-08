<template>
  <form @submit.prevent="handleSubmit" class="form">
    <slot></slot>
    
    <div class="form-actions">
      <button
        type="submit"
        :disabled="isSubmitting"
        class="btn btn-primary"
      >
        <span v-if="!isSubmitting">{{ submitText }}</span>
        <span v-else>
          <span class="spinner-border spinner-border-sm me-2"></span>
          {{ submitLoadingText }}
        </span>
      </button>
      
      <button
        v-if="showCancel"
        type="button"
        :disabled="isSubmitting"
        class="btn btn-secondary ms-2"
        @click="handleCancel"
      >
        {{ cancelText }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  submitText: {
    type: String,
    default: 'Submit',
  },
  submitLoadingText: {
    type: String,
    default: 'Đang xử lý...',
  },
  cancelText: {
    type: String,
    default: 'Hủy',
  },
  showCancel: {
    type: Boolean,
    default: true,
  },
  onSubmit: {
    type: Function,
    required: true,
  },
  onCancel: {
    type: Function,
    default: null,
  },
  requiresConfirmation: {
    type: Boolean,
    default: false,
  },
  confirmMessage: {
    type: String,
    default: 'Bạn có chắc muốn thực hiện hành động này?',
  },
  onConfirm: {
    type: Function,
    default: null,
  },
});

const emit = defineEmits(['submit', 'cancel']);

const isSubmitting = ref(false);

const handleSubmit = async () => {
  if (props.requiresConfirmation) {
    // Emit event để component cha xử lý confirm
    emit('submit', { requiresConfirmation: true });
    return;
  }

  isSubmitting.value = true;
  try {
    await props.onSubmit();
  } finally {
    isSubmitting.value = false;
  }
};

const handleCancel = () => {
  if (props.onCancel) {
    props.onCancel();
  }
  emit('cancel');
};

const executeSubmit = async () => {
  isSubmitting.value = true;
  try {
    if (props.onConfirm) {
      await props.onConfirm();
    } else {
      await props.onSubmit();
    }
  } finally {
    isSubmitting.value = false;
  }
};

defineExpose({
  executeSubmit,
  isSubmitting,
});
</script>

<style scoped>
.form {
  width: 100%;
}

.form-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 2rem;
}

.btn {
  padding: 0.5rem 1rem;
  font-size: 1rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  transition: all 0.3s ease-in-out;
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

.btn-primary:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
  opacity: 0.65;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background-color: #545b62;
}

.btn-secondary:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
  opacity: 0.65;
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

.ms-2 {
  margin-left: 0.5rem;
}
</style>
