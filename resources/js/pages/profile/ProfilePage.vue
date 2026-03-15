<template>
  <div class="page-header">
    <div class="title-area">
      <h6 class="font-weight-bolder mb-0">Thông tin cá nhân</h6>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-8">
      <div class="soft-card">
        <BaseForm :show-cancel="false" @submit="handleUpdate">
          <div class="form-row">
            <div class="form-col">
              <BaseInput v-model="formData.name" label="Họ và tên" required :error="errors.name" />
            </div>
            <div class="form-col">
              <BaseInput
                v-model="formData.email"
                label="Email"
                type="email"
                required
                :error="errors.email"
              />
            </div>
          </div>

          <div class="form-row">
            <div class="form-col">
              <BaseInput v-model="formData.phone" label="Số điện thoại" :error="errors.phone" />
            </div>
            <div class="form-col">
              <div class="soft-form-group mb-4">
                <label class="soft-label">Team hiện tại</label>
                <div class="team-badge-display">
                  {{ authStore.user?.team?.name || 'Chưa thuộc team' }}
                </div>
              </div>
            </div>
          </div>

          <BaseInput
            v-model="formData.notes"
            label="Ghi chú"
            type="textarea"
            :error="errors.notes"
          />

          <div class="divider" />

          <h6 class="text-upper text-xs font-weight-bolder mb-3">ĐỔI MẬT KHẨU</h6>
          <div class="form-row">
            <div class="form-col">
              <BaseInput
                v-model="formData.password"
                label="Mật khẩu mới"
                type="password"
                placeholder="Để trống nếu không đổi"
                :error="errors.password"
              />
            </div>
            <div class="form-col">
              <BaseInput
                v-model="formData.password_confirmation"
                label="Xác nhận mật khẩu"
                type="password"
                placeholder="Nhập lại mật khẩu mới"
              />
            </div>
          </div>

          <div class="actions mt-4">
            <button type="submit" class="soft-btn soft-btn-primary" :disabled="loading">
              {{ loading ? 'ĐANG LƯU...' : 'LƯU THAY ĐỔI' }}
            </button>
          </div>
        </BaseForm>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { userService } from '@/services/userService';
import { useAuthStore } from '@/stores/useAuthStore';
import BaseForm from '@/components/BaseForm.vue';
import BaseInput from '@/components/BaseInput.vue';
import { useModal } from '@/composables/useModal';

const { showConfirm, showSuccess, showError } = useModal();

const authStore = useAuthStore();
const loading = ref(false);

const formData = reactive({
  name: '',
  email: '',
  phone: '',
  notes: '',
  password: '',
  password_confirmation: '',
});

const errors = reactive({});

const fetchProfile = async () => {
  const user = authStore.user;
  if (user) {
    formData.name = user.name;
    formData.email = user.email;
    formData.phone = user.phone || '';
    formData.notes = user.notes || '';
  }
};

const handleUpdate = () => {
  showConfirm('Bạn có chắc muốn cập nhật thông tin cá nhân?', async () => {
    loading.value = true;
    Object.keys(errors).forEach((key) => delete errors[key]);

    try {
      const response = await userService.updateProfile(formData);
      if (response.data.success) {
        showSuccess('Cập nhật thông tin thành công!');
        authStore.setUser(response.data.data);
        formData.password = '';
        formData.password_confirmation = '';
      } else if (response.data.errors) {
        Object.assign(errors, response.data.errors);
      }
    } catch (error) {
      console.error('Error updating profile:', error);
      showError('Có lỗi xảy ra khi cập nhật thông tin.');
    } finally {
      loading.value = false;
    }
  });
};

onMounted(fetchProfile);
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.font-weight-bolder {
  font-weight: 700;
}
.text-xs {
  font-size: 0.75rem;
}
.text-upper {
  text-transform: uppercase;
}
.mb-0 {
  margin-bottom: 0;
}
.mb-3 {
  margin-bottom: 1rem;
}
.mt-4 {
  margin-top: 1.5rem;
}

.form-row {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 0.5rem;
}

.form-col {
  flex: 1;
}

.team-badge-display {
  padding: 0.5rem 0.75rem;
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  color: #67748e;
  min-height: 1.4rem;
  line-height: 1.4rem;
}

.soft-label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 0.75rem;
  font-weight: 700;
  color: #344767;
  margin-left: 0.25rem;
}

.divider {
  height: 1px;
  background-image: linear-gradient(
    to right,
    rgba(0, 0, 0, 0),
    rgba(0, 0, 0, 0.1),
    rgba(0, 0, 0, 0)
  );
  margin: 1.5rem 0;
}

.actions {
  display: flex;
  justify-content: flex-end;
}

@media (max-width: 768px) {
  .form-row {
    flex-direction: column;
    gap: 0;
  }
}
</style>
