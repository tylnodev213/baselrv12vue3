// resources/js/services/api.js
import axios from 'axios';
import { useAuthStore } from '@/stores/useAuthStore';

// Get API URL - VITE_API_URL can be relative path or full URL
const getApiUrl = () => {
  const envUrl = import.meta.env.VITE_API_URL;
  
  // If it's a relative path (starts with /), keep it relative
  // Vite proxy will handle forwarding
  if (envUrl && envUrl.startsWith('/')) {
    return envUrl;
  }
  
  // If it's a full URL, use it
  if (envUrl) {
    return envUrl;
  }
  
  // Fallback: construct from current domain
  return `${window.location.origin}/api`;
};

const API_BASE_URL = getApiUrl();

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
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
