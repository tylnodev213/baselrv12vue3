// resources/js/stores/useProductStore.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { productService } from '@/services/productService';

export const useProductStore = defineStore('product', () => {
  const products = ref([]);
  const currentProduct = ref(null);
  const isLoading = ref(false);
  const currentPage = ref(1);
  const totalPages = ref(1);
  const perPage = ref(15);

  /**
   * Get all products
   */
  const fetchProducts = async (page = 1) => {
    isLoading.value = true;
    try {
      const response = await productService.getAll(page, perPage.value);

      if (response.data.success) {
        products.value = response.data.data.data || response.data.data;
        currentPage.value = page;
        if (response.data.data.last_page) {
          totalPages.value = response.data.data.last_page;
        }
        return { success: true };
      }

      return { success: false, message: response.data.message };
    } catch (error) {
      return { success: false, message: error.message };
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Get product by ID
   */
  const fetchProductById = async (id) => {
    try {
      const response = await productService.getById(id);

      if (response.data.success) {
        currentProduct.value = response.data.data;
        return { success: true, data: response.data.data };
      }

      return { success: false, message: response.data.message };
    } catch (error) {
      return { success: false, message: error.message };
    }
  };

  /**
   * Create product
   */
  const createProduct = async (payload) => {
    try {
      const response = await productService.create(payload);

      if (response.data.success) {
        products.value.unshift(response.data.data);
        return { success: true, data: response.data.data };
      }

      return {
        success: false,
        message: response.data.message,
        errors: response.data.errors,
      };
    } catch (error) {
      return { success: false, message: error.message };
    }
  };

  /**
   * Update product
   */
  const updateProduct = async (id, payload) => {
    try {
      const response = await productService.update(id, payload);

      if (response.data.success) {
        const index = products.value.findIndex((p) => p.id === id);
        if (index !== -1) {
          products.value[index] = response.data.data;
        }
        currentProduct.value = response.data.data;
        return { success: true, data: response.data.data };
      }

      return {
        success: false,
        message: response.data.message,
        errors: response.data.errors,
      };
    } catch (error) {
      return { success: false, message: error.message };
    }
  };

  /**
   * Delete product (soft delete)
   */
  const deleteProduct = async (id) => {
    try {
      const response = await productService.delete(id);

      if (response.data.success) {
        products.value = products.value.filter((p) => p.id !== id);
        return { success: true };
      }

      return { success: false, message: response.data.message };
    } catch (error) {
      return { success: false, message: error.message };
    }
  };

  /**
   * Restore product
   */
  const restoreProduct = async (id) => {
    try {
      const response = await productService.restore(id);

      if (response.data.success) {
        return { success: true };
      }

      return { success: false, message: response.data.message };
    } catch (error) {
      return { success: false, message: error.message };
    }
  };

  /**
   * Force delete product
   */
  const forceDeleteProduct = async (id) => {
    try {
      const response = await productService.forceDelete(id);

      if (response.data.success) {
        products.value = products.value.filter((p) => p.id !== id);
        return { success: true };
      }

      return { success: false, message: response.data.message };
    } catch (error) {
      return { success: false, message: error.message };
    }
  };

  /**
   * Reset current product
   */
  const resetCurrentProduct = () => {
    currentProduct.value = null;
  };

  /**
   * Computed
   */
  const totalProducts = computed(() => products.value.length);
  const isEmpty = computed(() => products.value.length === 0);

  return {
    products,
    currentProduct,
    isLoading,
    currentPage,
    totalPages,
    perPage,
    totalProducts,
    isEmpty,
    fetchProducts,
    fetchProductById,
    createProduct,
    updateProduct,
    deleteProduct,
    restoreProduct,
    forceDeleteProduct,
    resetCurrentProduct,
  };
});
