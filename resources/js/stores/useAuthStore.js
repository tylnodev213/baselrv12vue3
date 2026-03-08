// resources/js/stores/useAuthStore.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authService } from '@/services/authService';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const accessToken = ref(localStorage.getItem('access_token'));
  const refreshToken = ref(localStorage.getItem('refresh_token'));
  const isAuthenticated = computed(() => !!accessToken.value);
  const isLoading = ref(false);

  /**
   * Set access token
   */
  const setAccessToken = (token) => {
    accessToken.value = token;
    localStorage.setItem('access_token', token);
  };

  /**
   * Set refresh token
   */
  const setRefreshToken = (token) => {
    refreshToken.value = token;
    localStorage.setItem('refresh_token', token);
  };

  /**
   * Get access token
   */
  const getAccessToken = () => accessToken.value;

  /**
   * Get refresh token
   */
  const getRefreshToken = () => refreshToken.value;

  /**
   * Set user
   */
  const setUser = (userData) => {
    user.value = userData;
  };

  /**
   * Register
   */
  const register = async (payload) => {
    isLoading.value = true;
    try {
      const response = await authService.register(payload);

      if (response.data.success) {
        const { user: userData, access_token, refresh_token } = response.data.data;
        setUser(userData);
        setAccessToken(access_token);
        setRefreshToken(refresh_token);
        return { success: true, data: response.data.data };
      }

      return {
        success: false,
        message: response.data.message,
        errors: response.data.errors,
      };
    } catch (error) {
      return {
        success: false,
        message: error.message,
        errors: error.response?.data?.errors || {},
      };
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Login
   */
  const login = async (payload) => {
    isLoading.value = true;
    try {
      const response = await authService.login(payload);

      if (response.data.success) {
        const { user: userData, access_token, refresh_token } = response.data.data;
        setUser(userData);
        setAccessToken(access_token);
        setRefreshToken(refresh_token);
        return { success: true, data: response.data.data };
      }

      return {
        success: false,
        message: response.data.message,
        errors: response.data.errors,
      };
    } catch (error) {
      return {
        success: false,
        message: error.message,
        errors: error.response?.data?.errors || {},
      };
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Logout
   */
  const logout = async () => {
    isLoading.value = true;
    try {
      await authService.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      user.value = null;
      accessToken.value = null;
      refreshToken.value = null;
      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
      isLoading.value = false;
    }
  };

  /**
   * Refresh token
   */
  const refreshAccessToken = async () => {
    try {
      const response = await authService.refreshToken(refreshToken.value);

      if (response.data.success) {
        setAccessToken(response.data.data.access_token);
        return { success: true };
      }

      logout();
      return { success: false };
    } catch (error) {
      logout();
      return { success: false };
    }
  };

  /**
   * Lấy thông tin user
   */
  const fetchUser = async () => {
    try {
      const response = await authService.me();

      if (response.data.success) {
        setUser(response.data.data);
        return { success: true, data: response.data.data };
      }

      return { success: false };
    } catch (error) {
      return { success: false };
    }
  };

  /**
   * Kiểm tra authentication
   */
  const checkAuth = async () => {
    if (!accessToken.value) {
      return false;
    }

    try {
      const response = await fetchUser();
      return response.success;
    } catch (error) {
      return false;
    }
  };

  return {
    user,
    accessToken,
    refreshToken,
    isAuthenticated,
    isLoading,
    setAccessToken,
    setRefreshToken,
    getAccessToken,
    getRefreshToken,
    setUser,
    register,
    login,
    logout,
    refreshAccessToken,
    fetchUser,
    checkAuth,
  };
});
