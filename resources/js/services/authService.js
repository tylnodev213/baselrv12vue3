// resources/js/services/authService.js
import api from './api';

export const authService = {
  /**
   * Đăng ký
   */
  register(payload) {
    return api.post('/auth/register', payload);
  },

  /**
   * Đăng nhập
   */
  login(payload) {
    return api.post('/auth/login', payload);
  },

  /**
   * Đăng xuất
   */
  logout() {
    return api.post('/auth/logout');
  },

  /**
   * Refresh token
   */
  refreshToken(refreshToken) {
    return api.post('/auth/refresh-token', {
      refresh_token: refreshToken,
    });
  },

  /**
   * Lấy thông tin user hiện tại
   */
  me() {
    return api.get('/auth/me');
  },

  /**
   * Kiểm tra xem có token không
   */
  hasToken() {
    const token = localStorage.getItem('access_token');
    return !!token;
  },

  /**
   * Lấy token từ localStorage
   */
  getToken() {
    return localStorage.getItem('access_token');
  },

  /**
   * Xóa token
   */
  removeToken() {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
  },
};
