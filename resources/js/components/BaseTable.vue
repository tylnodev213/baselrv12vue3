<template>
  <div class="soft-card">
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th v-for="column in columns" :key="column.key" :style="{ width: column.width }">
              {{ column.label }}
            </th>
            <th v-if="showActions" style="width: 150px">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="rows.length === 0">
            <td :colspan="columns.length + (showActions ? 1 : 0)" class="text-center">
              Không có dữ liệu
            </td>
          </tr>
          <tr v-for="(row, index) in rows" :key="row.id || index">
            <td v-for="column in columns" :key="column.key">
              <slot :name="`cell-${column.key}`" :row="row" :column="column">
                {{ row[column.key] }}
              </slot>
            </td>
            <td v-if="showActions">
              <div class="actions">
                <button v-if="showEdit" class="btn-edit-text" @click="$emit('edit', row)">
                  EDIT
                </button>
                <button v-if="showDelete" class="btn-delete-text" @click="$emit('delete', row)">
                  DELETE
                </button>
                <slot name="actions" :row="row" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
defineProps({
  columns: {
    type: Array,
    required: true,
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
}

.table thead th {
  padding: 0.75rem 1rem;
  text-align: left;
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #adb5bd;
  opacity: 0.8;
  border-bottom: 1px solid #e9ecef;
  vertical-align: middle;
}

.table tbody td {
  padding: 1rem;
  border-bottom: 1px solid #e9ecef;
  font-size: 0.8125rem;
  color: #344767;
  vertical-align: middle;
}

.table tbody tr:last-child td {
  border-bottom: none;
}

.text-center {
  text-align: center;
  color: #adb5bd;
  padding: 3rem !important;
}

.actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  justify-content: flex-start;
  min-height: 24px;
}

.btn-edit-text {
  background: none;
  border: none;
  font-size: 0.7rem;
  font-weight: 700;
  color: var(--text-secondary);
  cursor: pointer;
  transition: var(--transition);
}

.btn-edit-text:hover {
  color: #cb0c9f;
}

.btn-delete-text {
  background: none;
  border: none;
  font-size: 0.7rem;
  font-weight: 700;
  color: #ea0606;
  cursor: pointer;
  transition: var(--transition);
}

.btn-delete-text:hover {
  color: #ff3545;
}
</style>
