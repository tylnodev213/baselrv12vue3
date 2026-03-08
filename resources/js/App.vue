<template>
  <RouterView />
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/useAuthStore';

const authStore = useAuthStore();

// Kiểm tra authentication khi app load
onMounted(async () => {
  await authStore.checkAuth();
});
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html,
body,
#app {
  width: 100%;
  height: 100%;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
    'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  background-color: #f5f5f5;
  color: #333;
}

a {
  color: #007bff;
  text-decoration: none;
  cursor: pointer;
}

a:hover {
  color: #0056b3;
}

button {
  font-family: inherit;
}

input,
textarea,
select {
  font-family: inherit;
}
</style>
