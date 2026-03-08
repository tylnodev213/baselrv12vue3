// resources/js/services/api.js
import axios from 'axios';
import { useAuthStore } from '@/stores/useAuthStore';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore();
    const token = authStore.getAccessToken();
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const authStore = useAuthStore();
    const originalRequest = error.config;

    // Nếu token hết hạn, thử refresh
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      
      try {
        const refreshToken = authStore.getRefreshToken();
        if (!refreshToken) {
          authStore.logout();
          return Promise.reject(error);
        }

        const response = await axios.post(`${API_BASE_URL}/auth/refresh-token`, {
          refresh_token: refreshToken,
        });

        if (response.data.success) {
          authStore.setAccessToken(response.data.data.access_token);
          api.defaults.headers.common.Authorization = `Bearer ${response.data.data.access_token}`;
          
          return api(originalRequest);
        } else {
          authStore.logout();
          return Promise.reject(error);
        }
      } catch (refreshError) {
        authStore.logout();
        return Promise.reject(refreshError);
      }
    }

    return Promise.reject(error);
  }
);

export default api;
