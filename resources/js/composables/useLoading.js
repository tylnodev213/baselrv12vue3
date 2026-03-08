// resources/js/composables/useLoading.js
import { ref } from 'vue';

/**
 * Composable cho loading state
 */
export const useLoading = () => {
  const isLoading = ref(false);
  const loadingMessage = ref('');

  /**
   * Bắt đầu loading
   */
  const startLoading = (message = '') => {
    isLoading.value = true;
    loadingMessage.value = message;
  };

  /**
   * Kết thúc loading
   */
  const stopLoading = () => {
    isLoading.value = false;
    loadingMessage.value = '';
  };

  /**
   * Set loading message
   */
  const setLoadingMessage = (message) => {
    loadingMessage.value = message;
  };

  return {
    isLoading,
    loadingMessage,
    startLoading,
    stopLoading,
    setLoadingMessage,
  };
};
