<template>
  <div class="page-header">
    <div class="title-area">
      <h6 class="font-weight-bolder mb-0">Quản lý User</h6>
    </div>
    <div class="actions">
      <button class="soft-btn soft-btn-primary" @click="openCreateModal">
        <span class="icon">+</span> THÊM USER MỚI
      </button>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-12">
      <BaseTable
        :columns="columns"
        :rows="users"
        :loading="loading"
        @edit="openEditModal"
        @delete="handleDelete"
      >
        <template #cell-name="{ row }">
          <div class="d-flex px-2 py-1">
            <div class="d-flex flex-column justify-content-center">
              <h6 class="mb-0 text-sm">
                {{ row.name }}
              </h6>
              <p class="text-xs text-secondary mb-0">
                {{ row.email }}
              </p>
            </div>
          </div>
        </template>
        <template #cell-team="{ row }">
          <p class="text-xs font-weight-bold mb-0">
            {{ row.team?.name || 'Chưa thuộc team' }}
          </p>
        </template>
        <template #cell-role="{ row }">
          <span :class="['badge-soft', row.role]">
            {{ row.role === 'admin' ? 'Admin' : 'User' }}
          </span>
        </template>
        <template #cell-phone="{ row }">
          <span class="text-secondary text-xs font-weight-bold">{{ row.phone || '-' }}</span>
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

  <BaseModal v-model="showModal" :title="isEditing ? 'Cập nhật User' : 'Thêm User mới'" hide-footer>
    <BaseForm ref="formRef" @submit="submitForm" @cancel="showModal = false">
      <BaseInput v-model="formData.name" label="Họ và tên" required :error="errors.name" />

      <BaseInput
        v-model="formData.email"
        label="Email"
        type="email"
        required
        :error="errors.email"
      />

      <BaseInput
        v-model="formData.password"
        label="Mật khẩu"
        type="password"
        :required="!isEditing"
        :placeholder="isEditing ? 'Để trống nếu không muốn thay đổi' : 'Nhập mật khẩu'"
        :error="errors.password"
      />

      <div class="soft-form-group mb-4">
        <label class="soft-label">Team</label>
        <select v-model="formData.team_id" class="soft-select">
          <option :value="null">Không thuộc team</option>
          <option v-for="team in teams" :key="team.id" :value="team.id">
            {{ team.name }}
          </option>
        </select>
        <p v-if="errors.team_id" class="text-danger text-xs mt-1">
          {{ errors.team_id[0] }}
        </p>
      </div>

      <div class="soft-form-group mb-4">
        <label class="soft-label">Vai trò</label>
        <div class="d-flex gap-4 mt-2">
          <label class="d-flex align-items-center text-sm">
            <input v-model="formData.role" type="radio" value="user" class="me-2" />
            User
          </label>
          <label class="d-flex align-items-center text-sm">
            <input v-model="formData.role" type="radio" value="admin" class="me-2" />
            Admin
          </label>
        </div>
        <p v-if="errors.role" class="text-danger text-xs mt-1">
          {{ errors.role[0] }}
        </p>
      </div>

      <BaseInput v-model="formData.phone" label="Số điện thoại" :error="errors.phone" />

      <BaseInput v-model="formData.notes" label="Ghi chú" type="textarea" :error="errors.notes" />
    </BaseForm>
  </BaseModal>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { userService } from '@/services/userService';
import { teamService } from '@/services/teamService';
import BaseTable from '@/components/BaseTable.vue';
import BaseModal from '@/components/BaseModal.vue';
import BaseForm from '@/components/BaseForm.vue';
import BaseInput from '@/components/BaseInput.vue';
import { useModal } from '@/composables/useModal';

const { showConfirm, showSuccess, showError, isOpen, title, message, type, isLoading, confirm, cancel } = useModal();

const users = ref([]);
const teams = ref([]);
const loading = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);
const formRef = ref(null);

const formData = reactive({
  name: '',
  email: '',
  password: '',
  team_id: null,
  role: 'user',
  phone: '',
  notes: '',
});

const errors = reactive({});

const columns = [
  { key: 'id', label: 'ID', width: '80px' },
  { key: 'name', label: 'Họ tên' },
  { key: 'team', label: 'Team' },
  { key: 'role', label: 'Vai trò' },
  { key: 'phone', label: 'Số điện thoại' },
];

const fetchData = async () => {
  loading.value = true;
  try {
    const [usersRes, teamsRes] = await Promise.all([
      userService.getAllUsers(),
      teamService.getAllTeams(),
    ]);

    if (usersRes.data.success) {
      users.value = usersRes.data.data.data;
    }

    if (teamsRes.data.success) {
      teams.value = teamsRes.data.data;
    }
  } catch (error) {
    console.error('Error fetching data:', error);
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  currentId.value = null;
  Object.assign(formData, {
    name: '',
    email: '',
    password: '',
    team_id: null,
    role: 'user',
    phone: '',
    notes: '',
  });
  Object.keys(errors).forEach((key) => delete errors[key]);
  showModal.value = true;
};

const openEditModal = (item) => {
  isEditing.value = true;
  currentId.value = item.id;
  Object.assign(formData, {
    name: item.name,
    email: item.email,
    password: '',
    team_id: item.team_id,
    role: item.role,
    phone: item.phone || '',
    notes: item.notes || '',
  });
  Object.keys(errors).forEach((key) => delete errors[key]);
  showModal.value = true;
};

const submitForm = () => {
  showConfirm(
    `Bạn có chắc muốn ${isEditing.value ? 'cập nhật' : 'tạo mới'} người dùng này?`,
    async () => {
      Object.keys(errors).forEach((key) => delete errors[key]);

      try {
        let response;
        if (isEditing.value) {
          response = await userService.updateUser(currentId.value, formData);
        } else {
          response = await userService.createUser(formData);
        }

        if (response.data.success) {
          showModal.value = false;
          showSuccess(`${isEditing.value ? 'Cập nhật' : 'Tạo'} người dùng thành công!`);
          fetchData();
        } else if (response.data.errors) {
          Object.assign(errors, response.data.errors);
        }
      } catch (error) {
        console.error('Error saving user:', error);
        showError('Có lỗi xảy ra khi lưu thông tin người dung.');
      }
    }
  );
};

const handleDelete = (item) => {
  showConfirm(`Bạn có chắc muốn xóa người dùng "${item.name}"?`, async () => {
    try {
      const response = await userService.deleteUser(item.id);
      if (response.data.success) {
        showSuccess('Xóa người dùng thành công!');
        fetchData();
      }
    } catch (error) {
      console.error('Error deleting user:', error);
      showError('Có lỗi xảy ra khi xóa người dùng.');
    }
  });
};

onMounted(fetchData);
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
.d-flex {
  display: flex;
}
.flex-column {
  flex-direction: column;
}
.justify-content-center {
  justify-content: center;
}
.me-2 {
  margin-right: 0.5rem;
}

.badge-soft {
  padding: 0.5rem 0.9rem;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
}
.badge-soft.admin {
  background-image: var(--primary-gradient);
  color: #fff;
}
.badge-soft.user {
  background-color: #e9ecef;
  color: #67748e;
}

.soft-label {
  font-size: 0.75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  display: block;
  color: var(--text-main);
}

.soft-select {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #d2d6da;
  outline: none;
  font-size: 0.875rem;
  color: #495057;
}

.soft-select:focus {
  border-color: #cb0c9f;
}
</style>
