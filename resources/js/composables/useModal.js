// resources/js/composables/useModal.js
import { ref } from 'vue';

/**
 * Composable cho modal handling
 */
export const useModal = () => {
  const isOpen = ref(false);
  const title = ref('');
  const message = ref('');
  const type = ref('info'); // 'info', 'confirm', 'success', 'error', 'warning'
  const onConfirm = ref(null);
  const onCancel = ref(null);
  const isLoading = ref(false);

  /**
   * Mở modal
   */
  const openModal = (options) => {
    title.value = options.title || '';
    message.value = options.message || '';
    type.value = options.type || 'info';
    onConfirm.value = options.onConfirm || null;
    onCancel.value = options.onCancel || null;
    isOpen.value = true;
  };

  /**
   * Đóng modal
   */
  const closeModal = () => {
    isOpen.value = false;
    setTimeout(() => {
      title.value = '';
      message.value = '';
      type.value = 'info';
      onConfirm.value = null;
      onCancel.value = null;
    }, 300);
  };

  /**
   * Confirm action
   */
  const confirm = async () => {
    if (onConfirm.value) {
      isLoading.value = true;
      try {
        await onConfirm.value();
      } finally {
        isLoading.value = false;
        closeModal();
      }
    }
  };

  /**
   * Cancel action
   */
  const cancel = () => {
    if (onCancel.value) {
      onCancel.value();
    }
    closeModal();
  };

  /**
   * Show confirm dialog
   */
  const showConfirm = (message, onConfirmCallback) => {
    openModal({
      title: 'Xác nhận',
      message,
      type: 'confirm',
      onConfirm: onConfirmCallback,
    });
  };

  /**
   * Show success message
   */
  const showSuccess = (message) => {
    openModal({
      title: 'Thành công',
      message,
      type: 'success',
      onConfirm: () => closeModal(),
    });
  };

  /**
   * Show error message
   */
  const showError = (message) => {
    openModal({
      title: 'Lỗi',
      message,
      type: 'error',
      onConfirm: () => closeModal(),
    });
  };

  return {
    isOpen,
    title,
    message,
    type,
    isLoading,
    openModal,
    closeModal,
    confirm,
    cancel,
    showConfirm,
    showSuccess,
    showError,
  };
};
