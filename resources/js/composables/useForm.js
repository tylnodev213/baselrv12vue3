// resources/js/composables/useForm.js
import { ref, reactive, computed } from 'vue';

/**
 * Composable cho form handling
 */
export const useForm = () => {
  const isLoading = ref(false);
  const isSubmitting = ref(false);
  const formData = reactive({});
  const errors = reactive({});
  const touched = reactive({});

  /**
   * Set form data
   */
  const setFormData = (data) => {
    Object.assign(formData, data);
  };

  /**
   * Reset form
   */
  const resetForm = () => {
    Object.keys(formData).forEach(key => delete formData[key]);
    Object.keys(errors).forEach(key => delete errors[key]);
    Object.keys(touched).forEach(key => delete touched[key]);
  };

  /**
   * Set field value
   */
  const setFieldValue = (fieldName, value) => {
    formData[fieldName] = value;
  };

  /**
   * Set field error
   */
  const setFieldError = (fieldName, error) => {
    if (error) {
      errors[fieldName] = error;
    } else {
      delete errors[fieldName];
    }
  };

  /**
   * Set field touched
   */
  const setFieldTouched = (fieldName, isTouched = true) => {
    touched[fieldName] = isTouched;
  };

  /**
   * Clear field error
   */
  const clearFieldError = (fieldName) => {
    delete errors[fieldName];
  };

  /**
   * Clear all errors
   */
  const clearErrors = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
  };

  /**
   * Set multiple errors
   */
  const setErrors = (errorMap) => {
    clearErrors();
    Object.entries(errorMap).forEach(([key, error]) => {
      setFieldError(key, error);
    });
  };

  /**
   * Validate form
   */
  const validateForm = (validationRules) => {
    clearErrors();
    let isValid = true;

    Object.entries(validationRules).forEach(([fieldName, rules]) => {
      const value = formData[fieldName];
      const fieldErrors = [];

      if (Array.isArray(rules)) {
        rules.forEach(rule => {
          const error = rule(value, formData);
          if (error) {
            fieldErrors.push(error);
          }
        });
      }

      if (fieldErrors.length > 0) {
        setFieldError(fieldName, fieldErrors[0]);
        isValid = false;
      }
    });

    return isValid;
  };

  /**
   * Get field value
   */
  const getFieldValue = (fieldName) => {
    return formData[fieldName];
  };

  /**
   * Get field error
   */
  const getFieldError = (fieldName) => {
    return errors[fieldName];
  };

  /**
   * Check if field has error
   */
  const hasFieldError = (fieldName) => {
    return !!errors[fieldName];
  };

  /**
   * Computed: has any error
   */
  const hasErrors = computed(() => {
    return Object.keys(errors).length > 0;
  });

  return {
    isLoading,
    isSubmitting,
    formData,
    errors,
    touched,
    setFormData,
    resetForm,
    setFieldValue,
    setFieldError,
    setFieldTouched,
    clearFieldError,
    clearErrors,
    setErrors,
    validateForm,
    getFieldValue,
    getFieldError,
    hasFieldError,
    hasErrors,
  };
};
