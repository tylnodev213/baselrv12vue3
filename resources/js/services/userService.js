// resources/js/services/userService.js
import api from './api';

export const userService = {
  /**
   * Admin: Get all users
   */
  getAllUsers(params) {
    return api.get('/admin/users', { params });
  },

  /**
   * Admin: Create a user
   */
  createUser(data) {
    return api.post('/admin/users', data);
  },

  /**
   * Admin: Update a user
   */
  updateUser(id, data) {
    return api.put(`/admin/users/${id}`, data);
  },

  /**
   * Admin: Delete a user
   */
  deleteUser(id) {
    return api.delete(`/admin/users/${id}`);
  },

  /**
   * User: Get profile
   */
  getProfile() {
    return api.get('/profile');
  },

  /**
   * User: Update profile
   */
  updateProfile(data) {
    return api.put('/profile', data);
  },
};
