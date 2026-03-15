// resources/js/services/dashboardService.js
import api from './api';

export const dashboardService = {
  /**
   * Get real dashboard stats from the server
   */
  getStats() {
    return api.get('/admin/dashboard-stats');
  },
};
