<template>
  <div class="login-page">
    <h2 class="page-title">Đăng nhập</h2>

    <BaseForm
      ref="formRef"
      :submit-text="'Đăng nhập'"
      :submit-loading-text="'Đang đăng nhập...'"
      :show-cancel="false"
      :on-submit="handleSubmit"
      :requires-confirmation="false"
    >
      <BaseInput
        v-model="form.email"
        type="email"
        label="Email"
        placeholder="Nhập email của bạn"
        :error="form.errors.email"
        required
        @blur="handleFieldTouched('email')"
      />

      <BaseInput
        v-model="form.password"
        type="password"
        label="Mật khẩu"
        placeholder="Nhập mật khẩu"
        :error="form.errors.password"
        required
        @blur="handleFieldTouched('password')"
      />
    </BaseForm>

    <div class="auth-footer">
      <p>Chưa có tài khoản? <router-link to="/auth/register" class="auth-link">Đăng ký ngay</router-link></p>
    </div>

    <BaseModal
      :is-open="modal.isOpen"
      :title="modal.title"
      :message="modal.message"
      :type="modal.type"
      :is-loading="modal.isLoading"
      @confirm="modal.confirm"
      @cancel="modal.cancel"
    />
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/useAuthStore';
import { useModal } from '@/composables/useModal';
import BaseForm from '@/components/BaseForm.vue';
import BaseInput from '@/components/BaseInput.vue';
import BaseModal from '@/components/BaseModal.vue';

const router = useRouter();
const authStore = useAuthStore();
const modal = useModal();
const formRef = ref(null);

const form = reactive({
  email: '',
  password: '',
  errors: {},
});

const handleFieldTouched = (fieldName) => {
  // Xóa error của field khi user bắt đầu nhập
  if (form.errors[fieldName]) {
    form.errors[fieldName] = '';
  }
};

const validateForm = () => {
  form.errors = {};
  let isValid = true;

  if (!form.email) {
    form.errors.email = 'Email không được để trống';
    isValid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    form.errors.email = 'Email không hợp lệ';
    isValid = false;
  }

  if (!form.password) {
    form.errors.password = 'Mật khẩu không được để trống';
    isValid = false;
  } else if (form.password.length < 6) {
    form.errors.password = 'Mật khẩu phải có ít nhất 6 ký tự';
    isValid = false;
  }

  return isValid;
};

const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }

  const result = await authStore.login({
    email: form.email,
    password: form.password,
  });

  if (result.success) {
    modal.showSuccess('Đăng nhập thành công!');
    setTimeout(() => {
      router.push({ name: 'home' });
    }, 1500);
  } else {
    // Hiển thị lỗi
    if (result.errors) {
      Object.entries(result.errors).forEach(([key, value]) => {
        if (Array.isArray(value)) {
          form.errors[key] = value[0];
        } else {
          form.errors[key] = value;
        }
      });
    } else {
      modal.showError(result.message || 'Đăng nhập thất bại');
    }
  }
};
</script>

<style scoped>
.login-page {
  display: flex;
  flex-direction: column;
}

.page-title {
  text-align: center;
  margin-bottom: 2rem;
  color: #212529;
  font-size: 1.8rem;
}

.auth-footer {
  text-align: center;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #dee2e6;
}

.auth-footer p {
  margin: 0;
  color: #6c757d;
}

.auth-link {
  color: #007bff;
  text-decoration: none;
  font-weight: 600;
}

.auth-link:hover {
  text-decoration: underline;
}
</style>
