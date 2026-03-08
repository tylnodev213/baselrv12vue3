<template>
  <div class="product-page">
    <div class="page-header">
      <h1>Quản lý Sản phẩm</h1>
      <button class="btn btn-primary" @click="handleNew">
        + Thêm sản phẩm
      </button>
    </div>

    <div v-if="productStore.isLoading" class="loading">
      <div class="spinner"></div>
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
          @click="handlePreviousPage"
          class="btn btn-secondary"
        >
          Trang trước
        </button>
        <span class="page-info">
          Trang {{ productStore.currentPage }} / {{ productStore.totalPages }}
        </span>
        <button
          v-if="productStore.currentPage < productStore.totalPages"
          @click="handleNextPage"
          class="btn btn-secondary"
        >
          Trang sau
        </button>
      </div>
    </div>

    <!-- Product Form Modal -->
    <div v-if="isFormOpen" class="modal-overlay" @click.self="closeForm">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Chỉnh sửa' : 'Thêm mới' }} sản phẩm</h2>
          <button class="btn-close" @click="closeForm">✕</button>
        </div>

        <div class="modal-body">
          <BaseForm
            :submit-text="isEditing ? 'Cập nhật' : 'Tạo mới'"
            :submit-loading-text="'Đang xử lý...'"
            :on-submit="handleFormSubmit"
            :show-cancel="true"
            @cancel="closeForm"
          >
            <BaseInput
              v-model="form.name"
              type="text"
              label="Tên sản phẩm"
              placeholder="Nhập tên sản phẩm"
              :error="form.errors.name"
              required
              @blur="handleFieldTouched('name')"
            />

            <BaseInput
              v-model="form.price"
              type="number"
              label="Giá"
              placeholder="Nhập giá"
              :error="form.errors.price"
              required
              @blur="handleFieldTouched('price')"
            />

            <div class="form-group">
              <label class="form-label">Mô tả</label>
              <textarea
                v-model="form.description"
                class="form-textarea"
                :class="{ 'is-invalid': form.errors.description }"
                placeholder="Nhập mô tả sản phẩm"
                rows="4"
              ></textarea>
              <div v-if="form.errors.description" class="invalid-feedback d-block">
                {{ form.errors.description }}
              </div>
            </div>
          </BaseForm>
        </div>
      </div>
    </div>

    <!-- Modal confirm delete -->
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
import { reactive, ref, onMounted } from 'vue';
import { useProductStore } from '@/stores/useProductStore';
import { useModal } from '@/composables/useModal';
import BaseForm from '@/components/BaseForm.vue';
import BaseInput from '@/components/BaseInput.vue';
import BaseTable from '@/components/BaseTable.vue';
import BaseModal from '@/components/BaseModal.vue';

const productStore = useProductStore();
const modal = useModal();

const columns = [
  { key: 'id', label: 'ID', width: '60px' },
  { key: 'name', label: 'Tên sản phẩm' },
  { key: 'price', label: 'Giá' },
  { key: 'description', label: 'Mô tả' },
];

const isFormOpen = ref(false);
const isEditing = ref(false);

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
  form.name = product.name;
  form.price = product.price;
  form.description = product.description || '';
  form.errors = {};
  openForm();
};

const handleDelete = (product) => {
  modal.showConfirm(`Bạn có chắc muốn xóa sản phẩm "${product.name}"?`, async () => {
    const result = await productStore.deleteProduct(product.id);
    if (result.success) {
      modal.showSuccess('Xóa sản phẩm thành công!');
    } else {
      modal.showError(result.message || 'Xóa sản phẩm thất bại');
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
    result = await productStore.updateProduct(
      productStore.currentProduct?.id,
      payload
    );
  } else {
    result = await productStore.createProduct(payload);
  }

  if (result.success) {
    modal.showSuccess(
      isEditing.value ? 'Cập nhật thành công!' : 'Tạo mới thành công!'
    );
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
      modal.showError(result.message || 'Xảy ra lỗi');
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

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #dee2e6;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #212529;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6c757d;
  cursor: pointer;
  padding: 0;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}

.btn-close:hover {
  color: #212529;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
}

.form-textarea {
  display: block;
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 1rem;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  font-family: inherit;
  resize: vertical;
}

.form-textarea:focus {
  color: #495057;
  background-color: #fff;
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-textarea.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  display: none;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #dc3545;
}

.invalid-feedback.d-block {
  display: block;
}
</style>
