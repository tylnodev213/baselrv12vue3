<template>
  <div class="product-page">
    <div class="page-header">
      <h1>Quản lý Sản phẩm</h1>
      <button class="btn btn-primary" @click="handleNew">+ Thêm sản phẩm</button>
    </div>

    <div v-if="productStore.isLoading" class="loading">
      <div class="spinner" />
      <p>Đang tải...</p>
    </div>

    <div v-else class="content">
      <BaseTable
        :columns="columns"
        :rows="productStore.products"
        :show-actions="true"
        :show-edit="true"
        :show-delete="true"
        @edit="handleEdit"
        @delete="handleDelete"
      >
        <template #cell-price="{ row }">
          {{ formatPrice(row.price) }}
        </template>
      </BaseTable>

      <div class="pagination">
        <button
          v-if="productStore.currentPage > 1"
          class="btn btn-secondary"
          @click="handlePreviousPage"
        >
          Trang trước
        </button>
        <span class="page-info">
          Trang {{ productStore.currentPage }} / {{ productStore.totalPages }}
        </span>
        <button
          v-if="productStore.currentPage < productStore.totalPages"
          class="btn btn-secondary"
          @click="handleNextPage"
        >
          Trang sau
        </button>
      </div>
    </div>

    <!-- Product Form Modal -->
    <BaseModal
      v-model="isFormOpen"
      :title="isEditing ? 'Chỉnh sửa sản phẩm' : 'Thêm mới sản phẩm'"
      hide-footer
    >
      <BaseForm
        :submit-text="isEditing ? 'Cập nhật' : 'Tạo mới'"
        @submit="handleFormSubmit"
        @cancel="closeForm"
      >
        <BaseInput
          v-model="form.name"
          type="text"
          label="Tên sản phẩm"
          placeholder="Nhập tên sản phẩm"
          :error="form.errors.name"
          required
        />

        <BaseInput
          v-model="form.price"
          type="number"
          label="Giá"
          placeholder="Nhập giá"
          :error="form.errors.price"
          required
        />

        <div class="soft-form-group mb-4">
          <label class="soft-label">Mô tả</label>
          <textarea
            v-model="form.description"
            class="soft-textarea"
            :class="{ 'is-invalid': form.errors.description }"
            placeholder="Nhập mô tả sản phẩm"
            rows="4"
          ></textarea>
          <div v-if="form.errors.description" class="text-danger text-xs mt-1">
            {{ form.errors.description }}
          </div>
        </div>
      </BaseForm>
    </BaseModal>

    <!-- Modal confirm delete -->
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
import { reactive, ref, onMounted } from 'vue';
import { useProductStore } from '@/stores/useProductStore';
import { useModal } from '@/composables/useModal';
import BaseForm from '@/components/BaseForm.vue';
import BaseInput from '@/components/BaseInput.vue';
import BaseTable from '@/components/BaseTable.vue';
import BaseModal from '@/components/BaseModal.vue';

const productStore = useProductStore();
const {
  isOpen,
  title,
  message,
  type,
  isLoading,
  confirm,
  cancel,
  showConfirm,
  showSuccess,
  showError,
} = useModal();

const columns = [
  { key: 'id', label: 'ID', width: '60px' },
  { key: 'name', label: 'Tên sản phẩm' },
  { key: 'price', label: 'Giá' },
  { key: 'description', label: 'Mô tả' },
];

const isFormOpen = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

const form = reactive({
  name: '',
  price: '',
  description: '',
  errors: {},
});

const openForm = () => {
  isFormOpen.value = true;
};

const closeForm = () => {
  isFormOpen.value = false;
  isEditing.value = false;
  resetForm();
};

const resetForm = () => {
  currentId.value = null;
  form.name = '';
  form.price = '';
  form.description = '';
  form.errors = {};
};

const handleFieldTouched = (fieldName) => {
  if (form.errors[fieldName]) {
    form.errors[fieldName] = '';
  }
};

const validateForm = () => {
  form.errors = {};
  let isValid = true;

  if (!form.name) {
    form.errors.name = 'Tên sản phẩm không được để trống';
    isValid = false;
  }

  if (!form.price) {
    form.errors.price = 'Giá không được để trống';
    isValid = false;
  } else if (parseFloat(form.price) <= 0) {
    form.errors.price = 'Giá phải lớn hơn 0';
    isValid = false;
  }

  return isValid;
};

const handleNew = () => {
  isEditing.value = false;
  resetForm();
  openForm();
};

const handleEdit = (product) => {
  isEditing.value = true;
  currentId.value = product.id;
  form.name = product.name;
  form.price = product.price;
  form.description = product.description || '';
  form.errors = {};
  openForm();
};

const handleDelete = (product) => {
  showConfirm(`Bạn có chắc muốn xóa sản phẩm "${product.name}"?`, async () => {
    const result = await productStore.deleteProduct(product.id);
    if (result.success) {
      showSuccess('Xóa sản phẩm thành công!');
    } else {
      showError(result.message || 'Xóa sản phẩm thất bại');
    }
  });
};

const handleFormSubmit = async () => {
  if (!validateForm()) {
    return;
  }

  const payload = {
    name: form.name,
    price: parseFloat(form.price),
    description: form.description,
  };

  let result;
  if (isEditing.value) {
    result = await productStore.updateProduct(currentId.value, payload);
  } else {
    result = await productStore.createProduct(payload);
  }

  if (result.success) {
    showSuccess(isEditing.value ? 'Cập nhật thành công!' : 'Tạo mới thành công!');
    closeForm();
  } else {
    if (result.errors) {
      Object.entries(result.errors).forEach(([key, value]) => {
        if (Array.isArray(value)) {
          form.errors[key] = value[0];
        } else {
          form.errors[key] = value;
        }
      });
    } else {
      showError(result.message || 'Xảy ra lỗi');
    }
  }
};

const handlePreviousPage = () => {
  if (productStore.currentPage > 1) {
    productStore.fetchProducts(productStore.currentPage - 1);
  }
};

const handleNextPage = () => {
  if (productStore.currentPage < productStore.totalPages) {
    productStore.fetchProducts(productStore.currentPage + 1);
  }
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(price);
};

onMounted(() => {
  productStore.fetchProducts();
});
</script>

<style scoped>
.product-page {
  display: flex;
  flex-direction: column;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
}

.page-header h1 {
  margin: 0;
  color: #212529;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #545b62;
}

.loading {
  text-align: center;
  padding: 2rem;
}

.spinner {
  width: 3rem;
  height: 3rem;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #dee2e6;
}

.page-info {
  color: #6c757d;
  font-weight: 500;
}

</style>
