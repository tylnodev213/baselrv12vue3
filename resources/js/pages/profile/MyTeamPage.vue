<template>
  <div class="my-team-page">
    <div class="header">
      <h1>Thành viên cùng Team</h1>
    </div>

    <div v-if="!authStore.user?.team_id" class="no-team-alert">
      Bạn chưa được phân vào team nào.
    </div>

    <BaseTable v-else :columns="columns" :rows="members" :loading="loading" :show-actions="false" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { teamService } from '@/services/teamService';
import { useAuthStore } from '@/stores/useAuthStore';
import BaseTable from '@/components/BaseTable.vue';

const authStore = useAuthStore();
const members = ref([]);
const loading = ref(false);

const columns = [
  { key: 'id', label: 'ID', width: '80px' },
  { key: 'name', label: 'Họ tên' },
  { key: 'email', label: 'Email' },
  { key: 'phone', label: 'Số điện thoại' },
];

const fetchMembers = async () => {
  if (!authStore.user?.team_id) return;

  loading.value = true;
  try {
    const response = await teamService.getMyTeamMembers();
    if (response.data.success) {
      members.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching team members:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchMembers);
</script>

<style scoped>
.my-team-page {
  padding: 1rem;
}

.header {
  margin-bottom: 2rem;
}

h1 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
}

.no-team-alert {
  background-color: #fef2f2;
  border-left: 4px solid #ef4444;
  padding: 1rem;
  color: #991b1b;
  border-radius: 0.25rem;
}
</style>
