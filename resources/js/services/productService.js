// resources/js/services/productService.js
import api from './api';

export const productService = {
  /**
   * Lấy danh sách sản phẩm
   */
  getAll(page = 1, perPage = 15) {
    return api.get('/products', {
      params: { page, per_page: perPage },
    });
  },

  /**
   * Lấy chi tiết sản phẩm
   */
  getById(id) {
    return api.get(`/products/${id}`);
  },

  /**
   * Tạo sản phẩm mới
   */
  create(payload) {
    return api.post('/products', payload);
  },

  /**
   * Cập nhật sản phẩm
   */
  update(id, payload) {
    return api.put(`/products/${id}`, payload);
  },

  /**
   * Xóa sản phẩm (soft delete)
   */
  delete(id) {
    return api.delete(`/products/${id}`);
  },

  /**
   * Khôi phục sản phẩm
   */
  restore(id) {
    return api.post(`/products/${id}/restore`);
  },

  /**
   * Xóa vĩnh viễn
   */
  forceDelete(id) {
    return api.delete(`/products/${id}/force`);
  },
};
