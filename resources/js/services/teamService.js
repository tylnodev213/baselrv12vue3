// resources/js/services/teamService.js
import api from './api';

export const teamService = {
  /**
   * Admin: Get all teams
   */
  getAllTeams() {
    return api.get('/admin/teams');
  },

  /**
   * Admin: Create a team
   */
  createTeam(data) {
    return api.post('/admin/teams', data);
  },

  /**
   * Admin: Update a team
   */
  updateTeam(id, data) {
    return api.put(`/admin/teams/${id}`, data);
  },

  /**
   * Admin: Delete a team
   */
  deleteTeam(id) {
    return api.delete(`/admin/teams/${id}`);
  },

  /**
   * User: Get my team members
   */
  getMyTeamMembers() {
    return api.get('/my-team');
  },
};
