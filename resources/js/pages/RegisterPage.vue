<template>
  <div class="register-page">
    <h2 class="page-title">
      Đăng ký
    </h2>

    <BaseForm
      ref="formRef"
      :submit-text="'Đăng ký'"
      :submit-loading-text="'Đang đăng ký...'"
      :show-cancel="false"
      :on-submit="handleSubmit"
      :requires-confirmation="false"
    >
      <BaseInput
        v-model="form.name"
        type="text"
        label="Tên đầy đủ"
        placeholder="Nhập tên của bạn"
        :error="form.errors.name"
        required
        @blur="handleFieldTouched('name')"
      />

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
        placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
        :error="form.errors.password"
        required
        @blur="handleFieldTouched('password')"
      />

      <BaseInput
        v-model="form.password_confirmation"
        type="password"
        label="Xác nhận mật khẩu"
        placeholder="Xác nhận mật khẩu"
        :error="form.errors.password_confirmation"
        required
        @blur="handleFieldTouched('password_confirmation')"
      />
    </BaseForm>

    <div class="auth-footer">
      <p>
        Đã có tài khoản? <router-link
          to="/auth/login"
          class="auth-link"
        >
          Đăng nhập
        </router-link>
      </p>
    </div>

    <BaseModal
      :is-open="isOpen"
      :title="title"
      :message="message"
      :type="type"
      :is-loading="isLoading"
      @confirm="confirm"
      @cancel="cancel"
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
const { isOpen, title, message, type, isLoading, confirm, cancel, showSuccess, showError } = useModal();
const formRef = ref(null);

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
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

  if (!form.name) {
    form.errors.name = 'Tên không được để trống';
    isValid = false;
  } else if (form.name.length < 3) {
    form.errors.name = 'Tên phải có ít nhất 3 ký tự';
    isValid = false;
  }

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

  if (!form.password_confirmation) {
    form.errors.password_confirmation = 'Xác nhận mật khẩu không được để trống';
    isValid = false;
  } else if (form.password !== form.password_confirmation) {
    form.errors.password_confirmation = 'Mật khẩu không khớp';
    isValid = false;
  }

  return isValid;
};

const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }

  const result = await authStore.register({
    name: form.name,
    email: form.email,
    password: form.password,
    password_confirmation: form.password_confirmation,
  });

  if (result.success) {
    showSuccess('Đăng ký thành công!');
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
      showError(result.message || 'Đăng ký thất bại');
    }
  }
};
</script>

<style scoped>
.register-page {
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
