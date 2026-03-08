<template>
  <div class="table-container">
    <table class="table">
      <thead>
        <tr>
          <th
            v-for="column in columns"
            :key="column.key"
            :style="{ width: column.width }"
          >
            {{ column.label }}
          </th>
          <th
            v-if="showActions"
            style="width: 150px"
          >
            Hành động
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="rows.length === 0">
          <td
            :colspan="columns.length + (showActions ? 1 : 0)"
            class="text-center"
          >
            Không có dữ liệu
          </td>
        </tr>
        <tr
          v-for="(row, index) in rows"
          :key="row.id || index"
        >
          <td
            v-for="column in columns"
            :key="column.key"
          >
            <slot
              :name="`cell-${column.key}`"
              :row="row"
              :column="column"
            >
              {{ row[column.key] }}
            </slot>
          </td>
          <td
            v-if="showActions"
            class="actions"
          >
            <button
              v-if="showEdit"
              class="btn-action btn-edit"
              title="Chỉnh sửa"
              @click="$emit('edit', row)"
            >
              ✎
            </button>
            <button
              v-if="showDelete"
              class="btn-action btn-delete"
              title="Xóa"
              @click="$emit('delete', row)"
            >
              ✕
            </button>
            <slot
              name="actions"
              :row="row"
            />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  columns: {
    type: Array,
    required: true,
    validator: (columns) => Array.isArray(columns) && columns.every((col) => col.key && col.label),
  },
  rows: {
    type: Array,
    default: () => [],
  },
  showActions: {
    type: Boolean,
    default: true,
  },
  showEdit: {
    type: Boolean,
    default: true,
  },
  showDelete: {
    type: Boolean,
    default: true,
  },
});

defineEmits(['edit', 'delete']);
</script>

<style scoped>
.table-container {
  width: 100%;
  overflow-x: auto;
}

.table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
}

.table thead {
  background-color: #f8f9fa;
}

.table thead th {
  padding: 0.75rem;
  text-align: left;
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
  color: #495057;
}

.table tbody td {
  padding: 0.75rem;
  border-bottom: 1px solid #dee2e6;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
}

.text-center {
  text-align: center;
  color: #6c757d;
  padding: 2rem !important;
}

.actions {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-start;
}

.btn-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  padding: 0;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 1.2rem;
  transition: all 0.2s ease-in-out;
  background-color: #e9ecef;
  color: #495057;
}

.btn-action:hover {
  background-color: #dee2e6;
}

.btn-edit {
  background-color: #e7f3ff;
  color: #007bff;
}

.btn-edit:hover {
  background-color: #d1e7ff;
  color: #0056b3;
}

.btn-delete {
  background-color: #ffe7e7;
  color: #dc3545;
}

.btn-delete:hover {
  background-color: #f8d7da;
  color: #bb2d2d;
}
</style>
