<template>
  <div class="form-group">
    <label v-if="label" :for="id" class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="readonly"
      :required="required"
      class="form-control"
      :class="{ 'is-invalid': error }"
      @input="$emit('update:modelValue', $event.target.value)"
      @blur="$emit('blur')"
      @focus="$emit('focus')"
    />
    <div v-if="error" class="invalid-feedback d-block">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  label: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
  id: {
    type: String,
    default: () => `input-${Math.random().toString(36).substr(2, 9)}`,
  },
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus']);
</script>

<style scoped>
.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
}

.form-control {
  display: block;
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 1rem;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
  color: #495057;
  background-color: #fff;
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control.is-invalid {
  border-color: #dc3545;
}

.form-control.is-invalid:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
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

.text-danger {
  color: #dc3545;
}
</style>
