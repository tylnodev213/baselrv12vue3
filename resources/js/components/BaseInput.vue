<template>
  <div class="soft-form-group">
    <label v-if="label" :for="id" class="soft-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>
    <div class="input-wrapper">
      <input
        v-if="type !== 'textarea'"
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        class="soft-input"
        :class="{ 'is-invalid': error }"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur')"
        @focus="$emit('focus')"
      />
      <textarea
        v-else
        :id="id"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        class="soft-input textarea"
        :class="{ 'is-invalid': error }"
        rows="4"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur')"
        @focus="$emit('focus')"
      />
    </div>
    <div v-if="error" class="invalid-feedback d-block">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
defineProps({
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

defineEmits(['update:modelValue', 'blur', 'focus']);
</script>

<style scoped>
.soft-form-group {
  margin-bottom: 1rem;
}

.soft-label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 0.75rem;
  font-weight: 700;
  color: #344767;
  margin-left: 0.25rem;
}

.soft-input {
  display: block;
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 400;
  line-height: 1.4rem;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #d2d6da;
  appearance: none;
  border-radius: 0.5rem;
  transition: all 0.15s ease-in-out;
  outline: none;
}

.soft-input:focus {
  color: #495057;
  background-color: #fff;
  border-color: #cb0c9f;
  outline: 0;
  box-shadow: 0 0 0 2px rgba(203, 12, 159, 0.2);
}

.soft-input.is-invalid {
  border-color: #fd5c70;
}

.soft-input.is-invalid:focus {
  box-shadow: 0 0 0 2px rgba(253, 92, 112, 0.2);
}

.textarea {
  min-height: 100px;
  resize: vertical;
}

.invalid-feedback {
  margin-top: 0.25rem;
  font-size: 0.75rem;
  color: #fd5c70;
  margin-left: 0.25rem;
}

.d-block {
  display: block;
}

.text-danger {
  color: #fd5c70;
}
</style>
