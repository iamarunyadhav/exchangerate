<template>
  <RouterView />
</template>

<script>
import { onMounted } from 'vue';
import { useStore } from 'vuex';

export default {
  setup() {
    const store = useStore();
    
    onMounted(() => {
      // Initialize auth state when app starts
      if (localStorage.getItem('auth_token')) {
        store.dispatch('auth/initializeAuth');
      }
    });
  }
};
</script>