<template>
  <q-page class="row items-center justify-evenly">
    <div v-if="authStore.isAuthenticated" class="text-white">
      Welcome, {{ authStore.user?.name }}
    </div>
    <div v-if="!authStore.isAuthenticated" class="text-white">
      You are not logged in. Please login to continue. <br>
      Use this link to login: <q-btn @click="$router.push('/login')">Login Page</q-btn>
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from 'src/stores/auth-store';

// const router = useRouter();
// const $q = useQuasar();
const authStore = useAuthStore();

onMounted(() => {
  void authStore.initializeAuth();
});

</script>
