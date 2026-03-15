<template>
  <div class="page-header">
    <div class="title-area">
      <h6 class="font-weight-bolder mb-0">Quản lý Team</h6>
    </div>
    <div class="actions">
      <button class="soft-btn soft-btn-primary" @click="openCreateModal">
        <span class="icon">+</span> THÊM TEAM MỚI
      </button>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-12">
      <BaseTable
        :columns="columns"
        :rows="teams"
        :loading="loading"
        @edit="openEditModal"
        @delete="handleDelete"
      >
        <template #cell-description="{ row }">
          <span class="text-xs font-weight-bold text-secondary">
            {{ row.description || 'Không có mô tả' }}
          </span>
        </template>
        <template #cell-name="{ row }">
          <div class="d-flex px-2 py-1">
            <div class="d-flex flex-column justify-content-center">
              <h6 class="mb-0 text-sm">
                {{ row.name }}
              </h6>
            </div>
          </div>
        </template>
      </BaseTable>
    </div>
  </div>

  <!-- Global Confirm Modal -->
  <BaseModal
    :is-open="isOpen"
    :title="title"
    :message="message"
    :type="type"
    :is-loading="isLoading"
    @confirm="confirm"
    @cancel="cancel"
  />

  <BaseModal v-model="showModal" :title="isEditing ? 'Cập nhật Team' : 'Thêm Team mới'" hide-footer>
    <BaseForm ref="formRef" @submit="submitForm" @cancel="showModal = false">
      <BaseInput
        v-model="formData.name"
        label="Tên Team"
        placeholder="Nhập tên team"
        required
        :error="errors.name"
      />

      <BaseInput
        v-model="formData.description"
        label="Mô tả"
        type="textarea"
        placeholder="Nhập mô tả team"
        :error="errors.description"
      />
    </BaseForm>
  </BaseModal>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { teamService } from '@/services/teamService';
import BaseTable from '@/components/BaseTable.vue';
import BaseModal from '@/components/BaseModal.vue';
import BaseForm from '@/components/BaseForm.vue';
import BaseInput from '@/components/BaseInput.vue';
import { useModal } from '@/composables/useModal';

const { showConfirm, showSuccess, showError, isOpen, title, message, type, isLoading, confirm, cancel } = useModal();

const teams = ref([]);
const loading = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);
const formRef = ref(null);

const formData = reactive({
  name: '',
  description: '',
});

const errors = reactive({
  name: '',
  description: '',
});

const columns = [
  { key: 'id', label: 'ID', width: '80px' },
  { key: 'name', label: 'Tên Team' },
  { key: 'description', label: 'Mô tả' },
  { key: 'created_at', label: 'Ngày tạo' },
];

const fetchTeams = async () => {
  loading.value = true;
  try {
    const response = await teamService.getAllTeams();
    if (response.data.success) {
      teams.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching teams:', error);
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  currentId.value = null;
  formData.name = '';
  formData.description = '';
  Object.keys(errors).forEach((key) => (errors[key] = ''));
  showModal.value = true;
};

const openEditModal = (item) => {
  isEditing.value = true;
  currentId.value = item.id;
  formData.name = item.name;
  formData.description = item.description;
  Object.keys(errors).forEach((key) => (errors[key] = ''));
  showModal.value = true;
};

const submitForm = () => {
  showConfirm(
    `Bạn có chắc muốn ${isEditing.value ? 'cập nhật' : 'tạo mới'} team này?`,
    async () => {
      Object.keys(errors).forEach((key) => (errors[key] = ''));

      try {
        let response;
        if (isEditing.value) {
          response = await teamService.updateTeam(currentId.value, formData);
        } else {
          response = await teamService.createTeam(formData);
        }

        if (response.data.success) {
          showModal.value = false;
          showSuccess(`${isEditing.value ? 'Cập nhật' : 'Tạo'} team thành công!`);
          fetchTeams();
        } else if (response.data.errors) {
          Object.assign(errors, response.data.errors);
        }
      } catch (error) {
        console.error('Error saving team:', error);
        showError('Có lỗi xảy ra khi lưu thông tin team.');
      }
    }
  );
};

const handleDelete = (item) => {
  showConfirm(`Bạn có chắc muốn xóa team "${item.name}"?`, async () => {
    try {
      const response = await teamService.deleteTeam(item.id);
      if (response.data.success) {
        showSuccess('Xóa team thành công!');
        fetchTeams();
      }
    } catch (error) {
      console.error('Error deleting team:', error);
      showError('Có lỗi xảy ra khi xóa team.');
    }
  });
};

onMounted(fetchTeams);
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.text-xs {
  font-size: 0.75rem;
}

.text-sm {
  font-size: 0.875rem;
}

.font-weight-bold {
  font-weight: 600;
}

.font-weight-bolder {
  font-weight: 700;
}

.text-secondary {
  color: #67748e;
}

.mb-0 {
  margin-bottom: 0;
}

.px-2 {
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

.py-1 {
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
}

.d-flex {
  display: flex;
}

.flex-column {
  flex-direction: column;
}

.justify-content-center {
  justify-content: center;
}
</style>
